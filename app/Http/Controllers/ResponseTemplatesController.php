<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDepositsRequest;
use App\Http\Requests\UpdateDepositsRequest;
use App\Models\ResponseTemplates;
use App\DataTables\ResponseTemplatesDataTable;
use Illuminate\Http\Request;
class ResponseTemplatesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ResponseTemplatesDataTable $dataTable)
    {
        return $dataTable->render('pages/apps.response-templates.list');
    }

 public function update_status(Request $request, $uuid)
    {
    $request->validate([
        'template' => 'required|string|max:600',
    ]);
    try {
$template = ResponseTemplates::where('id', $uuid)->firstOrFail();
    $template->update([
        'message' => $request->template,
    ]); 
    
    return response()->json([
        'success' => true,
        'message' => 'Template updated successfl',
    ]);
    
}   catch (ModelNotFoundException $e) {
    // User not found
    return response()->json([
        'success' => false,
        'message' => 'Template not found or already deleted.',
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
  
}
