<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FolderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:folders,id',
            'category_id' => 'required|exists:categories,id',
        ]);

        try {
            DB::beginTransaction();

            // Check if parent folder belongs to the same category
            if ($request->parent_id) {
                $parentFolder = Folder::find($request->parent_id);
                if ($parentFolder && $parentFolder->category_id != $request->category_id) {
                    return redirect()->back()->with('error', 'Parent folder must belong to the same category.');
                }
            }

            // Create the folder
            Folder::create([
                'name' => $request->name,
                'description' => $request->description,
                'parent_id' => $request->parent_id,
                'category_id' => $request->category_id,
                'created_by' => Auth::id(),
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Folder created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Error creating folder. Please try again.')
                ->withInput();
        }
    }

    public function getFoldersByCategory(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
        ]);

        $folders = Folder::where('category_id', $request->category_id)
                        ->get(['id', 'name', 'parent_id']);

        return response()->json($folders);
    }
}
