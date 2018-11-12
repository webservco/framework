<?php
namespace WebServCo\Framework\Interfaces;

interface FileInterface
{
    public function getContentType();
    public function getDownloadResponse();
    public function getFileData();
    public function getFileName();
    public function getOutputResponse();
}
