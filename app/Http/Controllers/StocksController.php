<?php

namespace App\Http\Controllers;
use App\Models\stocks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage; // Don't forget to import Storage


class StocksController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(stocks::all());
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
    public function store(Request $request)
    {
        //
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',        // up to 2MB :contentReference[oaicite:4]{index=4}
            'quantity' => 'required|numeric|min:1',
            'date_received' => 'required|date',
            'date_expiration' => 'required|date|after:today',
            'supplier' => 'required|string|max:255',
            'archived'    => 'sometimes|boolean',
        ]);

        $imagePath = null; // Initialize image path

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            // Store on "public" disk → storage/app/public/menu_images
            $imagePath = $request->file('image')->store('stock_images', 'public'); 
            // Build a URL or save just the relative path
            $validated['image'] = $imagePath;
            $validated['imageUrl'] = asset("storage/{$imagePath}");

            try {
                // Store the image to your specific folder using the 'orderAssets' disk
                // It will be saved in a 'menu_images' subfolder within the root of 'orderAssets' disk
                $specificFolderPath = $request->file('image')->store('stock_images', 'orderAssets');
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

        $stock = stocks::create($validated);

        return response()->json([
            'success'  => true,
            'stock' => $stock,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(stocks $stocks)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(stocks $stocks)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, stocks $stock)
    {
        //
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'image' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif,svg',        // up to 2MB :contentReference[oaicite:4]{index=4}
            'quantity' => 'sometimes|numeric|min:1',
            'date_received' => 'sometimes|nullable|date',
            'date_expiration' => 'sometimes|date',
            'supplier' => 'sometimes|string|max:255',
            'archived'    => 'sometimes|boolean',
        ]);

        if ($request->hasFile('image')) {
            if ($stock->image) { 
                 Storage::disk(name: 'public')->delete($stock->image);
            }
            $imagePath = $request->file('image')->store('stock_images', 'public');
            $validated['image'] = $imagePath;
            $validated['imageUrl'] = asset("storage/{$imagePath}");
            try {
                // Store the image to your specific folder using the 'orderAssets' disk
                // It will be saved in a 'menu_images' subfolder within the root of 'orderAssets' disk
                $specificFolderPath = $request->file('image')->store('stock_images', 'orderAssets');
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

        $stock->fill($validated);
        $stock->save();
        return response()->json($stock); 
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(stocks $stock)
    {
        //
        if ($stock->image) {
            Storage::disk('public')->delete($stock->image);
        }

        $stock->delete();

        return response()->json([
            'success' => true,
            'message' => 'Successfully deleted!'
        ]);
    }
}
