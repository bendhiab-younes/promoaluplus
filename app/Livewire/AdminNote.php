<?php

namespace App\Livewire;

use App\Support\AdminNotes;
use Illuminate\Contracts\View\View;
use Livewire\Component;

/**
 * The "À propos de cette page" callout pinned above each admin section.
 *
 * Rendered through a single unscoped render hook in AdminPanelProvider, which
 * hands it the key resolved from the current page. The language toggle writes
 * to the signed-in admin rather than the session, so a colleague who reads
 * Arabic sets it once and finds it that way at the next login.
 */
class AdminNote extends Component
{
    public string $noteKey;

    public string $locale = AdminNotes::DEFAULT_LOCALE;

    public function mount(string $noteKey): void
    {
        $this->noteKey = $noteKey;
        $this->locale = AdminNotes::normalizeLocale(auth()->user()?->admin_note_locale);
    }

    public function switchLocale(string $locale): void
    {
        if (! AdminNotes::isSupportedLocale($locale)) {
            return;
        }

        $this->locale = $locale;

        auth()->user()?->forceFill(['admin_note_locale' => $locale])->save();
    }

    public function render(): View
    {
        return view('livewire.admin-note', [
            'heading' => AdminNotes::heading($this->noteKey, $this->locale),
            'paragraphs' => AdminNotes::body($this->noteKey, $this->locale),
            'title' => __('admin_notes.panel.title', [], $this->locale),
            'languageLabel' => __('admin_notes.panel.language', [], $this->locale),
            'isRtl' => AdminNotes::isRightToLeft($this->locale),
            'locales' => AdminNotes::LOCALES,
        ]);
    }
}
