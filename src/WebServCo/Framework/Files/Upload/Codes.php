<?php
namespace WebServCo\Framework\Files\Upload;

final class Codes
{
    const OK = 0;
    const INI_SIZE = 1;
    const FORM_SIZE = 2;
    const PARTIAL = 3;
    const NO_FILE = 4;
    const NO_TMP_DIR = 6;
    const CANT_WRITE = 7;
    const EXTENSION = 8;
    /* custom codes */
    const TYPE_NOT_ALLOWED = 91;

    public static function getMessage($errorCode)
    {
        switch ($errorCode) {
            case self::OK:
                return null;
                break;
            case self::INI_SIZE:
            case self::FORM_SIZE:
                return __('Uploaded file size exceeds maximum file size allowed.');
                break;
            case self::PARTIAL:
                return __('The uploaded file was only partially uploaded.');
                break;
            case self::NO_FILE:
                return __('No file was uploaded.');
                break;
            case self::TYPE_NOT_ALLOWED:
                return __('File type not allowed.');
                break;
            case self::CANT_WRITE:
                return __('Error saving uploaded file.');
                break;
            case self::NO_TMP_DIR:
            case self::EXTENSION:
            default:
                return __('Upload error.');
                break;
        }
    }
}
