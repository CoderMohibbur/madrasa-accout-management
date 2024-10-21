<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lender extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'phone', 'email','address','bank_detils','users_id','isActived', 'isDeleted'];

    public function users()
    {

        return $this->belongsTo(User::class);
    }
}
