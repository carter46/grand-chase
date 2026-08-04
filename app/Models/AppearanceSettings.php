<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppearanceSettings extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'primary_color',
        'primary_color_dark',
        'primary_color_light',
        'secondary_color',
        'secondary_color_dark',
        'secondary_color_light',
        'text_color',
        'bg_color',
        'sidebar_bg_color',
        'sidebar_text_color',
        'card_bg_color',
        'use_gradient',
        'gradient_direction',
        'custom_css',
        'disable_animations',
        'notes'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'use_gradient' => 'boolean',
        'disable_animations' => 'boolean',
    ];
} 