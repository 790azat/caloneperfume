<?php

namespace App\Models\Concerns;

/**
 * Stores translatable attributes as JSON: {"hy": "...", "ru": "...", "en": "..."}.
 * Declare them in a `$translatable` property on the model.
 */
trait HasTranslations
{
    public function initializeHasTranslations(): void
    {
        foreach ($this->translatable as $attribute) {
            $this->mergeCasts([$attribute => 'array']);
        }
    }

    /** Value in the current locale, falling back to Russian, English, then any filled locale. */
    public function tr(string $attribute, ?string $locale = null): string
    {
        $values = array_filter((array) $this->getAttribute($attribute));
        $locale ??= app()->getLocale();

        return $values[$locale]
            ?? $values['ru']
            ?? $values['en']
            ?? (reset($values) ?: '');
    }
}
