<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Coupan extends Controller
{
    public function index()
    {
        return view('coupan.index');
    }
}
