<?php

namespace App\Models;

use App\Http\Controllers\BidsController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class WorkOrder extends Pivot
{
    use HasFactory;
    //
    protected $table = 'work_orders';


    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function bids()
    {
        return $this->belongsToMany(Bid::class);
    }

}
