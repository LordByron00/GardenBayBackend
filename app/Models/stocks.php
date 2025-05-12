<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage; // Make sure this is imported

class stocks extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'image', 'quantity', 'date_received', 'date_expiration', 'supplier', 'archived'];

    public function getImageUrlAttribute()
    {
        // Check if image_path (or whatever your column is named) exists
        if ($this->image) { // Use $this->image if your column is 'image'
            // Use the 'storage' helper which knows about the public disk
            // and the storage:link you created.
            // Assumes your database column is named 'image'
            return Storage::disk('public')->url($this->image);

            // OR if your column is named 'image_path', use $this->image_path
            // return Storage::disk('public')->url($this->image_path);
        }

        // Return null or a default image URL if no image is set
        return null; // Or return asset('images/default.jpg') if you have a default
    }
    protected $appends = ['image_url']; // <-- Make sure 'image_url' is in this array

}
