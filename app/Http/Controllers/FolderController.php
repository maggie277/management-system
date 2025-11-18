<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use App\Models\Category;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class FolderController extends Controller
{
    /**
     * Store a newly created folder
     */
    public function store(Request $request)
    {
        Log::info('=== FOLDER STORE METHOD CALLED ===');
        Log::info('Request data:', $request->all());

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:folders,id',
            'category_id' => 'required|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please fix the validation errors.');
        }

        try {
            DB::beginTransaction();

            // Check if parent folder belongs to the same category
            if ($request->parent_id) {
                $parentFolder = Folder::find($request->parent_id);
                if ($parentFolder && $parentFolder->category_id != $request->category_id) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Parent folder must belong to the same category.');
                }
            }

            // Check if folder with same name already exists in this category and parent
            $existingFolder = Folder::where('category_id', $request->category_id)
                ->where('parent_id', $request->parent_id)
                ->where('name', $request->name)
                ->first();

            if ($existingFolder) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'A folder with this name already exists in this location.');
            }

            // Create the folder
            $folder = Folder::create([
                'name' => $request->name,
                'description' => $request->description,
                'parent_id' => $request->parent_id,
                'category_id' => $request->category_id,
                'created_by' => Auth::id(),
            ]);

            DB::commit();

            Log::info('Folder created successfully:', ['folder_id' => $folder->id]);

            // Determine redirect URL based on context
            $redirectUrl = $this->getRedirectUrl($request, $folder);

            return redirect($redirectUrl)
                ->with('success', 'Folder created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Folder creation error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating folder. Please try again.');
        }
    }

    /**
     * Get redirect URL based on current context
     */
    private function getRedirectUrl(Request $request, Folder $folder)
    {
        // If we're in a specific folder context, redirect back there
        if ($request->parent_id) {
            $parentFolder = Folder::find($request->parent_id);
            if ($parentFolder) {
                return route('folders.show', [
                    'category' => $parentFolder->category_id,
                    'folder' => $parentFolder->id
                ]);
            }
        }

        // If we have a category context, redirect to that category
        if ($request->category_id) {
            return route('documents.category', ['category' => $request->category_id]);
        }

        // Default redirect to documents list
        return route('documents.list');
    }

    /**
     * Get folder contents
     */
    public function show($categoryId, $folderId)
    {
        $category = Category::findOrFail($categoryId);
        $folder = Folder::where('id', $folderId)
            ->where('category_id', $categoryId)
            ->firstOrFail();

        return app(DocumentController::class)->list(request()->merge([
            'category_id' => $categoryId,
            'folder_id' => $folderId
        ]));
    }
}
