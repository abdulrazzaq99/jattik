<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class CourierPayment extends Model
{
    use GlobalStatus;
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
