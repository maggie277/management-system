<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\AssetFolder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;

class AssetController extends Controller
{
    public function index()
    {
        $totalAssets = Asset::count();
        $activeAssets = Asset::where('status', 'active')->count();
        $maintenanceAssets = Asset::where('status', 'maintenance')->count();
        $disposedAssets = Asset::where('status', 'disposed')->count();

        return view('assets.index', compact('totalAssets', 'activeAssets', 'maintenanceAssets', 'disposedAssets'));
    }

    public function create()
    {
        $folders = AssetFolder::all();
        return view('assets.create', compact('folders'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'owner' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'serial_number' => 'nullable|string|max:255|unique:assets,serial_number',
            'location' => 'nullable|string|max:255',
            'purchase_date' => 'nullable|date',
            'value' => 'nullable|numeric',
            'currency' => 'nullable|string|max:5',
            'status' => 'required|in:active,maintenance,disposed',
            'folder_id' => 'nullable|exists:asset_folders,id'
        ]);

        Asset::create($request->only([
            'name', 'owner', 'category', 'description', 'serial_number',
            'location', 'purchase_date', 'value', 'status', 'currency', 'folder_id'
        ]));

        // Redirect based on the view context
        if ($request->has('view') && $request->view === 'folders') {
            $redirectParams = ['view' => 'folders'];
            if ($request->has('folder_id') && $request->folder_id) {
                $redirectParams['folder_id'] = $request->folder_id;
            }
            return redirect()->route('assets.list', $redirectParams)->with('success', 'Asset added successfully.');
        }

        return redirect()->route('assets.list')->with('success', 'Asset added successfully.');
    }

    public function list(Request $request)
    {
        $view = $request->get('view', 'all');

        $query = Asset::query();
        $currentFolder = null;
        $subfolders = collect();

        // Check if folder_id column exists before using folder functionality
        $hasFolderColumn = Schema::hasColumn('assets', 'folder_id');

        if ($view === 'folders' && $hasFolderColumn) {
            $folderId = $request->get('folder_id');

            if ($folderId && $folderId !== 'unassigned') {
                $currentFolder = AssetFolder::find($folderId);
                if ($currentFolder) {
                    $query->where('folder_id', $folderId);

                    // Get asset subfolders
                    $subfolders = AssetFolder::where('parent_id', $folderId)
                        ->withCount('assets')
                        ->get();
                }
            } elseif ($folderId === 'unassigned') {
                // Show unassigned assets
                $query->whereNull('folder_id');

                // For unassigned, show all root folders
                $subfolders = AssetFolder::whereNull('parent_id')
                    ->withCount('assets')
                    ->get();
            } else {
                // Root level - show assets without folders and root folders
                $query->whereNull('folder_id');

                // Get root asset folders
                $subfolders = AssetFolder::whereNull('parent_id')
                    ->withCount('assets')
                    ->get();
            }
        } else {
            // If folder view is requested but column doesn't exist, show all assets
            $view = 'all';
        }

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('owner', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Status filtering
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Sort functionality
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'owner_asc':
                $query->orderBy('owner', 'asc');
                break;
            case 'owner_desc':
                $query->orderBy('owner', 'desc');
                break;
            case 'value_high':
                $query->orderBy('value', 'desc');
                break;
            case 'value_low':
                $query->orderBy('value', 'asc');
                break;
            default:
                $query->latest();
                break;
        }

        $assets = $query->get();

        return view('assets.list', compact('assets', 'currentFolder', 'subfolders', 'view'));
    }

    public function show(Asset $asset)
    {
        return view('assets.show', compact('asset'));
    }

    public function edit(Asset $asset)
    {
        $folders = AssetFolder::all();
        return view('assets.edit', compact('asset', 'folders'));
    }

    public function update(Request $request, Asset $asset)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'owner' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'serial_number' => "nullable|string|max:255|unique:assets,serial_number,{$asset->id}",
            'location' => 'nullable|string|max:255',
            'purchase_date' => 'nullable|date',
            'value' => 'nullable|numeric',
            'currency' => 'nullable|string|max:5',
            'status' => 'required|in:active,maintenance,disposed',
            'folder_id' => 'nullable|exists:asset_folders,id'
        ]);

        $asset->update($request->only([
            'name', 'owner', 'category', 'description', 'serial_number',
            'location', 'purchase_date', 'value', 'status', 'currency', 'folder_id'
        ]));

        // Redirect based on the view context
        if ($request->has('view') && $request->view === 'folders') {
            $redirectParams = ['view' => 'folders'];
            if ($request->has('folder_id') && $request->folder_id) {
                $redirectParams['folder_id'] = $request->folder_id;
            }
            return redirect()->route('assets.list', $redirectParams)->with('success', 'Asset updated successfully.');
        }

        return redirect()->route('assets.list')->with('success', 'Asset updated successfully.');
    }

    public function destroy(Asset $asset)
    {
        $asset->delete();
        return redirect()->back()->with('success', 'Asset deleted successfully.');
    }

    /**
     * Folder management methods
     */
    public function createFolder(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:asset_folders,id'
        ]);

        AssetFolder::create([
            'name' => $request->name,
            'description' => $request->description,
            'parent_id' => $request->parent_id,
            'created_by' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Folder created successfully.');
    }

    public function updateFolder(Request $request, AssetFolder $folder)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $folder->update($request->only(['name', 'description']));

        return redirect()->back()->with('success', 'Folder updated successfully.');
    }

    public function deleteFolder(AssetFolder $folder)
    {
        // Move assets to root (null folder_id) when deleting folder
        Asset::where('folder_id', $folder->id)->update(['folder_id' => null]);

        // Also handle child folders - move them to root or delete them
        AssetFolder::where('parent_id', $folder->id)->update(['parent_id' => null]);

        $folder->delete();

        return redirect()->back()->with('success', 'Folder deleted successfully.');
    }

    /**
     * Download asset data as CSV
     */
    public function export(Request $request)
    {
        $assets = Asset::all();

        $fileName = 'assets_export_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
        ];

        $callback = function() use ($assets) {
            $file = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($file, [
                'Name', 'Owner', 'Category', 'Description', 'Serial Number',
                'Location', 'Purchase Date', 'Value', 'Currency', 'Status'
            ]);

            // Add data rows
            foreach ($assets as $asset) {
                fputcsv($file, [
                    $asset->name,
                    $asset->owner,
                    $asset->category,
                    $asset->description,
                    $asset->serial_number,
                    $asset->location,
                    $asset->purchase_date,
                    $asset->value,
                    $asset->currency,
                    $asset->status,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get folder breadcrumb data
     */
    public function getFolderBreadcrumb(AssetFolder $folder)
    {
        $breadcrumbs = [];
        $current = $folder;

        while ($current) {
            $breadcrumbs[] = [
                'id' => $current->id,
                'name' => $current->name
            ];
            $current = $current->parent;
        }

        return array_reverse($breadcrumbs);
    }
}
