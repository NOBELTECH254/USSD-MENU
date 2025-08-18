<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfilesRequest;
use App\Models\Profiles;
use DataTables;
use App\DataTables\ProfilesDataTable;
use Yajra\DataTables\Html\Builder;
use Illuminate\Http\Request;
use App\Services\ProfileService;
class ProfilesController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     protected $profileService;

     public function __construct(ProfileService $profileService)
     {
         $this->profileService = $profileService;
     }
 
   
     
    public function index(Builder $builder,ProfilesDataTable $dataTable)
    {
/*

        if (request()->ajax()) {
            return DataTables::of(Profiles::query())->toJson();
        }
        $dataTable = $builder->columns([
            ['data' => 'id', 'footer' => 'Id'],
            ['data' => 'mobile_number', 'footer' => 'Name'],
            ['data' => 'email', 'footer' => 'Email'],
            ['data' => 'created_at', 'footer' => 'Created At'],
            ['data' => 'updated_at', 'footer' => 'Updated At'],
        ]);

return view('pages/apps.profiles.list', compact('dataTable'));
*/
        return $dataTable->render('pages/apps.profiles.list');

    }
/**
     * Display the specified resource.
     */
    public function show(Profiles $profile)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function reset($uuid)
    {
        try {
            $result = $this->profileService->reset_pin($uuid);
            return response()->json($result, 200);
    }   catch (ModelNotFoundException $e) {
        // User not found
        return response()->json([
            'success' => false,
            'message' => 'User not found or already deleted.',
        ], 404);
    
    } catch (ValidationException $e) {
        // Validation failed
        return response()->json([
            'success' => false,
            'message' => 'Validation error.',
            'errors'  => $e->errors(), // contains field-specific errors
        ], 422);
    } catch (\Exception $e) {
        // Any other error
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
        ], 500);
    }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Profiles $profile)
    {
       // echo $profile->id;
    }

    public function update_status(Request $request, $uuid)
    {
    $request->validate([
        'status' => 'required|in:INACTIVE,ACTIVE',
        'reason' => 'required|string|max:1000',
    ]);
    try {
$profile = Profiles::where('uuid', $uuid)->firstOrFail();
    $history = $profile->status_history ?? [];
    $entry = [
        'status' => $request->status,
        'reason' => $request->status === 'INACTIVE' ? $request->reason : null,
        'changed_by' => auth()->user()->id ?? null,
        'changed_at' => now()->toDateTimeString(),
    ];

    $history[] = $entry;

    $profile->update([
        'status' => $request->status,
        'status_history' => $history,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Account status updated successfully to '.$request->status,
    ]);

}   catch (ModelNotFoundException $e) {
    // User not found
    return response()->json([
        'success' => false,
        'message' => 'User not found or already deleted.',
    ], 404);

} catch (ValidationException $e) {
    // Validation failed
    return response()->json([
        'success' => false,
        'message' => 'Validation error.',
        'errors'  => $e->errors(), // contains field-specific errors
    ], 422);
} catch (\Exception $e) {
    // Any other error
    return response()->json([
        'success' => false,
        'message' => $e->getMessage(),
    ], 500);
}

}

public function statusHistory(Profiles $profile)
{
    return response()->json($profile->status_history ?? []);
}

}
