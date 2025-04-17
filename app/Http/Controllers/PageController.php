<?php

namespace App\Http\Controllers;

use App\Models\VotersModel;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PageController extends Controller
{

    public function verify($uuid)
    {
        $voter = VotersModel::where('uuid', $uuid)->first();

        if (!$voter) {
            return Inertia::render('Homepage', [
                'verification' => [
                    'success' => false,
                    'message' => 'Invalid voter ID',
                    'is_in_database' => false
                ]
            ]);
        }

        return Inertia::render('Homepage', [
            'verification' => [
                'success' => true,
                'message' => 'Voter found in database',
                'is_in_database' => true,
                'data' => [
                    'first_name' => $voter->first_name,
                    'last_name' => $voter->last_name,
                    'middle_name' => $voter->middle_name,
                    'uuid' => $voter->uuid
                ]
            ]
        ]);
    }
   
}
