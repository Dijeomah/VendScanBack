<?php

namespace App\Services;

class CloudinaryStorage
{
    private const FOLDER_PATH = 'vendscan';
    private const PROFILE_FOLDER_PATH = 'profile';

    public static function path(string $path): string
    {
        return pathinfo($path, PATHINFO_FILENAME);
    }

    private static function uploadFile(string $file, string $filename, string $folder, array $options = []): string
    {
        $newFilename = str_replace(' ', '_', $filename);
        $publicId = date('Y-m-d_His').'_'.$newFilename;

        $uploadOptions = array_merge([
            "public_id" => self::path($publicId),
            "folder" => $folder
        ], $options);

        return cloudinary()->upload($file, $uploadOptions)->getSecurePath();
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
        $result = cloudinary()->uploadApi()->upload($video, [
            'resource_type' => 'video',
            'public_id' => self::path($filename),
            'folder' => self::FOLDER_PATH,
            'chunk_size' => 6000000,
        ]);

        return $result['url'];
    }

    public static function delete(string $path): bool
    {
        $publicId = self::FOLDER_PATH.'/'.self::path($path);
        return cloudinary()->destroy($publicId);
    }
}
