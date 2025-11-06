<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class CourierProduct extends Model
{
    use GlobalStatus;

    public function type()
    {
        return $this->belongsTo(Type::class, 'courier_type_id');
    }
}
