
<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database Configuration
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'jan_suraksha';

$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($mysqli->connect_errno) {
    die('Database Connection Error: ' . $mysqli->connect_error);
}

// Security: XSS Prevention Helper
if (!function_exists('e')) {
    function e($string) {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
}

// Security: Safe MIME detection for file uploads
if (!function_exists('js_detect_mime_type')) {
    /**
     * Detect MIME type of an uploaded file based on its actual content.
     * Returns null if detection fails.
     */
    function js_detect_mime_type(string $tmpPath): ?string {
        if (!is_file($tmpPath)) {
            return null;
        }

        // Prefer ext/fileinfo
        if (function_exists('finfo_open')) {
            $finfo = @finfo_open(FILEINFO_MIME_TYPE);
            if ($finfo) {
                $mime = @finfo_file($finfo, $tmpPath);
                finfo_close($finfo);
                if ($mime !== false && is_string($mime)) {
                    return $mime;
                }
            }
        }

        // Fallback where available
        if (function_exists('mime_content_type')) {
            $mime = @mime_content_type($tmpPath);
            if ($mime !== false && is_string($mime)) {
                return $mime;
            }
        }

        return null;
    }
}

// Security: Centralised safe upload helper
if (!function_exists('js_secure_upload')) {
    /**
     * Perform strict validation and move an uploaded file.
     *
     * @param array  $file              The single element from $_FILES
     * @param array  $allowedMap        [extension => [allowed mime types]]
     * @param string $destinationDir    Absolute target directory
     * @param int    $maxSizeBytes      Maximum allowed file size in bytes
     * @param string $errorMessage      Populated with a user-facing error on failure
     * @param string $context           Short label for logging (e.g. 'evidence', 'mugshot')
     *
     * @return string|null              The stored filename on success, or null on failure
     */
    function js_secure_upload(array $file, array $allowedMap, string $destinationDir, int $maxSizeBytes, ?string &$errorMessage = null, string $context = 'upload'): ?string {
        $errorMessage = null;

        // Basic PHP upload error handling
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            if (isset($file['error']) && $file['error'] !== UPLOAD_ERR_NO_FILE) {
                $errorMessage = 'There was a problem receiving the uploaded file.';
                error_log("Rejected {$context} upload: PHP upload error code " . $file['error']);
            }
            return null;
        }

        if (empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            $errorMessage = 'Invalid upload attempt.';
            error_log("Rejected {$context} upload: tmp_name missing or not an uploaded file.");
            return null;
        }

        $size = isset($file['size']) ? (int)$file['size'] : 0;
        if ($size <= 0 || $size > $maxSizeBytes) {
            $errorMessage = 'File size is not allowed.';
            error_log("Rejected {$context} upload: size {$size} bytes outside allowed range.");
            return null;
        }

        $mime = js_detect_mime_type($file['tmp_name']);
        if ($mime === null) {
            $errorMessage = 'Unable to validate file type.';
            error_log("Rejected {$context} upload: could not detect MIME type.");
            return null;
        }

        $ext = strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));
        if ($ext === '' || !isset($allowedMap[$ext])) {
            $errorMessage = 'Unsupported file type.';
            error_log("Rejected {$context} upload: extension '{$ext}' not in allow-list (MIME {$mime}).");
            return null;
        }

        $allowedMimesForExt = $allowedMap[$ext];
        if (!in_array($mime, $allowedMimesForExt, true)) {
            $errorMessage = 'File type does not match the file content.';
            error_log("Rejected {$context} upload: MIME '{$mime}' not allowed for extension '{$ext}'.");
            return null;
        }

        // Extra safeguard for images: verify structure
        if (strpos($mime, 'image/') === 0 && function_exists('getimagesize')) {
            if (@getimagesize($file['tmp_name']) === false) {
                $errorMessage = 'Image file appears to be corrupted or invalid.';
                error_log("Rejected {$context} upload: getimagesize() failed for supposed image.");
                return null;
            }
        }

        // Ensure destination directory exists
        if (!is_dir($destinationDir)) {
            if (!@mkdir($destinationDir, 0755, true) && !is_dir($destinationDir)) {
                $errorMessage = 'Server configuration error: upload directory is unavailable.';
                error_log("Rejected {$context} upload: unable to create destination directory '{$destinationDir}'.");
                return null;
            }
        }

        // Generate a safe, random filename with normalised extension
        try {
            $randomName = bin2hex(random_bytes(16));
        } catch (Exception $e) {
            $errorMessage = 'Server configuration error: cannot generate safe filename.';
            error_log("Rejected {$context} upload: random_bytes() failed - " . $e->getMessage());
            return null;
        }

        $finalName = $randomName . '.' . $ext;
        $targetPath = rtrim($destinationDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $finalName;

        if (!@move_uploaded_file($file['tmp_name'], $targetPath)) {
            $errorMessage = 'Failed to store uploaded file.';
            error_log("Rejected {$context} upload: move_uploaded_file() failed for '{$targetPath}'.");
            return null;
        }

        return $finalName;
    }
}