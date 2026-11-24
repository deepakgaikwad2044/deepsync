<?php

namespace App\Config;

class File
{
    protected static function basePath(): string
    {
        return APP_ROOT . "/Public";
    }

    protected static function storagePath(): string
    {
        return self::basePath() . "/Storage";
    }

    /**
     * Validate a folder name and prevent path traversal.
     */
    private static function normalizeFolder(string $folder): string
    {
        $folder = str_replace("\\", "/", trim($folder, "/"));

        if ($folder === "" || str_contains($folder, "\0")) {
            throw new \InvalidArgumentException("Invalid storage folder");
        }

        $parts = explode("/", $folder);

        foreach ($parts as $part) {
            if (
                $part === "" ||
                $part === "." ||
                $part === ".." ||
                !preg_match('/\A[A-Za-z0-9_-]+\z/', $part)
            ) {
                throw new \InvalidArgumentException("Invalid storage folder");
            }
        }

        return implode("/", $parts);
    }

    /**
     * Resolve a path strictly inside Public/Storage.
     */
    private static function resolveStorageFile(string $path): ?string
    {
        if ($path === "" || str_contains($path, "\0")) {
            return null;
        }

        $path = str_replace("\\", "/", $path);
        $path = ltrim($path, "/");

        if (stripos($path, "storage/") === 0) {
            $path = substr($path, 8);
        }

        $parts = explode("/", $path);

        foreach ($parts as $part) {
            if (
                $part === "" ||
                $part === "." ||
                $part === ".." ||
                !preg_match('/\A[A-Za-z0-9][A-Za-z0-9._-]*\z/', $part)
            ) {
                return null;
            }
        }

        $root = realpath(self::storagePath());

        if ($root === false || is_link(self::storagePath())) {
            return null;
        }

        $target = $root . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $parts);

        if (!is_file($target) || is_link($target)) {
            return null;
        }

        $realTarget = realpath($target);

        if (
            $realTarget === false ||
            !str_starts_with($realTarget, $root . DIRECTORY_SEPARATOR)
        ) {
            return null;
        }

        return $realTarget;
    }

    public static function exists(string $path): bool
    {
        return self::resolveStorageFile($path) !== null;
    }

    public static function delete(string $path): bool
    {
        if (basename(str_replace("\\", "/", $path)) === "default_avtar.png") {
            return false;
        }

        $fullPath = self::resolveStorageFile($path);

        if ($fullPath === null || !is_writable($fullPath)) {
            return false;
        }

        return unlink($fullPath);
    }

    /**
     * Upload with size, MIME, extension and path validation.
     */
    public static function upload(
        array $file,
        string $folder,
        array $options = []
    ): ?string {

        if (empty($file["name"])) {
            return null;
        }

        if (
            !isset($file["error"], $file["tmp_name"], $file["size"]) ||
            $file["error"] !== UPLOAD_ERR_OK
        ) {
            throw new \RuntimeException("Invalid upload or upload error");
        }

        if (
            !is_string($file["tmp_name"]) ||
            !is_uploaded_file($file["tmp_name"])
        ) {
            throw new \RuntimeException("Invalid uploaded temporary file");
        }

        $maxSize = $options["max_size"] ?? 50 * 1024 * 1024;

        if (!is_numeric($maxSize) || (int) $maxSize <= 0) {
            throw new \InvalidArgumentException("Invalid maximum file size");
        }

        $actualSize = filesize($file["tmp_name"]);

        if (
            $actualSize === false ||
            $actualSize <= 0 ||
            $actualSize > (int) $maxSize
        ) {
            throw new \RuntimeException("File size invalid or too large");
        }

        $allowedMime = $options["mimes"] ?? [
            "image/jpeg",
            "image/png",
            "image/webp",
        ];

        if (!is_array($allowedMime) || empty($allowedMime)) {
            throw new \InvalidArgumentException("Invalid MIME allowlist");
        }

        $extensionMap = [
            "image/jpeg" => ["jpg", "jpeg"],
            "image/png"  => ["png"],
            "image/webp" => ["webp"],
            "image/gif"  => ["gif"],
            "application/pdf" => ["pdf"],
        ];

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file["tmp_name"]);

        if (
            !is_string($mime) ||
            !in_array($mime, $allowedMime, true) ||
            !isset($extensionMap[$mime])
        ) {
            throw new \RuntimeException("Invalid file type");
        }

        $extension = strtolower(
            pathinfo(basename($file["name"]), PATHINFO_EXTENSION)
        );

        if (!in_array($extension, $extensionMap[$mime], true)) {
            throw new \RuntimeException("File extension does not match MIME type");
        }

        $folder = self::normalizeFolder($folder);

        $storageRoot = self::storagePath();

        if (!is_dir($storageRoot)) {
            throw new \RuntimeException("Storage directory does not exist");
        }

        if (is_link($storageRoot)) {
            throw new \RuntimeException("Invalid storage directory");
        }

        $root = realpath($storageRoot);

        if ($root === false) {
            throw new \RuntimeException("Storage directory unavailable");
        }

        $destinationDir = $root;

        foreach (explode("/", $folder) as $part) {
            $destinationDir .= DIRECTORY_SEPARATOR . $part;

            if (is_link($destinationDir)) {
                throw new \RuntimeException("Invalid storage directory");
            }

            if (!is_dir($destinationDir)) {
                if (!mkdir($destinationDir, 0755) && !is_dir($destinationDir)) {
                    throw new \RuntimeException("Unable to create storage directory");
                }
            }
        }

        $realDir = realpath($destinationDir);

        if (
            $realDir === false ||
            !str_starts_with($realDir, $root . DIRECTORY_SEPARATOR)
        ) {
            throw new \RuntimeException("Invalid storage destination");
        }

        if (!is_writable($realDir)) {
            throw new \RuntimeException("Storage directory is not writable");
        }

        $fileName = bin2hex(random_bytes(16)) . "." . $extension;
        $destination = $realDir . DIRECTORY_SEPARATOR . $fileName;

        if (file_exists($destination)) {
            throw new \RuntimeException("Upload destination already exists");
        }

        if (!move_uploaded_file($file["tmp_name"], $destination)) {
            throw new \RuntimeException("File upload failed");
        }

        return "/Storage/{$folder}/{$fileName}";
    }
}
