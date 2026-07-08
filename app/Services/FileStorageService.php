<?php

namespace App\Services;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileStorageService
{
    public function __construct(
        private readonly string $disk = 's3'
    ) {}

    public function uploadPublic(UploadedFile $file, string $directory): string
    {
        return $file->storePublicly($directory, $this->disk);
    }

    /**
     * @param  iterable<UploadedFile|null>|UploadedFile|null  $files
     * @return array<int, string>
     */
    public function uploadPublicMany(iterable|UploadedFile|null $files, string $directory): array
    {
        $paths = [];

        if ($files instanceof UploadedFile) {
            $files = [$files];
        }

        if ($files === null) {
            return $paths;
        }

        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $paths[] = $this->uploadPublic($file, $directory);
            }
        }

        return $paths;
    }

    public function replacePublic(?string $oldPath, UploadedFile $file, string $directory): string
    {
        $this->deleteIfExists($oldPath);

        return $this->uploadPublic($file, $directory);
    }

    public function deleteIfExists(?string $path): void
    {
        if (! $path) {
            return;
        }

        $disk = $this->disk();

        if ($disk->exists($path)) {
            $disk->delete($path);
        }
    }

    /**
     * @param  iterable<string|null>  $paths
     */
    public function deleteManyIfExists(iterable $paths): void
    {
        foreach ($paths as $path) {
            $this->deleteIfExists($path);
        }
    }

    public function movePublicIfExists(string $fromPath, string $toPath): bool
    {
        $disk = $this->disk();

        if (! $disk->exists($fromPath)) {
            return false;
        }

        $disk->move($fromPath, $toPath);
        $disk->setVisibility($toPath, 'public');

        return true;
    }

    public function disk(): Filesystem
    {
        return Storage::disk($this->disk);
    }
}
