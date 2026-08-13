<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Regression tests for the pre-deployment security audit.
 *
 * The admin panel is this application's only trust boundary, so every payload
 * below is one an admin (or anyone holding admin credentials) could store.
 * "The admin is trusted" stops being an answer once the payload runs in a
 * *visitor's* browser, or executes on the server.
 */
class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    private function service(array $attributes = []): Service
    {
        return Service::create(array_merge([
            'slug' => 'windows',
            'title' => ['fr' => 'Fenêtres'],
            'short_description' => ['fr' => 'Courte description'],
            'description' => ['fr' => 'Description'],
            'is_active' => true,
            'sort_order' => 1,
        ], $attributes));
    }

    // ------------------------------------------------- JSON-LD script breakout

    /**
     * The LocalBusiness schema is in the shared layout, so a breakout here
     * lands on every page of the site.
     */
    public function test_a_script_tag_in_a_site_setting_cannot_break_out_of_the_json_ld_block(): void
    {
        SiteSetting::set('contact_address', 'Sousse</script><script>alert(1)</script>');

        $html = $this->get(route('home'))->assertOk()->getContent();

        $this->assertStringNotContainsString('</script><script>alert(1)', $html);
    }

    public function test_a_script_tag_in_an_faq_cannot_break_out_of_the_contact_json_ld(): void
    {
        \App\Models\Faq::create([
            'question' => ['fr' => 'Question</script><script>alert(2)</script>'],
            'answer' => ['fr' => 'Réponse'],
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $html = $this->get(route('contact'))->assertOk()->getContent();

        $this->assertStringNotContainsString('</script><script>alert(2)', $html);
    }

    // ------------------------------------------------- unescaped admin HTML

    public function test_a_script_tag_in_the_svg_icon_field_is_not_rendered_to_visitors(): void
    {
        $this->service(['svg_icon' => '<svg><script>alert(3)</script></svg>']);

        $html = $this->get(route('services'))->assertOk()->getContent();

        $this->assertStringNotContainsString('<script>alert(3)', $html);
    }

    public function test_an_event_handler_in_the_svg_icon_field_is_stripped(): void
    {
        $this->service(['svg_icon' => '<svg onload="alert(4)"><circle cx="1" cy="1" r="1"/></svg>']);

        $html = $this->get(route('services'))->assertOk()->getContent();

        // Narrow to the injected attribute: the page legitimately contains
        // `preloader.onload` in an inline script.
        $this->assertStringNotContainsString('onload="alert(4)"', $html);
        $this->assertStringNotContainsString('alert(4)', $html);
    }

    public function test_a_legitimate_svg_icon_still_renders(): void
    {
        $this->service(['svg_icon' => '<svg viewBox="0 0 24 24"><path d="M12 2L2 7"/></svg>']);

        $html = $this->get(route('services'))->assertOk()->getContent();

        $this->assertStringContainsString('<svg', $html);
        $this->assertStringContainsString('<path', $html);
        // SVG attributes are case-sensitive: a sanitiser that parses as HTML
        // lower-cases this to `viewbox` and every icon silently loses scaling.
        $this->assertStringContainsString('viewBox="0 0 24 24"', $html);
        $this->assertStringContainsString('d="M12 2L2 7"', $html);
    }

    public function test_a_script_tag_in_the_rich_text_description_is_not_rendered(): void
    {
        $this->service(['description' => ['fr' => '<p>Bonjour</p><script>alert(5)</script>']]);

        $html = $this->get(route('services'))->assertOk()->getContent();

        $this->assertStringNotContainsString('<script>alert(5)', $html);
    }

    public function test_an_image_onerror_payload_in_the_description_is_stripped(): void
    {
        $this->service(['description' => ['fr' => '<img src=x onerror="alert(6)">']]);

        $html = $this->get(route('services'))->assertOk()->getContent();

        $this->assertStringNotContainsString('onerror="alert(6)"', $html);
        $this->assertStringNotContainsString('alert(6)', $html);
    }

    public function test_legitimate_rich_text_formatting_survives_sanitisation(): void
    {
        $this->service(['description' => ['fr' => '<p>Des <strong>fenêtres</strong> et une <em>pergola</em>.</p><ul><li>Alu</li></ul>']]);

        $html = $this->get(route('services'))->assertOk()->getContent();

        $this->assertStringContainsString('<strong>fenêtres</strong>', $html);
        $this->assertStringContainsString('<em>pergola</em>', $html);
        $this->assertStringContainsString('<li>Alu</li>', $html);
    }
}
