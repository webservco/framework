<?php

namespace WebServCo\Framework\Files\Upload;

use WebServCo\Framework\Exceptions\UploadException;

abstract class AbstractUpload
{
    protected $allowedExtensions;
    protected $fileName;
    protected $fileMimeType;
    protected $formFieldName;
    protected $uploadDirectory;

    abstract protected function generateUploadedFileName($uploadFileName, $uploadFileMimeType);

    public function __construct($uploadDirectory)
    {
        $this->allowedExtensions = []; // default all
        $this->formFieldName = 'upload';
        $this->uploadDirectory = $uploadDirectory;
    }

    public function getFileName()
    {
        return $this->fileName;
    }

    public function getFileMimeType()
    {
        return $this->fileMimeType;
    }

    final public function do()
    {
        if (empty($_FILES)) {
            return false;
        }
        if (!isset($_FILES[$this->formFieldName]['error'])) {
            throw new UploadException(Codes::NO_FILE);
        }
        if (Codes::OK != $_FILES[$this->formFieldName]['error']) {
            throw new UploadException($_FILES[$this->formFieldName]['error']);
        }
        $this->checkAllowedExtensions();
        $this->fileName = $this->generateUploadedFileName(
            $_FILES[$this->formFieldName]['name'],
            $_FILES[$this->formFieldName]['type']
        );
        $this->fileMimeType = $_FILES[$this->formFieldName]['type'];

        if (!move_uploaded_file($_FILES[$this->formFieldName]['tmp_name'], $this->uploadDirectory . $this->fileName)) {
            throw new UploadException(Codes::CANT_WRITE);
        }

        try {
            chmod($this->uploadDirectory . $this->fileName, 0664);
        } catch (\Exception $e) {
            // Operation not permitted
        }

        return true;
    }

    final public function setAllowedExtensions($allowedExtensions)
    {
        $this->allowedExtensions = $allowedExtensions;
    }

    final public function setFormFieldName($formFieldName)
    {
        $this->formFieldName = $formFieldName;
    }

    final protected function checkAllowedExtensions()
    {
        if (!empty($this->allowedExtensions)) {
            if (!array_key_exists($_FILES[$this->formFieldName]['type'], $this->allowedExtensions)) {
                throw new UploadException(Codes::TYPE_NOT_ALLOWED);
            }
        }
        return true;
    }
}
