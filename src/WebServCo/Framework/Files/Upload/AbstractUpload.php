<?php

declare(strict_types=1);

namespace WebServCo\Framework\Files\Upload;

use Throwable;
use WebServCo\Framework\Exceptions\UploadException;

use function array_key_exists;
use function chmod;
use function move_uploaded_file;

/**
 * Initial functionality: simple upload.
 */
abstract class AbstractUpload
{
    /**
    * Allowed extensions.
    * mime / extension
    *
    * @var array<string,string>
    */
    protected array $allowedExtensions;
    protected string $fileName;
    protected string $fileMimeType;
    protected string $formFieldName;

    final public function do(): bool
    {
        if (!$_FILES) {
            return false;
        }

        if (!isset($_FILES[$this->formFieldName]['error'])) {
            throw new UploadException(Codes::NO_FILE);
        }

        if ($_FILES[$this->formFieldName]['error'] !== Codes::OK) {
            throw new UploadException($_FILES[$this->formFieldName]['error']);
        }
        $this->checkAllowedExtensions();
        $this->fileName = $this->generateUploadedFileName(
            $_FILES[$this->formFieldName]['name'],
            $_FILES[$this->formFieldName]['type'],
        );

        $this->fileMimeType = $_FILES[$this->formFieldName]['type'];

        if (!move_uploaded_file($_FILES[$this->formFieldName]['tmp_name'], $this->uploadDirectory . $this->fileName)) {
            throw new UploadException(Codes::CANT_WRITE);
        }

        try {
            chmod($this->uploadDirectory . $this->fileName, 0664);
        } catch (Throwable) {
            // Operation not permitted
        }

        return true;
    }

    /**
    * @param array<string,string> $allowedExtensions
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
        if ($this->allowedExtensions) {
            if (!array_key_exists($_FILES[$this->formFieldName]['type'], $this->allowedExtensions)) {
                throw new UploadException(Codes::TYPE_NOT_ALLOWED);
            }
        }

        return true;
    }

    abstract protected function generateUploadedFileName(string $uploadFileName, string $uploadFileMimeType): string;

    public function __construct(protected string $uploadDirectory)
    {
        // default all
        $this->allowedExtensions = [];
        $this->formFieldName = 'upload';
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getFileMimeType(): string
    {
        return $this->fileMimeType;
    }
}
