<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Automation extends Controller
{
    public function index()
    {
        return view('automation.index');
    }

    public function automationCampaign()
    {
        return view('automation.automation-campaign');
    }
}
