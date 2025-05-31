<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\TicketTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TicketTemplateController extends MyBaseController
{
    /**
     * Show the ticket template settings page
     */
    public function showEventTicketTemplate(Request $request, $event_id)
    {
        $event = Event::scope()->findOrFail($event_id);
        $template = TicketTemplate::where('event_id', $event_id)->first();

        $data = [
            'event' => $event,
            'template' => $template,
            'page_title' => 'Ticket Template',
        ];

        return view('ManageEvent.TicketTemplate', $data);
    }

    /**
     * Update ticket template settings
     */
    public function postEditEventTicketTemplate(Request $request, $event_id)
    {
        $event = Event::scope()->findOrFail($event_id);
        $ticketTemplate = TicketTemplate::firstOrNew(['event_id' => $event->id]);

        $rules = [
            'background_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'], // Max 2MB
            'name_position_x' => ['nullable', 'string', 'max:10'],
            'name_position_y' => ['nullable', 'string', 'max:10'],
            'name_font_size' => ['nullable', 'string', 'max:10'],
            'name_font_color' => ['nullable', 'string', 'max:10'],
            'code_position_x' => ['nullable', 'string', 'max:10'],
            'code_position_y' => ['nullable', 'string', 'max:10'],
            'code_font_size' => ['nullable', 'string', 'max:10'],
            'code_font_color' => ['nullable', 'string', 'max:10'],
            'qr_position_x' => ['nullable', 'string', 'max:10'],
            'qr_position_y' => ['nullable', 'string', 'max:10'],
            'qr_size' => ['nullable', 'string', 'max:10'],
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'messages' => $validator->messages()->toArray(),
            ]);
        }

        if ($request->hasFile('background_image')) {
            // Delete old image if exists
            if ($ticketTemplate->background_image_path && Storage::disk('public')->exists($ticketTemplate->background_image_path)) {
                Storage::disk('public')->delete($ticketTemplate->background_image_path);
            }
            $path = $request->file('background_image')->store('ticket_backgrounds', 'public');
            $ticketTemplate->background_image_path = $path;
        }

        $ticketTemplate->fill($request->only([
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
            'qr_size'
        ]));

        $ticketTemplate->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Ticket Template Settings Updated Successfully.',
        ]);
    }

    /**
     * Save element positions and settings
     */
    public function savePositions(Request $request, $event_id)
    {
        $validator = Validator::make($request->all(), [
            'name_position_x' => 'required|numeric',
            'name_position_y' => 'required|numeric',
            'code_position_x' => 'required|numeric',
            'code_position_y' => 'required|numeric',
            'qr_position_x' => 'required|numeric',
            'qr_position_y' => 'required|numeric',
            'name_font_size' => 'nullable|numeric|min:8|max:72',
            'name_font_color' => 'nullable|string',
            'code_font_size' => 'nullable|numeric|min:8|max:72',
            'code_font_color' => 'nullable|string',
            'qr_size' => 'nullable|numeric|min:50|max:300',
            'preview_width' => 'nullable|numeric',
            'preview_height' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'messages' => $validator->errors()
            ]);
        }

        try {
            $template = TicketTemplate::firstOrNew(['event_id' => $event_id]);

            // Save positions
            $template->name_position_x = $request->name_position_x;
            $template->name_position_y = $request->name_position_y;
            $template->code_position_x = $request->code_position_x;
            $template->code_position_y = $request->code_position_y;
            $template->qr_position_x = $request->qr_position_x;
            $template->qr_position_y = $request->qr_position_y;

            // Save font settings
            $template->name_font_size = $request->name_font_size ?? 24;
            $template->name_font_color = $request->name_font_color ?? '#000000';
            $template->code_font_size = $request->code_font_size ?? 20;
            $template->code_font_color = $request->code_font_color ?? '#000000';
            $template->qr_size = $request->qr_size ?? 100;

            // Save preview dimensions for scaling
            if ($request->has('preview_width') && $request->has('preview_height')) {
                $template->preview_width = $request->preview_width;
                $template->preview_height = $request->preview_height;
            }

            $template->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Template settings saved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to save settings: ' . $e->getMessage()
            ]);
        }
    }
}
