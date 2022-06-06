<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg',
            'type' => 'required|in:profile,project',
        ]);

        if ($request->type === 'profile') return response()->json(['error' => 'PROFILE_IMAGE_UPLOAD_NOT_ALLOWED'], 400);

        if ($request->type === 'project' && !auth()->user()->project) {
            return response()->json([
                'error' => "USER_NOT_REGISTERED_AS_PROJECT",
            ], 400);
        }

        $image_path = cloudinary()->upload($request->file('image')->getRealPath(), [
            'folder' => 'projects'
        ])->getSecurePath();

        auth()->user()->project->images()->create([
            'url' => $image_path,
        ]);

        return response()->json([
            'status' => 'ok',
        ]);
    }
}
