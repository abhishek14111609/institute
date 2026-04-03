<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaController extends Controller
{
    public function publicFile(string $path)
    {
        $normalizedPath = ltrim($path, '/');

        // Basic traversal guard
        if (Str::contains($normalizedPath, ['..', '\\'])) {
            abort(404);
        }

        $disk = Storage::disk('public');

        if (! $disk->exists($normalizedPath)) {
            abort(404);
        }

        $absolutePath = $disk->path($normalizedPath);

        return response()->file($absolutePath, [
            'Cache-Control' => 'public, max-age=604800',
        ]);
    }
}
