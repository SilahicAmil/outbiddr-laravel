<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\WorkOrder;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class NotesController extends Controller
{
    use AuthorizesRequests;

    /**
     * Store a new note on a work order.
     */
    public function store(Request $request, WorkOrder $workOrder)
    {
        $this->authorize('create', Note::class);

        $validated = $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        $note = Note::create([
            'work_order_id' => $workOrder->id,
            'user_id' => $request->user()->id,
            'content' => $validated['content'],
        ]);

        return response()->json([
            'message' => 'Note added successfully.',
            'note' => [
                'id' => $note->id,
                'content' => $note->content,
                'user_id' => $note->user_id,
                'user_name' => $note->user->name,
                'created_at' => $note->created_at->toDateTimeString(),
                'updated_at' => $note->updated_at->toDateTimeString(),
            ],
        ], 201);
    }

    /**
     * Update a note - only the note author can do this.
     */
    public function update(Request $request, Note $note)
    {
        $this->authorize('update', $note);

        $validated = $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        $note->update(['content' => $validated['content']]);

        return response()->json([
            'message' => 'Note updated successfully.',
            'note' => [
                'id' => $note->id,
                'content' => $note->content,
                'user_id' => $note->user_id,
                'user_name' => $note->user->name,
                'created_at' => $note->created_at->toDateTimeString(),
                'updated_at' => $note->updated_at->toDateTimeString(),
            ],
        ]);
    }

    /**
     * Delete a note - only the note author can do this.
     */
    public function destroy(Request $request, Note $note)
    {
        $this->authorize('delete', $note);

        $note->delete();

        return response()->json([
            'message' => 'Note deleted successfully.',
        ]);
    }
}
