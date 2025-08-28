<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VBuilder extends Controller
{
    public function index()
    {
        return view('v3builder.index');
    }
}
