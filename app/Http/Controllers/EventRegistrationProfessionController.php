<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Conference;
use App\Models\Event;
use App\Models\Profession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class EventRegistrationProfessionController extends MyBaseController
{
    /**
     * Show the 'Edit Profession' modal
     *
     * @param Request $request
     * @param int $event_id
     * @param int $profession_id
     * @return View
     */
    public function showEditProfession(Request $request, $event_id, $profession_id)
    {
        $profession = Profession::findOrFail($profession_id);
        $event = Event::find($event_id);

        $data = [
            'profession' => $profession,
            'event' => $event,
        ];

        return view('ManageEvent.Modals.EditProfession', $data);
    }

    /**
     * Get active conferences for a category
     *
     * @param int $category_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCategoryConferences($category_id)
    {
        try {
            $category = Category::findOrFail($category_id);

            // Get only active conferences
            $conferences = $category
                ->conferences()
                ->where('status', 'active')
                ->orderBy('created_at')
                ->get()
                ->map(function ($conference) use ($category_id) {
                    $conference->price = $conference->getPriceForCategory($category_id);
                    return $conference;
                });

            return response()->json([
                'conferences' => $conferences,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'conferences' => []
            ], 400);
        }
    }

    /**
     * Get active professions for a conference
     *
     * @param int $conference_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getConferenceProfessions($conference_id)
    {
        try {
            $conference = Conference::findOrFail($conference_id);

            // Get only active professions
            $professions = $conference
                ->professions()
                // ->where('status', 'active')
                ->orderBy('name')
                ->get();

            return response()->json([
                'professions' => $professions,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'professions' => []
            ], 400);
        }
    }

    /**
     * Get price for category and conference combination
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRegistrationPrice(Request $request)
    {
        try {
            $categoryId = $request->get('category_id');
            $conferenceId = $request->get('conference_id');

            if (!$categoryId || !$conferenceId) {
                return response()->json([
                    'price' => 0,
                    'error' => 'Category ID and Conference ID are required'
                ], 400);
            }

            $conference = Conference::findOrFail($conferenceId);
            $price = $conference->getPriceForCategory($categoryId);

            return response()->json([
                'price' => (float) $price,
                'formatted_price' => number_format($price, 2)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'price' => 0,
                'error' => $e->getMessage()
            ], 400);
        }
    }
}
