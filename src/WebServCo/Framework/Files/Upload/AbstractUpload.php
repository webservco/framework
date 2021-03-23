<?php

declare(strict_types=1);

namespace WebServCo\Framework\Files\Upload;

use WebServCo\Framework\Exceptions\UploadException;

abstract class AbstractUpload
{

    /**
    * Allowed extensions.
    *
    * @var array<int,string>
    */
    protected array $allowedExtensions;
    protected string $fileName;
    protected string $fileMimeType;
    protected string $formFieldName;
    protected string $uploadDirectory;

    abstract protected function generateUploadedFileName(string $uploadFileName, string $uploadFileMimeType): string;

    public function __construct(string $uploadDirectory)
    {
        $this->allowedExtensions = []; // default all
        $this->formFieldName = 'upload';
        $this->uploadDirectory = $uploadDirectory;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getFileMimeType(): string
    {
        return $this->fileMimeType;
    }

    final public function do(): bool
    {
        // phpcs:ignore SlevomatCodingStandard.Variables.DisallowSuperGlobalVariable.DisallowedSuperGlobalVariable
        if (empty($_FILES)) {
            return false;
        }
        // phpcs:ignore SlevomatCodingStandard.Variables.DisallowSuperGlobalVariable.DisallowedSuperGlobalVariable
        if (!isset($_FILES[$this->formFieldName]['error'])) {
            throw new UploadException(Codes::NO_FILE);
        }
        // phpcs:ignore SlevomatCodingStandard.Variables.DisallowSuperGlobalVariable.DisallowedSuperGlobalVariable
        if (Codes::OK !== $_FILES[$this->formFieldName]['error']) {
            // phpcs:ignore SlevomatCodingStandard.Variables.DisallowSuperGlobalVariable.DisallowedSuperGlobalVariable
            throw new UploadException($_FILES[$this->formFieldName]['error']);
        }
        $this->checkAllowedExtensions();
        $this->fileName = $this->generateUploadedFileName(
            // phpcs:ignore SlevomatCodingStandard.Variables.DisallowSuperGlobalVariable.DisallowedSuperGlobalVariable
            $_FILES[$this->formFieldName]['name'],
            // phpcs:ignore SlevomatCodingStandard.Variables.DisallowSuperGlobalVariable.DisallowedSuperGlobalVariable
            $_FILES[$this->formFieldName]['type'],
        );
        // phpcs:ignore SlevomatCodingStandard.Variables.DisallowSuperGlobalVariable.DisallowedSuperGlobalVariable
        $this->fileMimeType = $_FILES[$this->formFieldName]['type'];
        // phpcs:ignore SlevomatCodingStandard.Variables.DisallowSuperGlobalVariable.DisallowedSuperGlobalVariable
        if (!\move_uploaded_file($_FILES[$this->formFieldName]['tmp_name'], $this->uploadDirectory . $this->fileName)) {
            throw new UploadException(Codes::CANT_WRITE);
        }

        try {
            \chmod($this->uploadDirectory . $this->fileName, 0664);
        } catch (\Throwable $e) {
            // Operation not permitted
        }

        return true;
    }

    /**
    * @param array<int,string> $allowedExtensions
    */
    final public function setAllowedExtensions(array $allowedExtensions): bool
    {
        $this->allowedExtensions = $allowedExtensions;
        return true;
    }

    final public function setFormFieldName(string $formFieldName): bool
    {
        $this->formFieldName = $formFieldName;
        return true;
    }

    final protected function checkAllowedExtensions(): bool
    {
        if (!empty($this->allowedExtensions)) {
            // phpcs:ignore SlevomatCodingStandard.Variables.DisallowSuperGlobalVariable.DisallowedSuperGlobalVariable
            if (!\array_key_exists($_FILES[$this->formFieldName]['type'], $this->allowedExtensions)) {
                throw new UploadException(Codes::TYPE_NOT_ALLOWED);
            }
        }
        return true;
    }
}
