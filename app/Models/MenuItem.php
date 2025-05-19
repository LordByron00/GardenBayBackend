<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage; // Make sure this is imported


class MenuItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', // <-- Add the name field here
        'image', // <-- Assuming you have a field for the image path/filename
        'category', // <-- Add other fields you're sending
        'description', // <-- Add other fields you're sending
        'price', // <-- Add other fields you're sending
        'archived'// ... add any other fields you want to allow mass assignment for
    ];


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

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        // Add fields you want to hide when converting to JSON
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        // Define type casting for attributes if needed (e.g., 'price' => 'float')
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }


}
