<?php

declare(strict_types=1);

namespace WebServCo\Framework\Files;

use WebServCo\Framework\Exceptions\ApplicationException;
use WebServCo\Framework\Files\CsvFile;

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
    * @param string $fileName
    * @param array<int,array<mixed>> $data
    * @param bool $addHeader
    * @return CsvFile
    */
    public function getCsvFile(string $fileName, array $data, bool $addHeader = true): CsvFile
    {
        if (empty($data)) {
            throw new ApplicationException('Empty data.');
        }
        $csvData = $this->getCsvData($data, $addHeader);
        return new CsvFile($fileName, $csvData);
    }

    /**
    * @param array<int,array<mixed>> $data
    * @param bool $addHeader
    * @return string
    */
    public function getCsvData(array $data, bool $addHeader = true): string
    {
        if (empty($data)) {
            return '';
        }
        try {
            // temporary memory wrapper; if bigger than 5MB will be written to temp file.
            $handle = fopen('php://temp/maxmemory: ' . (5*1024*1024), 'r+');

            if (!is_resource($handle)) {
                throw new ApplicationException('Not a valid resource.');
            }

            if ($addHeader) {
                $headerData = current($data);
                if (false !== $headerData) {
                    fputcsv($handle, array_keys($headerData), $this->delimiter, $this->enclosure);
                }
            }

            foreach ($data as $item) {
                fputcsv($handle, $item, $this->delimiter, $this->enclosure);
            }

            rewind($handle);

            $csvData = (string) stream_get_contents($handle);

            fclose($handle);

            return $csvData;
        } catch (\Exception $e) {
            throw new ApplicationException($e->getMessage());
        }
    }
}
