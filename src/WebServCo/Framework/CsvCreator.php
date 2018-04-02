<?php
namespace WebServCo\Framework;

final class CsvCreator
{
    protected $delimiter;
    protected $enclosure;

    public function __construct($delimiter = ',', $enclosure = '"')
    {
        $this->delimiter = $delimiter;
        $this->enclosure = $enclosure;
    }

    public function getCsvFile(string $fileName, array $data, bool $addHeader = true)
    {
        $csvData = $this->getCsvData($data, $addHeader);
        return new \WebServCo\Framework\CsvFile($fileName, $csvData);
    }

    public function getCsvData(array $data, bool $addHeader = true)
    {
        try {
            // temporary memory wrapper; if bigger than 5MB will be written to temp file.
            $handle = fopen('php://temp/maxmemory: ' . (5*1024*1024), 'r+');

            if ($addHeader) {
                fputcsv($handle, array_keys(current($data)), $this->delimiter, $this->enclosure);
            }

            foreach ($data as $item) {
                fputcsv($handle, $item, $this->delimiter, $this->enclosure);
            }

            rewind($handle);

            $csvData = stream_get_contents($handle);

            fclose($handle);

            return $csvData;
        } catch (\Exception $e) {
            throw new \WebServCo\Framework\Exceptions\ApplicationException($e->getMessage());
        }
    }
}
