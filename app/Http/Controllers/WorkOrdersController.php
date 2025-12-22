<?php

namespace App\Http\Controllers;

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
            'all_workorders' => WorkOrder::all(),
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
            'title' => "required|max:255",
            'description' => "required|max:255",
            'address' => "required|max:255",
            'status' => "required"
        ]);

        $this->authorize('create', WorkOrder::class);

        return $request->user()
        ->workOrders()
        ->create($validated);
    }

    /**
     * Display the specified resource.
     */
    public function show(WorkOrder $workOrder)
    {
        $this->authorize('view', $workOrder);
        return $workOrder;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WorkOrder $workOrder)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WorkOrder $workOrder)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WorkOrder $workOrder)
    {
        //
    }
}
