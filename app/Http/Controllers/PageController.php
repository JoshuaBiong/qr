<?php

namespace App\Http\Controllers;

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
                    'is_in_database' => false
                ], 200); 
            }
            return response()->json([
                'success' => true,
                'message' => 'Voter found in database',
                'is_in_database' => true,
                'data' => [
                    'first_name' => $voter->first_name,
                    'last_name' => $voter->last_name,
                    'middle_name' => $voter->middle_name,
                    'uuid' => $voter->uuid
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
   
}
