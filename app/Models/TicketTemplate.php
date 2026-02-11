<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketTemplate extends Model
{
    protected $table = 'ticket_templates';

    protected $fillable = [
        'event_id',
        'background_image_path',
        'name_position_x',
        'name_position_y',
        'name_font_size',
        'name_font_color',
        'code_position_x',
        'code_position_y',
        'code_font_size',
        'code_font_color',
        'qr_position_x',
        'qr_position_y',
        'qr_size',
        'show_user_type',
        'user_type_position_x',
        'user_type_position_y',
        'user_type_font_size',
        'user_type_font_color',
        'show_profession',
        'profession_position_x',
        'profession_position_y',
        'profession_font_size',
        'profession_font_color',
        'show_category',
        'category_position_x',
        'category_position_y',
        'category_font_size',
        'category_font_color',
        'preview_width',
        'preview_height',
    ];

    protected $casts = [
        'show_user_type' => 'boolean',
        'show_profession' => 'boolean',
        'show_category' => 'boolean',
    ];

    /**
     * Get the event that owns the ticket template.
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}