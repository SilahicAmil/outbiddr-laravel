<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class WorkOrder extends Pivot
{
    //
    protected $table = 'work_orders';


    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

}
