<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkOrder extends Model
{
    use HasFactory;

    protected $table = 'work_orders';

    protected $fillable = [
        'owner_id',
        'assigned_user_id',
        'title',
        'description',
        'address',
        'bidding_opens_at',
        'bidding_ends_at',
        'status',
    ];

    protected $casts = [
        'bidding_opens_at' => 'date',
        'bidding_ends_at' => 'date',
    ];

    /**
     * Get the owner of this work order.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get the assigned user for this work order.
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    /**
     * Get all bids for this work order.
     */
    public function bids(): HasMany
    {
        return $this->hasMany(Bid::class);
    }

    /**
     * Get all notes for this work order.
     */
    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }
}
