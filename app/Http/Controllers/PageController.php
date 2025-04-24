<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Setting;
use App\Models\VotersModel;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PageController extends Controller
{
          

            public function verify($uuid)
        {
            try {
                $voter = VotersModel::where('uuid', $uuid)->first();
                if (!$voter) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid voter ID',
                        'is_in_database' => false,
                    ], 200);
                }

                // Check if 'Scan Once Only' feature is active
                $setting = Setting::first();
                if ($setting && $setting->status == 1) {
                    if ($voter->status == 1) {  // If the voter has already been scanned
                        return response()->json([
                            'success' => false,
                            'message' => 'QR code has already been scanned',
                            'already_scanned' => true,
                            'is_in_database' => true
                        ], 200);
                    }
                    // Mark as scanned
                    $voter->update(['status' => 1]);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Voter found in database',
                    'is_in_database' => true,
                    'data' => [
                        'first_name' => $voter->first_name,
                        'last_name' => $voter->last_name,
                        'middle_name' => $voter->middle_name,
                        'uuid' => $voter->uuid,
                    ]
                ], 200);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred during verification',
                    'is_in_database' => false
                ], 200);
            }
        }


   

    public function viewcards() {
        $category= Category::all();
        $voters = VotersModel::with('category')->get();
        return Inertia::render("CardsQr", [
            "voters"=> $voters,
            "category"=> $category
        ]);
    }
}
