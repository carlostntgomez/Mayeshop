<?php

namespace App\Observers;

use App\Models\FontSetting;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class FontSettingObserver
{
    /**
     * Handle the FontSetting "saved" event.
     */
    public function saved(FontSetting $fontSetting): void
    {
        $primaryFontName = $fontSetting->primary_font_name ?? 'Jost';
        $secondaryFontName = $fontSetting->secondary_font_name ?? 'Roboto';

        $availableFonts = config('fonts.available_fonts');

        $primaryFontDetails = collect($availableFonts)->firstWhere('name', $primaryFontName);
        $secondaryFontDetails = collect($availableFonts)->firstWhere('name', $secondaryFontName);

        $primaryFontStack = '"Jost", sans-serif';
        $primaryFontUrl = 'https://fonts.googleapis.com/css2?family=Jost:wght@400;700&display=swap';

        if (is_array($primaryFontDetails) && isset($primaryFontDetails['stack']) && is_string($primaryFontDetails['stack'])) {
            $primaryFontStack = $primaryFontDetails['stack'];
        }
        if (is_array($primaryFontDetails) && isset($primaryFontDetails['url']) && is_string($primaryFontDetails['url']) && Str::startsWith($primaryFontDetails['url'], 'http')) {
            $primaryFontUrl = $primaryFontDetails['url'];
        }

        $secondaryFontStack = '"Roboto", sans-serif';
        $secondaryFontUrl = 'https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap';

        if (is_array($secondaryFontDetails) && isset($secondaryFontDetails['stack']) && is_string($secondaryFontDetails['stack'])) {
            $secondaryFontStack = $secondaryFontDetails['stack'];
        }
        if (is_array($secondaryFontDetails) && isset($secondaryFontDetails['url']) && is_string($secondaryFontDetails['url']) && Str::startsWith($secondaryFontDetails['url'], 'http')) {
            $secondaryFontUrl = $secondaryFontDetails['url'];
        }

        $cssFilePath = public_path('static/css/style.css');
        $cssContent = File::get($cssFilePath);

        // 1. Remove all existing Google Fonts @import rules and css2.css
        $cssContent = preg_replace(
            '/^@import url\(\'https:\/\/fonts\.googleapis\.com\/css2\?family=.*?'.*?\);\n/m',
            '',
            $cssContent
        );
        $cssContent = preg_replace('/^@import url\(\"css2\.css\"\);\n/m', '', $cssContent);


        // 2. Add new Google Fonts @import rules
        $newImportRules = '';
        if (!empty($primaryFontUrl)) {
            $newImportRules .= "@import url('{$primaryFontUrl}');\n";
        }
        if (!empty($secondaryFontUrl) && $primaryFontUrl !== $secondaryFontUrl) {
            $newImportRules .= "@import url('{$secondaryFontUrl}');\n";
        }

        // Prepend new import rules to the CSS content
        $cssContent = $newImportRules . $cssContent;


        // 3. Update :root CSS variables
        $newRootVars = <<<'CSS'
:root {
  --black: #000;
  --white: #fff;
  --ul-primary: #EF2853;
  --ul-secondary: #FFA31A;
  --ul-gradient: linear-gradient(90deg, var(--ul-primary) 0%, var(--ul-secondary) 100%);
  --font-primary: {$primaryFontStack};
  --font-secondary: {$secondaryFontStack};
}
CSS;
        // Regex to find the existing :root block and replace font-primary and font-secondary
        $cssContent = preg_replace(
            '/:root\s*\{\s*--black:.*?--font-primary:.*;(\s*--font-secondary:.*;)?\s*\}/s',
            $newRootVars,
            $cssContent
        );

        File::put($cssFilePath, $cssContent);
    }

    /**
     * Handle the FontSetting "created" event.
     */
    public function created(FontSetting $fontSetting): void
    {
        $this->saved($fontSetting);
    }

    /**
     * Handle the FontSetting "updated" event.
     */
    public function updated(FontSetting $fontSetting): void
    {
        $this->saved($fontSetting);
    }

    /**
     * Handle the FontSetting "deleted" event.
     */
    public function deleted(FontSetting $fontSetting): void
    {
        // Optionally revert to default fonts or handle deletion
        // For now, we'll just let the last saved state persist
    }

    /**
     * Handle the FontSetting "restored" event.
     */
    public function restored(FontSetting $fontSetting): void
    {
        $this->saved($fontSetting);
    }

    /**
     * Handle the FontSetting "force deleted" event.
     */
    public function forceDeleted(FontSetting $fontSetting): void
    {
        // 
    }
}