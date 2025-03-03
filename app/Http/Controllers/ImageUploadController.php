<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Illuminate\Support\Facades\Log;
use Google\Cloud\Storage\StorageClient;

class ImageUploadController extends Controller
{
    public function uploadImage(Request $request)
    {
        Log::info("📤 Processing image upload...");

        try {
            if (!$request->hasFile('image')) {
                Log::error("❌ No file uploaded.");
                return response()->json(['error' => 'No file uploaded.'], 400);
            }

            $file = $request->file('image');
            $filename = 'questions/' . uniqid() . '.' . $file->getClientOriginalExtension();

            $storage = app('firebase.storage')->getBucket();
            $object = $storage->upload(file_get_contents($file), ['name' => $filename]);

            $object->update(['acl' => []], ['predefinedAcl' => 'PUBLICREAD']);
            $imageUrl = "https://firebasestorage.googleapis.com/v0/b/" . env('FIREBASE_STORAGE_BUCKET') . "/o/" . urlencode($filename) . "?alt=media";

            Log::info("✅ Image uploaded successfully. URL: {$imageUrl}");
            return response()->json(['success' => true, 'imageUrl' => $imageUrl]);

        } catch (\Exception $e) {
            Log::error("❌ Image upload failed: " . $e->getMessage());
            return response()->json(['error' => 'Image upload failed.'], 500);
        }
    }

    public function deleteImages(Request $request)
    {
        Log::info("🗑 Received request to delete unused images.");

        $imagesToDelete = $request->input('images', []);

        if (empty($imagesToDelete)) {
            Log::info("🗑 No images to delete.");
            return response()->json(['message' => 'No images to delete.']);
        }

        Log::info("🚀 Deleting unused images: " . json_encode($imagesToDelete));

        $storage = app('firebase.storage')->getBucket();

        foreach ($imagesToDelete as $imageUrl) {
            $path = urldecode(parse_url($imageUrl, PHP_URL_PATH));
            $path = str_replace('/v0/b/' . env('FIREBASE_STORAGE_BUCKET') . "/o/", '', $path);
            $path = explode('?alt=media', $path)[0];

            Log::info("🔥 Trying to delete file at path: " . $path);

            $object = $storage->object($path);
            if ($object->exists()) {
                $object->delete();
                Log::info("✅ Deleted unused image: " . $imageUrl);
            } else {
                Log::warning("⚠ Image not found in storage (already deleted?): " . $imageUrl);
            }
        }

        return response()->json(['success' => true, 'message' => 'Unused images deleted.']);
    }
}

