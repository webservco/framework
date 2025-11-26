<?php

declare(strict_types=1);

namespace WebServCo\Framework\Files;

use Throwable;
use WebServCo\Framework\Exceptions\FileException;

use function array_keys;
use function chr;
use function current;
use function fclose;
use function fopen;
use function fputcsv;
use function fwrite;
use function is_resource;
use function rewind;
use function stream_get_contents;

final class CsvCreator
{
    /**
     * Warning
     * When escape is set to anything other than an empty string ("") it can result in CSV that is
     * not compliant with » RFC 4180 or unable to survive a roundtrip through the PHP CSV functions.
     * The default for escape is "\\" so it is recommended to set it to the empty string explicitly.
     * The default value will change in a future version of PHP, no earlier than PHP 9.0.
     */
    public function __construct(
        protected string $delimiter = ',',
        protected string $enclosure = '"',
        protected string $escape = '',
    ) {
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
            $handle = fopen('php://temp/maxmemory:' . (5 * 1024 * 1024), 'r+');

            if (!is_resource($handle)) {
                throw new FileException('Not a valid resource.');
            }

            // Add Byte Order mark (BOM) for UTF-8.
            fwrite($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            if ($addHeader) {
                $headerData = current($data);
                if ($headerData !== false) {
                    fputcsv($handle, array_keys($headerData), $this->delimiter, $this->enclosure, $this->escape);
                }
            }

            foreach ($data as $item) {
                fputcsv($handle, $item, $this->delimiter, $this->enclosure, $this->escape);
            }

            rewind($handle);

            $csvData = (string) stream_get_contents($handle);

            fclose($handle);

            return $csvData;
        } catch (Throwable $e) {
            throw new FileException($e->getMessage());
        }
    }
}
