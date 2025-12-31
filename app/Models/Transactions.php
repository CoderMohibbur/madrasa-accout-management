<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'doner_id',
        'lender_id',
        'fess_type_id',
        'transactions_type_id',
        'student_book_number',
        'recipt_no',

        'monthly_fees',
        'boarding_fees',
        'management_fees',
        'exam_fees',
        'others_fees',
        'total_fees',

        'debit',
        'credit',
        'transactions_date',
        'account_id',

        // ✅ FINAL meta fields
        'class_id',
        'section_id',
        'months_id',
        'academic_year_id',
        'roll',

        'created_by_id',

        'catagory_id',
        'expens_id',
        'income_id',

        'note',

        'c_d_1',
        'c_d_2',
        'c_d_3',
        'c_d_4',
        'c_d_5',
        'c_d_6',
        'c_d_7',

        'c_s_1',
        'c_s_2',
        'c_s_3',
        'c_s_4',
        'c_s_5',
        'c_s_6',
        'c_s_7',
        'c_s_8',

        'c_i_1',
        'c_i_2',
        'c_i_3',
        'c_i_4',
        'c_i_5',
        'c_i_6',

        'isActived',
        'isDeleted'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function doner()
    {
        return $this->belongsTo(Donor::class, 'doner_id');
    }

    public function donor()
    {
        return $this->doner();
    }

    public function lender()
    {
        return $this->belongsTo(Lender::class, 'lender_id');
    }

    public function fess()
    {
        return $this->belongsTo(AddFessType::class, 'fess_type_id');
    }

    public function feesType()
    {
        return $this->fess();
    }

    public function transactionsType()
    {
        return $this->belongsTo(TransactionsType::class, 'transactions_type_id');
    }

    public function type()
    {
        return $this->transactionsType();
    }

    public function sections()
    {
        return $this->belongsTo(AddSection::class, 'section_id');
    }

    public function section()
    {
        return $this->sections();
    }

    public function academic()
    {
        return $this->belongsTo(AddAcademy::class, 'academic_year_id');
    }

    public function academicYear()
    {
        return $this->academic();
    }

    public function accounts()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function account()
    {
        return $this->accounts();
    }

    public function classes()
    {
        return $this->belongsTo(AddClass::class, 'class_id');
    }

    public function classRef()
    {
        return $this->classes();
    }

    public function months()
    {
        return $this->belongsTo(AddMonth::class, 'months_id');
    }

    public function month()
    {
        return $this->months();
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function createdBy()
    {
        return $this->users();
    }

    public function getTitleAttribute()
    {
        return $this->c_s_1;
    }
}
