<?php

namespace App\Http\Controllers;

use App\Models\AbstractCategory;
use App\Models\Event;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class EventAbstractCategoryController extends MyBaseController
{
    public function showCategories(Request $request, $event_id)
    {
        return redirect()->route('showEventAbstracts', ['event_id' => $event_id, 'tab' => 'categories']);
    }

    public function showCreateCategory($event_id)
    {
        return view('ManageEvent.Modals.CreateAbstractCategory', [
            'event' => Event::scope()->findOrFail($event_id),
        ]);
    }

    public function postCreateCategory(Request $request, $event_id)
    {
        Event::scope()->findOrFail($event_id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'description' => 'nullable|string|max:2000',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'messages' => $validator->errors(),
            ]);
        }

        DB::beginTransaction();
        try {
            $slug = Str::slug($request->get('name')) ?: 'category';
            $base = $slug;
            $i = 1;
            while (AbstractCategory::where('event_id', $event_id)->where('slug', $slug)->exists()) {
                $slug = $base . '-' . $i++;
            }

            $category = new AbstractCategory();
            $category->event_id = $event_id;
            $category->name = $request->get('name');
            $category->slug = $slug;
            $category->description = $request->get('description');
            $category->sort_order = (int) $request->get('sort_order', 0);
            $category->is_active = $request->boolean('is_active', true);
            $category->save();

            DB::commit();

            session()->flash('message', trans('Abstract.category_created'));

            return response()->json([
                'status' => 'success',
                'id' => $category->id,
                'message' => trans('Controllers.refreshing'),
                'redirectUrl' => route('showEventAbstracts', ['event_id' => $event_id, 'tab' => 'categories']),
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong! Please try again.',
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function showEditCategory(Request $request, $event_id, $category_id)
    {
        $event = Event::scope()->findOrFail($event_id);
        $category = AbstractCategory::where('event_id', $event_id)->findOrFail($category_id);

        return view('ManageEvent.Modals.EditAbstractCategory', [
            'event' => $event,
            'category' => $category,
        ]);
    }

    public function postEditCategory(Request $request, $event_id, $category_id)
    {
        Event::scope()->findOrFail($event_id);
        $category = AbstractCategory::where('event_id', $event_id)->findOrFail($category_id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'description' => 'nullable|string|max:2000',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'messages' => $validator->errors(),
            ]);
        }

        DB::beginTransaction();
        try {
            $category->name = $request->get('name');
            $category->description = $request->get('description');
            $category->sort_order = (int) $request->get('sort_order', 0);
            $category->is_active = $request->boolean('is_active');
            $category->save();

            DB::commit();

            session()->flash('message', trans('Abstract.category_updated'));

            return response()->json([
                'status' => 'success',
                'id' => $category->id,
                'message' => trans('Controllers.refreshing'),
                'redirectUrl' => route('showEventAbstracts', ['event_id' => $event_id, 'tab' => 'categories']),
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong! Please try again.',
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function postDeleteCategory(Request $request, $event_id, $category_id)
    {
        Event::scope()->findOrFail($event_id);
        $category = AbstractCategory::where('event_id', $event_id)->findOrFail($category_id);

        if ($category->templates()->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot delete category that is used by one or more abstract templates.',
            ]);
        }

        $category->delete();

        return response()->json([
            'status' => 'success',
            'message' => trans('Abstract.category_deleted'),
        ]);
    }
}
