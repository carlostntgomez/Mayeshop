<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class ColorSetting extends Model
{
    protected $fillable = ['name', 'value'];

    protected static function booted()
    {
        static::saved(function (ColorSetting $colorSetting) {
            $colorSetting->updateCssFile();
        });

        static::deleted(function (ColorSetting $colorSetting) {
            $colorSetting->removeCssFromFile();
        });
    }

    public function updateCssFile()
    {
        $path = public_path('static/css/style.css');
        $content = File::get($path);

        // Pattern to find the CSS variable definition
        $pattern = '/(--' . preg_quote(str_replace('--', '', $this->name), '/') . ':\s*[^;]+;)/';

        if (preg_match($pattern, $content)) {
            // If the variable exists, replace its value
            $content = preg_replace($pattern, $this->name . ': ' . $this->value . ';', $content);
        } else {
            // If the variable doesn't exist, add it to the :root selector
            // This assumes :root is always present and at the beginning of the file
            $content = preg_replace(
                '/:root\s*{',
                ":root {\n  " . $this->name . ': ' . $this->value . ';',
                $content,
                1 // Only replace the first occurrence of :root
            );
        }

        File::put($path, $content);
    }

    public function removeCssFromFile()
    {
        $path = public_path('static/css/style.css');
        $content = File::get($path);

        // Pattern to find and remove the CSS variable definition
        $pattern = '/\s*' . preg_quote($this->name, '/') . ':\s*[^;]+;\n/';
        $content = preg_replace($pattern, '', $content);

        File::put($path, $content);
    }
}
