{{-- Emphasis for the "À propos de cette page" callout.

     Written as real CSS rather than utility classes because Filament ships a
     pre-compiled stylesheet: a class it does not already use would simply not
     exist. The --info-* custom properties, on the other hand, are emitted
     globally by the panel's colour config, so this tracks the theme.

     Selector specificity (.fi-admin-note .fi-section) beats the component's own
     bg-white / dark:bg-gray-900 utilities without needing !important. --}}
<style>
    .fi-admin-note {
        padding-top: 1.5rem;
    }

    .fi-admin-note .fi-section {
        background-color: rgb(var(--info-50));
        box-shadow: inset 0 0 0 1px rgba(var(--info-500), 0.25);
    }

    .fi-admin-note .fi-section-header-heading {
        color: rgb(var(--info-900));
    }

    .fi-admin-note .fi-section-header-description {
        color: rgba(var(--info-900), 0.7);
    }

    .dark .fi-admin-note .fi-section {
        background-color: rgba(var(--info-400), 0.08);
        box-shadow: inset 0 0 0 1px rgba(var(--info-400), 0.2);
    }

    .dark .fi-admin-note .fi-section-header-heading {
        color: rgb(var(--info-200));
    }

    .dark .fi-admin-note .fi-section-header-description {
        color: rgba(var(--info-200), 0.7);
    }
</style>
