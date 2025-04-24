<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingsController extends Controller
{

     // Get the current status of the 'Scan Once Only' feature
     public function getStatus()
     {
         $settings = Setting::all();
         return Inertia::render('Admin/Voters/VotersList', [
            "settings" => $settings
         ]);
     }
 
     // Activate or deactivate the 'Scan Once Only' feature
     public function toggleStatus(Request $request)
     {
         $validated = $request->validate([
             'is_active' => 'required|boolean'
         ]);
 
         $setting = Setting::getScanOnceStatus();
 
         if (!$setting) {
             $setting = Setting::create([
                 'feature_name' => 'scan_once',
                 'is_active' => $validated['is_active']
             ]);
         } else {
             $setting->update([
                 'is_active' => $validated['is_active']
             ]);
         }

         return Inertia::render('@Layout/AuthenticatedLayout');
 
     }
}
