<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AboutPageContent extends Model
{
    protected $fillable = [
        'breadcrumb_title',
        'cover_image',
        'section1_subtitle',
        'section1_title',
        'section1_paragraph',
        'section1_image',
        'section2_subtitle',
        'section2_title',
        'section2_paragraph',
        'section2_image',
        'more_about_heading_title',
        'more_about_heading_description',
        'point1_title',
        'point1_description',
        'point2_title',
        'point2_description',
        'point3_title',
        'point3_description',
    ];
}
