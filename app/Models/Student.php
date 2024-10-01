<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;
    protected $fillable = ['first_name','last_name','full_name','dob','roll','email','mobile','photo','age','fees_type_id','class_id','section_id','academic_year_id','isActived','isDeleted'];

public function AddFess(){

    return $this->belongsTo(AddFessType::class);
}
public function classes(){

    return $this->belongsTo(AddClass::class);
}
public function Section(){

    return $this->belongsTo(AddSection::class);
}
public function Academy(){

    return $this->belongsTo(AddAcademy::class);
}

}

