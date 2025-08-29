<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\RetailerWebManagement;
use App\Models\Theme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ThemeController extends Controller
{
    // index
    public function indexThemes(Request $request)
    {
        $retailer = Auth::user();

        $themes = Theme::whereIn('theme_type', ['retailer', 'both'])
            ->where('status', 1)
            ->get();

        $webManagement = RetailerWebManagement::where('retailer_id', $retailer->id)->first();

        return view('themes.index', compact('themes', 'webManagement'));
    }

    // AJAX : active theme
    public function activeTheme(Request $request)
    {
        DB::beginTransaction();
        try {
            $retailer = Auth::user();

            $webManagement = RetailerWebManagement::where('retailer_id', $retailer->id)->first();
            $webManagement->theme = $request->theme_id;
            $webManagement->save();

            DB::commit();
            return response()->json(['status' => true, 'msg' => 'Theme activated successfully!']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Theme activation error: ' . $e->getMessage());
            return response()->json(['status' => false, 'msg' => 'Something went wrong, Please try later!']);
        }
    }
}
