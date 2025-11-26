<?php

declare(strict_types=1);

namespace WebServCo\Framework\Files\Upload;

use function __;

final class Codes
{
    public const int OK = 0;
    public const int INI_SIZE = 1;
    public const int FORM_SIZE = 2;
    public const int PARTIAL = 3;
    public const int NO_FILE = 4;
    public const int NO_TMP_DIR = 6;
    public const int CANT_WRITE = 7;
    public const int EXTENSION = 8;

    /* custom codes */
    public const int TYPE_NOT_ALLOWED = 91;
    public const int IMAGE_TOO_SMALL = 101;
    public const int IMAGE_WRONG_ASPECT_RATIO = 102;

    public static function getMessage(int $errorCode): string
    {
        switch ($errorCode) {
            case self::OK:
                return '';
            case self::INI_SIZE:
            case self::FORM_SIZE:
                return __('Uploaded file size exceeds maximum file size allowed.');
            case self::PARTIAL:
                return __('The uploaded file was only partially uploaded.');
            case self::NO_FILE:
                return __('No file was uploaded.');
            case self::TYPE_NOT_ALLOWED:
                return __('File type not allowed.');
            case self::CANT_WRITE:
                return __('Error saving uploaded file.');
            case self::IMAGE_TOO_SMALL:
                return __('Image is too small.');
            case self::IMAGE_WRONG_ASPECT_RATIO:
                return __('Image has wrong aspect ratio.');
            case self::NO_TMP_DIR:
            case self::EXTENSION:
            default:
                return __('Upload error.');
        }
    }
}
