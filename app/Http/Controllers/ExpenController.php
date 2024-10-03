<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExpenController extends Controller
{
    public function index(){

return view('expen.index');



    }

    public function create(){


        return view('expen.create');


    }
}
