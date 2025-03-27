<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CMS extends Controller
{
    public function index()
    {
        return view('cms.index');
    }
}
