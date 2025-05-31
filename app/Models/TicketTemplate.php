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
    ];

    /**
     * Get the event that owns the ticket template.
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}