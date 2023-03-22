<?php

declare(strict_types=1);

namespace WebServCo\Framework\Files\Upload;

use WebServCo\Framework\Exceptions\UploadException;

/**
 * Advanced functionality.
 *
 * Uses validation and preprocessing
 */
abstract class AbstractFileUploadProcessor
{
    abstract protected function generateUploadedFileName(string $uploadFileName, string $uploadFileMimeType): string;

    abstract protected function processUploadedFileBeforeSaving(string $filePath): bool;

    abstract protected function validateUploadedFileBeforeSaving(string $filePath): bool;

    /**
     * @param array<string,string> $allowedExtensions
     */
    public function __construct(protected array $allowedExtensions)
    {
    }

    /**
     * @param array<int|string,mixed> $uploadedFiles
     */
    public function handleUpload(string $fieldName, string $uploadDirectory, array $uploadedFiles): ?string
    {
        if ([] === $uploadedFiles) {
            return null;
        }

        if (!isset($uploadedFiles[$fieldName]['error'])) {
            throw new UploadException(Codes::NO_FILE);
        }

        if (Codes::OK !== $uploadedFiles[$fieldName]['error']) {
            throw new UploadException($uploadedFiles[$fieldName]['error']);
        }

        $this->validateFileMimeType($uploadedFiles[$fieldName]['type']);

        $this->validateUploadedFileBeforeSaving($uploadedFiles[$fieldName]['tmp_name']);

        $this->processUploadedFileBeforeSaving($uploadedFiles[$fieldName]['tmp_name']);

        $uploadedFileName = $this->generateUploadedFileName(
            $uploadedFiles[$fieldName]['name'],
            $uploadedFiles[$fieldName]['type'],
        );
        $uploadPath = $uploadDirectory . $uploadedFileName;
        \umask(0002);
        $result = \move_uploaded_file($uploadedFiles[$fieldName]['tmp_name'], $uploadPath);

        if (false === $result) {
            throw new UploadException(Codes::CANT_WRITE);
        }

        return $uploadedFileName;
    }

    private function validateFileMimeType(string $fileMimeType): bool
    {
        if ([] === $this->allowedExtensions) {
            // All extensions allowed.
            return true;
        }

        if (\array_key_exists($fileMimeType, $this->allowedExtensions)) {
            return true;
        }

        throw new UploadException(Codes::TYPE_NOT_ALLOWED);
    }
}
