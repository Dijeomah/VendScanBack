<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Cloudinary\Api\Upload\UploadApi;
use Illuminate\Support\Facades\Log;

class CloudinaryStorage
{
    private const FOLDER_PATH = 'vendscan';
    private const PROFILE_FOLDER_PATH = 'profile';

    private static function getCloudinary(): Cloudinary
    {
        return new Cloudinary(config('cloudinary.cloud_url'));
    }

    public static function path(string $path): string
    {
        return pathinfo($path, PATHINFO_FILENAME);
    }

    private static function uploadFile(string $file, string $filename, string $folder, array $options = []): string
    {
        try {
            $cloudinary = self::getCloudinary();
            $newFilename = str_replace(' ', '_', $filename);
            $publicId = date('Y-m-d_His').'_'.$newFilename;

            $uploadOptions = array_merge([
                "public_id" => self::path($publicId),
                "folder" => $folder
            ], $options);

            $result = $cloudinary->uploadApi()->upload($file, $uploadOptions);
            return $result['secure_url'];
        } catch (\Exception $e) {
            Log::error('Cloudinary upload failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public static function upload(string $image, string $filename): string
    {
        return self::uploadFile($image, $filename, self::FOLDER_PATH);
    }

    public static function uploadProfile(string $image, string $filename): string
    {
        return self::uploadFile($image, $filename, self::PROFILE_FOLDER_PATH);
    }

    public static function uploadQr(string $image, string $filename): string
    {
        return self::upload($image, $filename); // Reuse main upload method
    }

    public static function uploadVid(string $video, string $filename): string
    {
        try {
            $cloudinary = self::getCloudinary();
            $result = $cloudinary->uploadApi()->upload($video, [
                'resource_type' => 'video',
                'public_id' => self::path($filename),
                'folder' => self::FOLDER_PATH,
                'chunk_size' => 6000000,
            ]);

            return $result['secure_url'];
        } catch (\Exception $e) {
            Log::error('Cloudinary video upload failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public static function delete(string $path): bool
    {
        try {
            $cloudinary = self::getCloudinary();
            $publicId = self::FOLDER_PATH.'/'.self::path($path);
            $result = $cloudinary->uploadApi()->destroy($publicId);
            return $result['result'] === 'ok';
        } catch (\Exception $e) {
            Log::error('Cloudinary delete failed: ' . $e->getMessage());
            return false;
        }
    }
}
