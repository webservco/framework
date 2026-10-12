<?php

declare(strict_types=1);

namespace WebServCo\Framework\Files\Upload;

use function __;
use function function_exists;

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
                return self::__('Uploaded file size exceeds maximum file size allowed.');
            case self::PARTIAL:
                return self::__('The uploaded file was only partially uploaded.');
            case self::NO_FILE:
                return self::__('No file was uploaded.');
            case self::TYPE_NOT_ALLOWED:
                return self::__('File type not allowed.');
            case self::CANT_WRITE:
                return self::__('Error saving uploaded file.');
            case self::IMAGE_TOO_SMALL:
                return self::__('Image is too small.');
            case self::IMAGE_WRONG_ASPECT_RATIO:
                return self::__('Image has wrong aspect ratio.');
            case self::NO_TMP_DIR:
            case self::EXTENSION:
            default:
                return self::__('Upload error.');
        }
    }

    /**
    * `__()` when the I18n library is loaded (defines it), the message as is otherwise.
    *
    * Named `__` so `xgettext` extracts the messages with the usual `--keyword=__`.
    */
    private static function __(string $message): string
    {
        return function_exists('__')
            ? __($message)
            : $message;
    }
}
