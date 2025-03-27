<?php

namespace App\Http\Controllers;

use App\Models\Coupan;
use Illuminate\Http\Request;

class CoupanController extends Controller
{
    public function index()
    {
        $coupans = Coupan::where('status',1)->get();
        return view('coupan.index',compact('coupans'));
    }
}
