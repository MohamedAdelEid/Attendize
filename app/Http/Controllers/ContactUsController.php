<?php

namespace App\Http\Controllers;

use App\Models\ContactUs;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ContactUsController extends Controller
{
    public function index(Event $event)
    {
        $contactMessages = ContactUs::where('event_id', $event->id)->get();
        return view('ManageEvent.contacts-us-messages', compact('event', 'contactMessages'));
    }

    public function postContactUs(Request $request, Event $event)
    {
        try {
            $request->validate(
                [
                    'name' => 'required',
                    'email' => 'required|email',
                    'subject' => 'nullable|string|max:255',
                    'message' => 'required|string|max:255',
                ],
                [
                    'name.required' => 'Please enter your name.',
                    'email.required' => 'Please enter your email.',
                    'email.email' => 'Please enter a valid email.',
                    'email.exists' => 'Email does not exist.',
                    'subject.string' => 'Subject must be a string.',
                    'subject.max' => 'Subject must be at most 255 characters.',
                    'message.required' => 'Please enter your message.',
                    'message.string' => 'Message must be a string.',
                    'message.max' => 'Message must be at most 255 characters.',
                ]
            );

            ContactUs::create([
                'event_id' => $event->id,
                'name' => $request->name,
                'email' => $request->email,
                'subject' => $request->subject,
                'message' => $request->message,
            ]);

            return redirect()->back()->with('success', 'Your message has been sent successfully.');
        } catch (\Exception $th) {
            dd($th);
            return redirect()->back()->with('error', 'Something went wrong. Please try again later.');
        }
    }

    public function deleteMessage(Request $request, Event $event, $message_id)
    {
        try {
            $message = ContactUs::findOrFail($message_id);
            $message->delete();
            return redirect()->back()->with('success', 'Message deleted successfully.');
        } catch (\Exception $th) {
            return redirect()->back()->with('error', 'Something went wrong. Please try again later.');
        }
    }

    public function deleteSelectedMessages(Request $request, Event $event)
    {
        try {
            $message_ids = $request->get('message_ids');
            ContactUs::whereIn('id', $message_ids)->delete();
            return redirect()->back()->with('success', 'Selected messages deleted successfully.');
        } catch (\Exception $th) {
            return redirect()->back()->with('error', 'Something went wrong. Please try again later.');
        }
    }
}
