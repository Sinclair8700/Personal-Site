<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait HandlesFileUploads
{
    /**
     * Process file uploads for a specific field
     *
     * @param string $fieldName The name of the field in the request
     * @param string $storagePath The storage path where files should be saved
     * @param array $options Additional options for file handling
     * @return array Array of saved file paths
     */
    public function processFileUploads(string $fieldName, string $storagePath, array $options = []): array
    {
        $options = array_merge([
            'disk' => 'public',
            'keepExistingFiles' => true,
            'fileNamePrefix' => '',
            'fileNameSuffix' => '',
            'generateUniqueNames' => true,
        ], $options);

        $savedFiles = [];
        $request = request();
        
        // Get existing files to keep (if any)
        $keepFiles = [];
        if ($options['keepExistingFiles'] && $request->has('keep_' . $fieldName)) {
            $keepFiles = json_decode($request->input('keep_' . $fieldName), true) ?? [];
        }
        
        // Process existing files
        if (method_exists($this, 'getFilesAttribute') && $this->exists) {
            $existingFiles = $this->files[$fieldName] ?? [];
            
            foreach ($existingFiles as $existingFile) {
                $fileId = $existingFile['id'] ?? null;
                
                // If file ID is in the keep list, add it to saved files
                if ($fileId && in_array($fileId, $keepFiles)) {
                    $savedFiles[] = $existingFile;
                } else {
                    // Delete file if not in keep list
                    $this->deleteFile($existingFile['path'] ?? null, $options['disk']);
                }
            }
        }
        
        // Process new uploaded files
        $uploadedFiles = $request->file($fieldName);
        if (!$uploadedFiles) {
            return $savedFiles;
        }
        
        // Convert to array if it's a single file
        if (!is_array($uploadedFiles)) {
            $uploadedFiles = [$uploadedFiles];
        }
        
        // Process each uploaded file
        foreach ($uploadedFiles as $file) {
            if ($file instanceof UploadedFile) {
                $savedFile = $this->saveUploadedFile($file, $storagePath, $options);
                if ($savedFile) {
                    $savedFiles[] = $savedFile;
                }
            }
        }
        
        return $savedFiles;
    }
    
    /**
     * Save an uploaded file to storage
     *
     * @param UploadedFile $file The uploaded file
     * @param string $storagePath The storage path
     * @param array $options Additional options
     * @return array|null File information or null if save failed
     */
    protected function saveUploadedFile(UploadedFile $file, string $storagePath, array $options): ?array
    {
        // Generate filename
        $extension = $file->getClientOriginalExtension();
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        
        if ($options['generateUniqueNames']) {
            $fileName = $options['fileNamePrefix'] . Str::uuid() . $options['fileNameSuffix'] . '.' . $extension;
        } else {
            $fileName = $options['fileNamePrefix'] . $originalName . $options['fileNameSuffix'] . '.' . $extension;
        }
        
        // Ensure storage path ends with a slash
        $storagePath = rtrim($storagePath, '/') . '/';
        
        // Store the file
        $path = $file->storeAs($storagePath, $fileName, ['disk' => $options['disk']]);
        
        if (!$path) {
            return null;
        }
        
        // Return file information
        return [
            'id' => Str::uuid()->toString(),
            'name' => $file->getClientOriginalName(),
            'path' => $path,
            'size' => $file->getSize(),
            'mime' => $file->getMimeType(),
            'disk' => $options['disk'],
            'url' => Storage::disk($options['disk'])->url($path),
        ];
    }
    
    /**
     * Delete a file from storage
     *
     * @param string|null $path The file path
     * @param string $disk The storage disk
     * @return bool Whether deletion was successful
     */
    protected function deleteFile(?string $path, string $disk = 'public'): bool
    {
        if (!$path) {
            return false;
        }
        
        if (Storage::disk($disk)->exists($path)) {
            return Storage::disk($disk)->delete($path);
        }
        
        return false;
    }
    
    /**
     * Delete all files associated with a model
     *
     * @param string|null $fieldName Specific field to delete files from, or null for all fields
     * @return void
     */
    public function deleteAllFiles(?string $fieldName = null): void
    {
        if (!method_exists($this, 'getFilesAttribute')) {
            return;
        }
        
        $files = $this->files;
        
        if ($fieldName) {
            // Delete files for specific field
            $fieldFiles = $files[$fieldName] ?? [];
            foreach ($fieldFiles as $file) {
                $this->deleteFile($file['path'] ?? null, $file['disk'] ?? 'public');
            }
        } else {
            // Delete all files
            foreach ($files as $fieldFiles) {
                foreach ($fieldFiles as $file) {
                    $this->deleteFile($file['path'] ?? null, $file['disk'] ?? 'public');
                }
            }
        }
    }
} 