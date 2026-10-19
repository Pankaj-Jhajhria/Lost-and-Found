<?php
// Shared image-upload handling for lost.php and found.php.

const UPLOAD_MAX_BYTES = 2097152; // 2 MB

const UPLOAD_HTACCESS = <<<'HT'
# Uploaded files must never be executed as scripts.
Options -ExecCGI -Indexes
RemoveHandler .php .phtml .php3 .php4 .php5 .php7 .phps .pht .phar .cgi .pl .py
RemoveType .php .phtml .php3 .php4 .php5 .php7 .phps .pht .phar
<IfModule mod_authz_core.c>
    <FilesMatch "\.(php[0-9]?|phtml|phps|pht|phar|cgi|pl|py|sh|asp|aspx|jsp)$">
        Require all denied
    </FilesMatch>
</IfModule>
<IfModule !mod_authz_core.c>
    <FilesMatch "\.(php[0-9]?|phtml|phps|pht|phar|cgi|pl|py|sh|asp|aspx|jsp)$">
        Order allow,deny
        Deny from all
    </FilesMatch>
</IfModule>
HT;

/**
 * Validates and stores an uploaded image.
 * Returns [relative_path_or_null, error_message_or_null].
 * No file selected => [null, null] (the image column is nullable).
 */
function save_uploaded_image($file)
{
    if ($file === null) {
        return [null, null]; // no file field submitted at all
    }
    if (!is_array($file) || !isset($file['error']) || is_array($file['error'])) {
        return [null, "Invalid upload request."];
    }

    switch ($file['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_NO_FILE:
            return [null, null];
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            return [null, "The image is too large (maximum 2 MB)."];
        default:
            return [null, "The image could not be uploaded. Please try again."];
    }

    if (!is_uploaded_file($file['tmp_name'])) {
        return [null, "Invalid upload."];
    }
    if ($file['size'] > UPLOAD_MAX_BYTES) {
        return [null, "The image is too large (maximum 2 MB)."];
    }

    // Decide the type from the file's contents, never from the client-supplied name.
    $info = @getimagesize($file['tmp_name']);
    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/gif'  => 'gif',
        'image/webp' => 'webp',
    ];
    if ($info === false || !isset($allowed[$info['mime']])) {
        return [null, "Only JPG, PNG, GIF or WEBP images are allowed."];
    }

    $dir = __DIR__ . '/images';
    if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
        return [null, "Could not store the image."];
    }
    if (!file_exists($dir . '/.htaccess')) {
        file_put_contents($dir . '/.htaccess', UPLOAD_HTACCESS);
    }

    // Server-generated name: no user-controlled filename or extension.
    $name = bin2hex(random_bytes(16)) . '.' . $allowed[$info['mime']];
    if (!move_uploaded_file($file['tmp_name'], $dir . '/' . $name)) {
        return [null, "Could not store the image."];
    }
    @chmod($dir . '/' . $name, 0644);

    return ['images/' . $name, null];
}

function upload_error_exit($message, $backUrl)
{
    http_response_code(400);
    echo htmlspecialchars($message) . ' <a href="' . htmlspecialchars($backUrl) . '">Go back</a>';
    exit();
}
