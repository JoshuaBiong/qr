<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Setting;
use App\Models\VotersModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;


class VotersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $voters = VotersModel::paginate(10);
        $categories = Category::all();
        return Inertia::render('Admin/Voters/VotersList', [
            'voters' => $voters,
            'categories' => $categories
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(){
        $categories = Category::all();
        return Inertia::render('Admin/Voters/VotersCreate', [
            'categories' => $categories
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'required|string|max:255',
        ]);

        $voter = VotersModel::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'middle_name' => $validated['middle_name'],
            'status' => $validated['status'],
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
        ]);

        return redirect()->route('voters.index')
            ->with('success', 'Voter created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit()
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy()
    {
        //
    }





    
    public function import(Request $request)
    {

        Log::info('📥 Voter import started');
    
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'file' => 'required|file|mimes:csv,xlsx',
        ]);
    
        try {
            $file = $request->file('file');
            $sheets = Excel::toArray([], $file);
            $data = $sheets[0];
    
            // 1. Filter out blank rows
            $data = array_filter($data, function ($row) {
                return array_filter($row); // skip empty rows
            });
    
            $data = array_values($data); // reindex
    
            if (count($data) < 2) {
                Log::warning('⚠️ Not enough data rows after cleaning');
                return back()->withErrors(['file' => 'Not enough data rows']);
            }
    
            // 2. Normalize headers (assumes first non-empty row is the header)
            $headers = array_map(function ($header) {
                return strtolower(trim(str_replace(['.', ' '], '_', $header)));
            }, $data[0]);
    
            Log::info('📝 Headers found:', $headers);
    
            // 3. Loop through rows
            foreach (array_slice($data, 1) as $index => $row) {
                if (count($headers) !== count($row)) {
                    Log::warning("⚠️ Row $index skipped due to column mismatch", $row);
                    continue;
                }
    
                $mapped = array_combine($headers, $row);
                Log::info("✅ Processing row $index", $mapped);
    
                // Convert status to integer and ensure it's either 0 or 1
                $status = isset($mapped['status']) ? (int) $mapped['status'] : 0;
                $status = in_array($status, [0, 1]) ? $status : 0;
    
                VotersModel::create([
                    'first_name' => trim($mapped['first_name'] ?? ''),
                    'last_name' => trim($mapped['last_name'] ?? ''),
                    'middle_name' => trim($mapped['middle_name'] ?? ''),
                    'prec_no' => trim($mapped['PREC. NO'] ?? $mapped['prec_no'] ?? 0),
                    'status' => $status,
                    'uuid' => (string) Str::uuid(),
                    'category_id' => $request->category_id,
                ]);
            }
    
            Log::info("✅ Voter import completed successfully");
            return back()->with('success', 'Voters imported successfully!');
        } catch (\Exception $e) {
            Log::error("❌ Import failed: " . $e->getMessage());
            return back()->withErrors(['file' => 'An error occurred during import. Check logs.']);
        }
    }


    public function featureStatus(Request $request) {
        try {
            $setting = Setting::first();
            if (!$setting) {
                $setting = new Setting();
                $setting->status = 0;
                $setting->save();
            }
            
            if ($request->isMethod('post')) {
                // Toggle the status for POST requests
                $setting->status = $setting->status ? 0 : 1;
                $setting->save();
            }
            
            return response()->json([
                'success' => true,
                'message' => $setting->status ? 'Scan Once feature activated' : 'Scan Once feature deactivated',
                'status' => $setting->status
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage(),
                'status' => 0
            ], 500);
        }
    }
    

}