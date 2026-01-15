<?php

namespace App\Http\Controllers;

use App\Http\Resources\WorkOrderResource;
use App\Models\WorkOrder;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;



class WorkOrdersController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return Inertia::render('WorkOrders/index', [
           'all_workorders' => WorkOrderResource::collection(
                WorkOrder::all()
            ),
        ]);
    }

    public function create()
    {
        return Inertia::render('WorkOrders/CreateWorkOrder');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
            'address' => 'nullable|max:255',
            'status' => 'required|in:open,assigned,completed',
            'bidding_opens_at' => 'required|date',
            'bidding_ends_at' => 'required|date|after_or_equal:bidding_opens_at',
        ]);

        $this->authorize('create', WorkOrder::class);

        $workOrder = $request->user()
            ->workOrders()
            ->create($validated);

        return response()->json([
            'message' => 'Work order created successfully.',
            'work_order' => new WorkOrderResource($workOrder),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, WorkOrder $workOrder)
    {
        $this->authorize('view', $workOrder);

        // Load bids with user info if the current user is the owner
        $bids = [];
        $isOwner = $request->user()->id === $workOrder->owner_id;

        if ($isOwner) {
            $bids = $workOrder->bids()
                ->with('user')
                ->orderBy('amount', 'asc')
                ->get()
                ->map(function ($bid) {
                    return [
                        'id' => $bid->id,
                        'user_id' => $bid->user_id,
                        'bidder_name' => $bid->user->name,
                        'amount' => $bid->amount,
                        'status' => $bid->status,
                        'created_at' => $bid->created_at->toDateTimeString(),
                    ];
                });
        }

        // Load notes for all users
        $notes = $workOrder->notes()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($note) {
                return [
                    'id' => $note->id,
                    'user_id' => $note->user_id,
                    'user_name' => $note->user->name,
                    'content' => $note->content,
                    'created_at' => $note->created_at->toDateTimeString(),
                    'updated_at' => $note->updated_at->toDateTimeString(),
                ];
            });

        return Inertia::render("WorkOrders/ViewWorkOrder", [
            'workOrder' => new WorkOrderResource($workOrder),
            'bids' => $bids,
            'notes' => $notes,
            'isOwner' => $isOwner,
            'currentUserId' => $request->user()->id,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WorkOrder $workOrder)
    {
        return Inertia::render("WorkOrders/EditWorkOrder", [
            'workOrder' => new WorkOrderResource($workOrder)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WorkOrder $workOrder)
    {
        $validated = $request->validate([
            'title' => "required|max:255",
            'description' => "required|max:255",
            'address' => "required|max:255",
            'status' => "required"
        ]);
        $this->authorize('update', $workOrder);
        // Only update the workorder in the database, not the resource
        $workOrder->update($validated);
        return new WorkOrderResource($workOrder);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WorkOrder $workOrder)
    {
        //
    }
}
