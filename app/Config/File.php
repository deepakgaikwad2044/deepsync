<?php

namespace App\Config;

class File
{
  /**
   * Base path for public storage
   * /public/storage/
   */
  protected static function basePath(): string
  {
    return APP_ROOT . "/public";
  }

  /**
   * Convert storage path to full system path
   */
  protected static function fullPath(string $path): string
  {
    return self::basePath() . $path;
  }

  /**
   * Check if file exists
   */
  public static function exists(string $path): bool
  {
    return file_exists(self::fullPath($path));
  }

  /**
   * Delete file safely
   */
  public static function delete(string $path): bool
  {
    $fullPath = self::fullPath($path);

    // protect default image
    if (basename($path) === 'default_avtar.png') {
      return false;
    }

    if (file_exists($fullPath)) {
      return unlink($fullPath);
    }

    return false;
  }

  /**
   * Upload file with validation
   */
  public static function upload(
    array $file,
    string $folder,
    array $options = []
  ): ?string {

    if (empty($file["name"])) {
      return null;
    }

    // max size 50MB default
    $maxSize = $options["max_size"] ?? 50 * 1024 * 1024;

    $allowedMime = $options["mimes"] ?? [
      "image/jpeg",
      "image/png",
      "image/webp",
    ];

    // size validation
    if ($file["size"] > $maxSize) {
      throw new \Exception("File size too large");
    }

    // mime validation
    $mime = mime_content_type($file["tmp_name"]);
    if (!in_array($mime, $allowedMime)) {
      throw new \Exception("Invalid file type");
    }

    // create storage directory
    $storagePath = self::basePath() . "/storage/" . $folder;

    if (!is_dir($storagePath)) {
      mkdir($storagePath, 0755, true);
    }

    // unique filename
    $ext = pathinfo($file["name"], PATHINFO_EXTENSION);
    $fileName = time() . "_" . uniqid() . "." . $ext;

    $destination = $storagePath . "/" . $fileName;

    // move file
    if (!move_uploaded_file($file["tmp_name"], $destination)) {
      throw new \Exception("File upload failed");
    }

    // return DB path (IMPORTANT)
    return "/storage/{$folder}/{$fileName}";
  }
}
