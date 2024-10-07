<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddRegistrationFess extends Model
{
    use HasFactory;
    protected $fillable = ['monthly_fee','boarding_fee','management_fee','examination_fee','other','class_id','isActived','isDeleted'];

public function class()
{

    return $this->belongsTo(AddClass::class);
}

}
