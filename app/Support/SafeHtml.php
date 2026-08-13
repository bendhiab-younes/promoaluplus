<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMXPath;
use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

/**
 * Sanitises the two admin-authored fields the public site renders as raw HTML:
 * a service's rich-text description and its pasted SVG icon.
 *
 * The admin panel is this application's only trust boundary, and "the admin is
 * trusted" stops being an answer once the payload executes in a *visitor's*
 * browser. Both fields previously went through `{!! !!}` untouched, so anyone
 * who reached the panel could store persistent XSS for every visitor.
 */
class SafeHtml
{
    private static ?HtmlSanitizer $richTextSanitizer = null;

    /**
     * Rich-text description, as produced by Filament's RichEditor. Symfony's
     * sanitiser is built for exactly this: an allowlist of formatting elements,
     * everything else dropped.
     */
    public static function richText(?string $html): string
    {
        $html = trim((string) $html);

        if ($html === '') {
            return '';
        }

        return trim(self::richTextSanitizer()->sanitize($html));
    }

    private static function richTextSanitizer(): HtmlSanitizer
    {
        if (self::$richTextSanitizer instanceof HtmlSanitizer) {
            return self::$richTextSanitizer;
        }

        $config = (new HtmlSanitizerConfig)
            ->allowLinkSchemes(['http', 'https', 'mailto', 'tel'])
            ->allowElement('a', ['href', 'title', 'target', 'rel'])
            ->allowElement('p')
            ->allowElement('br')
            ->allowElement('strong')
            ->allowElement('b')
            ->allowElement('em')
            ->allowElement('i')
            ->allowElement('u')
            ->allowElement('s')
            ->allowElement('ul')
            ->allowElement('ol')
            ->allowElement('li')
            ->allowElement('h2')
            ->allowElement('h3')
            ->allowElement('h4')
            ->allowElement('blockquote')
            ->allowElement('span')
            ->allowElement('div');

        return self::$richTextSanitizer = new HtmlSanitizer($config);
    }

    /**
     * A pasted SVG icon.
     *
     * Deliberately *not* Symfony's HtmlSanitizer: that parses as HTML, which
     * lower-cases attribute names, and SVG attributes are case-sensitive —
     * `viewBox` would become `viewbox` and every icon would lose its scaling.
     * Parsing as XML preserves the casing, so this walks the tree instead and
     * removes only what can execute: script-bearing elements, every `on*`
     * handler, and non-http(s) URL schemes.
     */
    public static function svgIcon(?string $svg): string
    {
        $svg = trim((string) $svg);

        if ($svg === '' || ! str_contains($svg, '<svg')) {
            return '';
        }

        $document = new DOMDocument;
        $previous = libxml_use_internal_errors(true);

        // LIBXML_NONET blocks network fetches; entity substitution is off by
        // default on PHP 8, so no XXE surface is opened here.
        $loaded = $document->loadXML($svg, LIBXML_NONET);

        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        if (! $loaded || $document->documentElement === null) {
            return '';
        }

        if (strtolower($document->documentElement->nodeName) !== 'svg') {
            return '';
        }

        self::scrubSvgTree($document);

        return trim((string) $document->saveXML($document->documentElement));
    }

    /**
     * Elements that can execute script or pull in foreign content, and are
     * never needed by a flat icon.
     *
     * @var list<string>
     */
    private const SVG_FORBIDDEN_ELEMENTS = [
        'script', 'foreignobject', 'iframe', 'embed', 'object', 'handler',
        'animate', 'set', 'use', 'image', 'audio', 'video', 'style',
    ];

    private static function scrubSvgTree(DOMDocument $document): void
    {
        $xpath = new DOMXPath($document);

        foreach (iterator_to_array($xpath->query('//*') ?: []) as $node) {
            if (! $node instanceof DOMElement) {
                continue;
            }

            $name = strtolower($node->localName ?? $node->nodeName);

            if (in_array($name, self::SVG_FORBIDDEN_ELEMENTS, true)) {
                $node->parentNode?->removeChild($node);

                continue;
            }

            foreach (iterator_to_array($node->attributes ?? []) as $attribute) {
                $attributeName = strtolower($attribute->nodeName);

                // Every scripting hook in SVG is an on* attribute.
                if (str_starts_with($attributeName, 'on')) {
                    $node->removeAttribute($attribute->nodeName);

                    continue;
                }

                if (in_array($attributeName, ['href', 'xlink:href', 'src'], true)
                    && ! preg_match('#^(https?:|/|\#|$)#i', trim($attribute->nodeValue ?? ''))) {
                    $node->removeAttribute($attribute->nodeName);
                }
            }
        }
    }
}
