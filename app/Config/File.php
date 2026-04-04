<?php

namespace App\Config;

class File
{
  /**
   * Base storage directory (absolute path)
   */
  protected static function basePath(): string
  {
    return APP_ROOT . "/public/storage/";
  }

  /**
   * Check if file exists (relative path from APP_ROOT)
   */
  public static function exists(string $path): bool
  {
    return file_exists(APP_ROOT . $path);
  }

  /**
   * Delete file (relative path from APP_ROOT)
   */
  public static function delete(string $path): bool
{
    $fullPath = APP_ROOT . $path;

    // ❌ Never delete default avatar
    if (basename($path) === 'default_avtar.png') {
        return false;
    }

    if (file_exists($fullPath)) {
        return unlink($fullPath);
    }

    return false;
}

  /**
   * Upload file with validation (size, mime)
   *
   * @param array $file    Uploaded file from $_FILES
   * @param string $folder Folder inside storage/ to save file
   * @param array $options Optional settings:
   *                       - max_size (int bytes, default 1MB)
   *                       - mimes (array of allowed mime types)
   *
   * @return string|null Relative path to saved file
   * @throws \Exception on validation or upload failure
   */
  public static function upload(
    array $file,
    string $folder,
    array $options = []
  ): ?string {
    if (empty($file["name"])) {
      return null; // No file uploaded
    }

    // Defaults
    //    $maxSize = $options['max_size'] ?? 1024 * 1024; // 1MB

    $maxSize = $options["max_size"] ?? 50 * 1024 * 1024; // 50MB

    $allowedMime = $options["mimes"] ?? [
      "image/jpeg",
      "image/png",
      "image/webp",
    ];

    // Validate size
    if ($file["size"] > $maxSize) {
      throw new \Exception("File size exceeds limit");
    }

    // Validate MIME type
    $mime = mime_content_type($file["tmp_name"]);
    if (!in_array($mime, $allowedMime)) {
      throw new \Exception("Invalid file type");
    }

    // Ensure storage folder exists
    $storagePath = self::basePath() . $folder;
    if (!is_dir($storagePath)) {
      mkdir($storagePath, 0755, true);
    }

    // Generate unique filename
    $extension = pathinfo($file["name"], PATHINFO_EXTENSION);
    $fileName = time() . "_" . uniqid() . "." . $extension;

    // Move uploaded file
    if (
      !move_uploaded_file($file["tmp_name"], $storagePath . "/" . $fileName)
    ) {
      throw new \Exception("File upload failed");
    }

    // Return relative path for DB usage
    return "/storage/{$folder}/{$fileName}";
  }
}
