<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MediaService
{

    /**
     * Ensure a directory exists on a disk and has sane permissions.
     */
    public function ensureDirectory(string $directory, string $disk = 'public', int $mode = 0775): void
    {
        $fs = Storage::disk($disk);

        // Create recursively if missing
        if (! $fs->exists($directory)) {
            $fs->makeDirectory($directory, $mode, true); // recursive
        }

        // Best-effort chmod (works for local/public disks on same host)
        $abs = method_exists($fs, 'path') ? $fs->path($directory) : null;
        if ($abs && is_dir($abs)) {
            @chmod($abs, $mode);
        }
    }

    /**
     * Store a file and return its relative storage path.
     * Optionally pass $visibility ('public' or 'private') for disks that support it.
     */
    public function storeFile(
        UploadedFile $file,
        string $directory = 'uploads',
        string $disk = 'public',
        ?string $visibility = null
    ): string {
        if (! $file->isValid()) {
            throw new \RuntimeException('Invalid upload: ' . $file->getErrorMessage());
        }

        $this->ensureDirectory($directory, $disk);

        $options = [];
        if ($visibility) {
            $options['visibility'] = $visibility; // works with putFile
        }

        // Use putFile so we can pass $options (visibility)
        $path = Storage::disk($disk)->putFile($directory, $file, $options);

        if (! $path) {
            throw new \RuntimeException('Failed to store uploaded file.');
        }

        return $path; // e.g. "shops/imports/abcd1234.xlsx"
    }

    /** Delete a file if it exists. */
    public function deleteFile(?string $path, string $disk = 'public'): void
    {
        if ($path && Storage::disk($disk)->exists($path)) {
            Storage::disk($disk)->delete($path);
        }
    }

    /**
     * Store a file from a request input if present; return path or null.
     */
    public function storeFromRequest(Request $request, string $input, string $directory = 'uploads', string $disk = 'public'): ?string
    {
        return $request->hasFile($input)
            ? $this->storeFile($request->file($input), $directory, $disk)
            : null;
    }

    /**
     * If request has a new file in $input: store it, delete $oldPath, return new path.
     * If no new file provided, return null (meaning: keep old path).
     */
    public function replaceFromRequest(Request $request, string $input, ?string $oldPath, string $directory = 'uploads', string $disk = 'public'): ?string
    {
        if (! $request->hasFile($input)) {
            return null; // no change
        }

        $newPath = $this->storeFile($request->file($input), $directory, $disk);
        $this->deleteFile($oldPath, $disk);

        return $newPath;
    }
}
