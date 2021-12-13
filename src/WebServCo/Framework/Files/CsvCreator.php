<?php

declare(strict_types=1);

namespace WebServCo\Framework\Files;

use WebServCo\Framework\Exceptions\FileException;

final class CsvCreator
{
    protected string $delimiter;
    protected string $enclosure;

    public function __construct(string $delimiter = ',', string $enclosure = '"')
    {
        $this->delimiter = $delimiter;
        $this->enclosure = $enclosure;
    }

    /**
    * @param array<int,array<mixed>> $data
    */
    public function getCsvFile(string $fileName, array $data, bool $addHeader = true): CsvFile
    {
        $csvData = $this->getCsvData($data, $addHeader);
        return new CsvFile($fileName, $csvData);
    }

    /**
    * @param array<int,array<mixed>> $data
    */
    public function getCsvData(array $data, bool $addHeader = true): string
    {
        if (!$data) {
            return '';
        }
        try {
            // temporary file/memory wrapper; if bigger than 5MB will be written to temp file.
            $handle = \fopen('php://temp/maxmemory:' . (5 * 1024 * 1024), 'r+');

            if (!\is_resource($handle)) {
                throw new FileException('Not a valid resource.');
            }

            // Add Byte Order mark (BOM) for UTF-8.
            \fwrite($handle, \chr(0xEF) . \chr(0xBB) . \chr(0xBF));

            if ($addHeader) {
                $headerData = \current($data);
                if (false !== $headerData) {
                    \fputcsv($handle, \array_keys($headerData), $this->delimiter, $this->enclosure);
                }
            }

            foreach ($data as $item) {
                \fputcsv($handle, $item, $this->delimiter, $this->enclosure);
            }

            \rewind($handle);

            $csvData = (string) \stream_get_contents($handle);

            \fclose($handle);

            return $csvData;
        } catch (\Throwable $e) {
            throw new FileException($e->getMessage());
        }
    }
}
