<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DrivingLicenseController extends Controller
{
    public function upload(Request $request)
    {
        // Validate the uploaded file
        $request->validate([
            'license_image' => 'required|image|mimes:jpeg,png,jpg|max:2048', // Limit size to 2MB
        ]);

        // Store the image in a directory like 'licenses'
        $path = $request->file('license_image')->store('licenses', 'public');

        // Save the image path to the database (optional)
        // DrivingLicense::create(['user_id' => $request->user()->id, 'image_path' => $path]);

        // Return a response
        return response()->json([
            'message' => 'License image uploaded successfully!',
            'image_url' => Storage::url($path), // Public URL for accessing the image
        ], 201);
    }
}
