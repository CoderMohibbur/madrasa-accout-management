<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name','last_name','full_name','dob','roll','email','mobile','photo','age',
        'fees_type_id','class_id','section_id','academic_year_id',
        'scholarship_amount','isActived','isDeleted'
    ];

    // ✅ Proper relations (use these in Transaction Center)
    public function feesType()
    {
        return $this->belongsTo(AddFessType::class, 'fees_type_id');
    }

    public function class()
    {
        return $this->belongsTo(AddClass::class, 'class_id');
    }

    public function section()
    {
        return $this->belongsTo(AddSection::class, 'section_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AddAcademy::class, 'academic_year_id');
    }

    // ✅ Backward compatibility (আপনার পুরোনো কোড ভাঙবে না)
    public function AddFess()   { return $this->belongsTo(AddFessType::class, 'fees_type_id'); }
    public function classes()   { return $this->belongsTo(AddClass::class, 'class_id'); }
    public function Sections()   { return $this->belongsTo(AddSection::class, 'section_id'); }
    public function Academy()   { return $this->belongsTo(AddAcademy::class, 'academic_year_id'); }
}
