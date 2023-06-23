<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Client extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'birthdate',
        'zip_code',
        'address',
        'province',
        'complement',
    ];

    protected $dates = ['deleted_at'];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

}
