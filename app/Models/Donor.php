<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donor extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'email', 'mobile','fees_type_id','isActived', 'isDeleted'];

    public function AddFess()
{

    return $this->belongsTo(AddFessType::class);
}


}


