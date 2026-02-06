<?php

namespace App\View\Composers;

use App\Models\ColorSetting;
use Illuminate\View\View;

class ThemeColorComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        $colorSettings = ColorSetting::all()->pluck('value', 'name');

        $view->with('themeColors', [
            '--black' => $colorSettings->get('--black', '#000'), // Default if not set
            '--white' => $colorSettings->get('--white', '#fff'), // Default if not set
            '--ul-primary' => $colorSettings->get('--ul-primary', '#EF2853'), // Default if not set
            '--ul-secondary' => $colorSettings->get('--ul-secondary', '#FFA31A'), // Default if not set
            '--ul-tertiary' => $colorSettings->get('--ul-tertiary', '#F8E6E2'), // Default if not set
            '--ul-text-gray' => $colorSettings->get('--ul-text-gray', '#676666'), // Default if not set
            // Add other color settings as needed
        ]);
    }
}
