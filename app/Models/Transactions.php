<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{
    use HasFactory;
    protected $fillable = ['student_id','doner_id','lender_id','transactions_type_id','student_book_number','fess_type_id','recipt_no','monthly_fees','boarding_fees','management_fees','exam_fees','others_fees','total_fees','debit','credit','transactions_date',
    'account_id','class_id','session_id','months_id','academy_year_id','created_by_id','noth','c_d_1','c_d_2','c_d_3','c_d_4','c_d_5','c_d_6','c_d_7','c_s_1','c_s_2','c_s_3','c_s_4','c_s_5','c_s_6','c_s_7','c_s_8',
    'c_i_1','c_i_2','c_i_3','c_i_4','c_i_5','c_i_6','isActived', 'isDeleted'];


    public function Students()
    {

        return $this->belongsTo(Student::class);
    }

    public function Donors()
    {

        return $this->belongsTo(Donor::class);
    }
    public function Lenders()
    {

        return $this->belongsTo(Lender::class);
    }
    public function Fess()
    {

        return $this->belongsTo(AddFessType::class);
    }
    public function Sections()
    {

        return $this->belongsTo(AddSection::class);
    } public function academic()
    {

        return $this->belongsTo(AddAcademy::class);
    } public function Accounts()
    {

        return $this->belongsTo(Account::class);
    } public function classes()
    {

        return $this->belongsTo(AddClass::class);
    } public function Months()
    {

        return $this->belongsTo(AddMonth::class);
    } public function Users()
    {

        return $this->belongsTo(User::class);
    }

}
