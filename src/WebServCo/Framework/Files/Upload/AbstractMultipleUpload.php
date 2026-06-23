<?php

declare(strict_types=1);

namespace WebServCo\Framework\Files\Upload;

use InvalidArgumentException;
use Throwable;
use WebServCo\Framework\Exceptions\UploadException;

use function array_key_exists;
use function chmod;
use function count;
use function is_array;
use function move_uploaded_file;

/**
 * Multiple upload.
 *
 * @phpcs:disable SlevomatCodingStandard.Variables.DisallowSuperGlobalVariable.DisallowedSuperGlobalVariable
 */
abstract class AbstractMultipleUpload extends AbstractUpload
{
    /**
     * @var array<int,string>
     */
    private array $fileNames = [];

    /**
     * @var array<int,string>
     */
    private array $fileMimeTypes = [];

    final public function doItem(int $index): bool
    {
        if (!$_FILES) {
            return false;
        }

        if (!isset($_FILES[$this->formFieldName]['error'][$index])) {
            throw new UploadException(Codes::NO_FILE);
        }

        if ($_FILES[$this->formFieldName]['error'][$index] !== Codes::OK) {
            throw new UploadException($_FILES[$this->formFieldName]['error'][$index]);
        }
        $this->validateFileType($_FILES[$this->formFieldName]['type'][$index]);
        $this->fileNames[$index] = $this->generateUploadedFileName(
            $_FILES[$this->formFieldName]['name'][$index],
            $_FILES[$this->formFieldName]['type'][$index],
        );

        $this->fileMimeTypes[$index] = $_FILES[$this->formFieldName]['type'][$index];

        $result = move_uploaded_file(
            $_FILES[$this->formFieldName]['tmp_name'][$index],
            $this->uploadDirectory . $this->fileNames[$index],
        );
        if (!$result) {
            throw new UploadException(Codes::CANT_WRITE);
        }

        try {
            chmod($this->uploadDirectory . $this->fileNames[$index], 0664);
        } catch (Throwable) {
            // Operation not permitted
        }

        return true;
    }

    final public function getItemFileName(int $index): string
    {
        if (!array_key_exists($index, $this->fileNames)) {
            throw new InvalidArgumentException('Invalid index.');
        }

        return $this->fileNames[$index];
    }

    final public function getItemFileMimeType(int $index): string
    {
        if (!array_key_exists($index, $this->fileMimeTypes)) {
            throw new InvalidArgumentException('Invalid index.');
        }

        return $this->fileMimeTypes[$index];
    }

    final public function getTotalUploaded(): int
    {
        if (!$_FILES) {
            return 0;
        }

        $fieldToCheck = $_FILES[$this->formFieldName]['error'];

        if (!isset($fieldToCheck)) {
            throw new UploadException(Codes::NO_FILE);
        }

        if (!is_array($fieldToCheck)) {
            throw new InvalidArgumentException('Not multiple files upload.');
        }

        return count($fieldToCheck);
    }
}
