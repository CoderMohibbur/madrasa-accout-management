<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'father_name',
        'dob',
        'roll',
        'email',
        'mobile',
        'address',
        'photo',
        'age',

        'fees_type_id',
        'class_id',
        'section_id',
        'academic_year_id',

        'scholarship_amount',

        // ✅ Boarding
        'is_boarding',
        'boarding_start_date',
        'boarding_end_date',
        'boarding_note',

        // ✅ Status flags
        'isActived',
        'isDeleted',
    ];

    protected $casts = [
        'dob' => 'date',
        'roll' => 'integer',
        'scholarship_amount' => 'decimal:2',

        'is_boarding' => 'boolean',
        'boarding_start_date' => 'date',
        'boarding_end_date' => 'date',

        'isActived' => 'boolean',
        'isDeleted' => 'boolean',
    ];

    /**
     * ✅ Legacy friendly:
     * আপনার অনেক জায়গায় $student->name চেক করা আছে,
     * তাই name attribute কে full_name হিসেবে expose করে দিলাম।
     */
    public function getNameAttribute(): ?string
    {
        return $this->full_name;
    }

    // ✅ Relations (Transaction Center + Profile)
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

    public function transactions()
    {
        return $this->hasMany(Transactions::class, 'student_id');
    }

    /**
     * ✅ Scopes (Controllers clean থাকবে + consistent filtering)
     */
    public function scopeActive($q)
    {
        return $q->where('isActived', true)->where('isDeleted', false);
    }

    public function scopeBoarding($q, ?bool $value = true)
    {
        if ($value === null) return $q;
        return $q->where('is_boarding', $value);
    }

    public function scopeFilterAcademic($q, $yearId = null, $classId = null, $sectionId = null)
    {
        if ($yearId)   $q->where('academic_year_id', $yearId);
        if ($classId)  $q->where('class_id', $classId);
        if ($sectionId)$q->where('section_id', $sectionId);
        return $q;
    }

    // ✅ Backward compatibility (পুরোনো কোড ভাঙবে না)
    public function AddFess()   { return $this->belongsTo(AddFessType::class, 'fees_type_id'); }
    public function classes()   { return $this->belongsTo(AddClass::class, 'class_id'); }
    public function Sections()  { return $this->belongsTo(AddSection::class, 'section_id'); }
    public function Academy()   { return $this->belongsTo(AddAcademy::class, 'academic_year_id'); }
}