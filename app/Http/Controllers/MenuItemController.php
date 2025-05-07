<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Don't forget to import Storage
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class MenuItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $menuItems = MenuItem::all()->map(function ($item) {
                        
            $relative = 'menu_images/' . $item->image_filename;
            Log::debug("Checking public disk for: {$relative}");

            if (Storage::disk('public')->exists($relative)) {
                $contents = Storage::disk('public')->get($relative);
                $mimeType = Storage::disk('public')->mimeType($relative);
                $item->image_base64 = 'data:'.$mimeType.';base64,'.base64_encode($contents);
            } else {
                Log::warning("Missing file: {$relative}");
                $item->image_base64 = null;
            }
        
            return $item;
        });

        return response()->json($menuItems);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */

        
    public function storeMenu(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',        // up to 2MB :contentReference[oaicite:4]{index=4}
            'category'    => 'required|string',
            'description' => 'required|string',
            'price'       => 'required|numeric|min:1',              // decimal price :contentReference[oaicite:3]{index=3}
            'archived'    => 'sometimes|boolean',
        ]);

        $imagePath = null; // Initialize image path

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            // Store on "public" disk → storage/app/public/menu_images
            $imagePath = $request->file('image')->store('menu_images', 'public'); 
            // Build a URL or save just the relative path
            $validated['image'] = $imagePath;
            $validated['imageUrl'] = asset("storage/{$imagePath}");

            try {
                // Store the image to your specific folder using the 'orderAssets' disk
                // It will be saved in a 'menu_images' subfolder within the root of 'orderAssets' disk
                $specificFolderPath = $request->file('image')->store('menu_images', 'orderAssets');
                Log::info("Image also saved to specific folder: {$specificFolderPath}");
    
                // If you wanted to store this path in a database column *instead* of the public path,
                // you would remove or modify the lines setting $validated['image'] and $validated['imageUrl'] above
                // and add something like:
                // $validated['specific_folder_path'] = $specificFolderPath;
                // But based on the "only add, no edit" rule, we leave the public disk save and just add this.
    
            } catch (\Exception $e) {
                Log::error("Failed to save image to specific folder: " . $e->getMessage());
                // Handle the error if saving to the specific folder fails
            }
        }
        

        // 3. Create the MenuItem record
        $menuItem = MenuItem::create($validated);

        // 4. Return a JSON response (or redirect as needed)
        return response()->json([
            'success'  => true,
            'menuItem' => $menuItem,
        ], 201);
        
    }


    

    /**
     * Display the specified resource.
     */
    public function show(MenuItem $menuItem)
    {
        return response()->json($menuItem); 

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MenuItem $menuItem)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MenuItem $menuItem)
    {
        $validated = $request->validate([
            'name'        => 'sometimes|required|string|max:255',
            'image'       => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',        // up to 2MB :contentReference[oaicite:4]{index=4}
            'category'    => 'sometimes|required|string',
            'description' => 'sometimes|required|string',
            'price'       => 'sometimes|required|numeric|min:1',              // decimal price :contentReference[oaicite:3]{index=3}
            'archived'    => 'sometimes|boolean',
        ]);

        $imagePath = $menuItem->image;

        if ($request->hasFile('image')) {
            if ($menuItem->image) { 
                 Storage::disk(name: 'public')->delete($menuItem->image);
            }
            $imagePath = $request->file('image')->store('menu_images', 'public');
            $validated['image'] = $imagePath;
            $validated['imageUrl'] = asset("storage/{$imagePath}");
            try {
                // Store the image to your specific folder using the 'orderAssets' disk
                // It will be saved in a 'menu_images' subfolder within the root of 'orderAssets' disk
                $specificFolderPath = $request->file('image')->store('menu_images', 'orderAssets');
                Log::info("Image also saved to specific folder: {$specificFolderPath}");
    
                // If you wanted to store this path in a database column *instead* of the public path,
                // you would remove or modify the lines setting $validated['image'] and $validated['imageUrl'] above
                // and add something like:
                // $validated['specific_folder_path'] = $specificFolderPath;
                // But based on the "only add, no edit" rule, we leave the public disk save and just add this.
    
            } catch (\Exception $e) {
                Log::error("Failed to save image to specific folder: " . $e->getMessage());
                // Handle the error if saving to the specific folder fails
            }
        }

        $menuItem->fill($validated);
        $menuItem->save();
        return response()->json($menuItem); 
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MenuItem $menuItem)
    {
        //
    }
}
