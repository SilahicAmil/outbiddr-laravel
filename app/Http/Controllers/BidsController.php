<?php

namespace App\Http\Controllers;

use App\Models\Bid;
use App\Models\WorkOrder;
use App\Http\Resources\BidResource;
use App\Http\Resources\WorkOrderResource;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Inertia\Inertia;

class BidsController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of open work orders available for bidding.
     */
    public function index()
    {
        // Get all open work orders that don't have an assigned user
        $openWorkOrders = WorkOrder::where('status', 'open')
            ->whereNull('assigned_user_id')
            ->with('owner')
            ->get();

        return Inertia::render('Bids/index', [
            'open_bids' => WorkOrderResource::collection($openWorkOrders),
        ]);
    }

    /**
     * Store a new bid on a work order.
     */
    public function store(Request $request, WorkOrder $workOrder)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
        ]);

        // Check if work order is still open for bidding
        if ($workOrder->status !== 'open' && $workOrder->assigned_user_id !== null) {
            return response()->json([
                'message' => 'This work order is no longer accepting bids.',
            ], 422);
        }

        // Check if user is not the owner
        if ($workOrder->owner_id === $request->user()->id) {
            return response()->json([
                'message' => 'You cannot bid on your own work order.',
            ], 422);
        }

        // Check if user already has a pending bid on this work order
        $existingBid = Bid::where('work_order_id', $workOrder->id)
            ->where('user_id', $request->user()->id)
            ->where('status', 'pending')
            ->first();

        if ($existingBid) {
            // Update existing bid amount
            $existingBid->update(['amount' => $validated['amount']]);
            return response()->json([
                'message' => 'Your bid has been updated.',
                'bid' => new BidResource($existingBid),
            ]);
        }

        // Create new bid
        $bid = Bid::create([
            'work_order_id' => $workOrder->id,
            'user_id' => $request->user()->id,
            'amount' => $validated['amount'],
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Your bid has been placed.',
            'bid' => new BidResource($bid),
        ], 201);
    }

    /**
     * Accept a bid - only the work order owner can do this.
     */
    public function accept(Request $request, Bid $bid)
    {
        $workOrder = $bid->workOrder;

        // Verify the current user is the owner of the work order
        if ($workOrder->owner_id !== $request->user()->id) {
            return response()->json([
                'message' => 'You are not authorized to accept this bid.',
            ], 403);
        }

        // Check if work order is still open
        if (strtolower($workOrder->status) !== 'open') {
            return response()->json([
                'message' => 'This work order is no longer open.',
            ], 422);
        }

        // Accept this bid
        $bid->update(['status' => 'accepted']);

        // Reject all other pending bids on this work order
        Bid::where('work_order_id', $workOrder->id)
            ->where('id', '!=', $bid->id)
            ->where('status', 'pending')
            ->update(['status' => 'rejected']);

        // Assign the bidder to the work order and change status
        $workOrder->update([
            'assigned_user_id' => $bid->user_id,
            'status' => 'assigned',
        ]);

        return response()->json([
            'message' => 'Bid accepted successfully. The user has been assigned to the work order.',
            'work_order' => new WorkOrderResource($workOrder->fresh()),
        ]);
    }

    /**
     * Reject a bid - only the work order owner can do this.
     */
    public function reject(Request $request, Bid $bid)
    {
        $workOrder = $bid->workOrder;

        // Verify the current user is the owner of the work order
        if ($workOrder->owner_id !== $request->user()->id) {
            return response()->json([
                'message' => 'You are not authorized to reject this bid.',
            ], 403);
        }

        // Check if bid is pending
        if ($bid->status !== 'pending') {
            return response()->json([
                'message' => 'This bid has already been processed.',
            ], 422);
        }

        $bid->update(['status' => 'rejected']);

        return response()->json([
            'message' => 'Bid rejected successfully.',
        ]);
    }

    /**
     * Get all bids for a specific work order.
     */
    public function workOrderBids(WorkOrder $workOrder)
    {
        $bids = $workOrder->bids()->with('user')->orderBy('amount', 'asc')->get();

        return response()->json([
            'bids' => BidResource::collection($bids),
        ]);
    }
}
