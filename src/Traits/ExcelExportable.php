<?php
/*
 *          M""""""""`M            dP
 *          Mmmmmm   .M            88
 *          MMMMP  .MMM  dP    dP  88  .dP   .d8888b.
 *          MMP  .MMMMM  88    88  88888"    88'  `88
 *          M' .MMMMMMM  88.  .88  88  `8b.  88.  .88
 *          M         M  `88888P'  dP   `YP  `88888P'
 *          MMMMMMMMMMM    -*-  Created by Zuko  -*-
 *
 *          * * * * * * * * * * * * * * * * * * * * *
 *          * -    - -   F.R.E.E.M.I.N.D   - -    - *
 *          * -  Copyright © 2024 (Z) Programing  - *
 *          *    -  -  All Rights Reserved  -  -    *
 *          * * * * * * * * * * * * * * * * * * * * *
 */

/**
 * --------------------------------------------------------------------------
 *
 * --------------------------------------------------------------------------
 * @PROJECT    : Flex2Cell | Zuko®
 * @AUTHOR     : Zuko <https://github.com/tansautn>
 * @LINK       : https://www.zuko.pro/
 * @FILE       : ExcelExportable.php
 * @CREATED    : 12:51 , 13/Oct/2024
 */

namespace Zuko\Flex2Cell\Traits;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Class ExcelExportable
 *
 * @package Zuko\Flex2Cell\Traits
 */
trait ExcelExportable
{
    use HasExportAttributes {
        HasExportAttributes::formatValue as parentFormat;
    }
    use HasExportMerging;

    protected $data;
    protected $headers      = [];
    protected $subHeaders   = [];
    protected $mapping      = [];
    protected $hiddens      = [];
    protected $metaSettings = [];
    protected $chunkSize    = 1000;
    protected $appendMode   = false;
    /**
     * @var bool
     */
    protected $skipHeader = false;
    /**
     * @var array
     */
    protected $columnLetters = [];

    /**
     * @param $data
     *
     * @return static
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Set the headers to be displayed on the first row of the export.
     *
     * @param array $headers
     *
     * @return static
     */
    public function setHeaders(array $headers)
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * Set the sub headers to be displayed on the second row of the export.
     *
     * @param array $subHeaders
     *
     * @return static
     */
    public function setSubHeaders(array $subHeaders)
    {
        $this->subHeaders = $subHeaders;

        return $this;
    }


    /**
     * Set the mapping of header => field names.
     *
     * @param array $mapping
     *
     * @return static
     */
    public function setMapping(array $mapping)
    {
        $this->mapping = $mapping;

        return $this;
    }

    /**
     * Set the fields which should not appear in the export.
     *
     * @param array $hiddens
     *
     * @return static
     */
    public function setHiddens(array $hiddens)
    {
        $this->hiddens = $hiddens;

        return $this;
    }

    /**
     * Set the meta settings of the export.
     *
     * The meta settings are written to the spreadsheet properties.
     *
     * Supported keys:
     * - `author`
     * - `title`
     *
     * @param array $metaSettings
     *
     * @return static
     */
    public function setMetaSettings(array $metaSettings)
    {
        $this->metaSettings = $metaSettings;

        return $this;
    }

    /**
     * Set the number of data rows to be processed in each chunk.
     *
     * When exporting large datasets, processing the data in chunks can help
     * reduce the memory footprint of the export process. The chunk size is
     * the number of data rows that are processed at a time.
     *
     * @param int $chunkSize The number of data rows to be processed in each chunk.
     *
     * @return static
     */
    public function setChunkSize(int $chunkSize)
    {
        $this->chunkSize = $chunkSize;

        return $this;
    }

    /**
     * Set whether the export should append to an existing file or replace the file.
     *
     * @param bool $appendMode If true, the export will append to an existing file.
     * If false, the export will replace the file.
     *
     * @return static
     */
    public function setAppendMode(bool $appendMode)
    {
        $this->appendMode = $appendMode;

        return $this;
    }

    /**
     * Set whether the export should write the header row or not.
     *
     * The header row is the first row of the spreadsheet and is used to
     * label the columns. If this is set to true, the export will skip
     * writing the header row.
     *
     * @param bool $skipHeader If true, the export will skip writing the header row.
     *
     * @return static
     */
    public function setSkipHeader(bool $skipHeader)
    {
        $this->skipHeader = $skipHeader;

        return $this;
    }

    /**
     * Export the data to an Excel file.
     *
     * @param string $filename The file name to export to.
     *
     * @return bool status of export based PHP Office reader load() result
     */
    public function export(string $filename)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        if (!$this->appendMode || !$this->skipHeader) {
            $this->writeHeaders($sheet);
        }
        $rowIndex = $this->appendMode ? $sheet->getHighestRow() + 1 : 3; // Start from row 3 due to double header
        if ($this->data instanceof Collection || $this->data instanceof Model) {
            $this->data = $this->data->toArray();
        }
        foreach (array_chunk($this->data, $this->chunkSize) as $chunk) {
            foreach ($chunk as $row) {
                $this->writeRow($sheet, $row, $rowIndex++);
            }
        }
        $this->applyMerging($sheet);
        $this->applyMetaSettings($spreadsheet);
        // get extension
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $extension = strtolower($extension);
        if ($extension === 'xls') {
            $writer = new Xls($spreadsheet);
            $validator = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
        } else {
            $writer = new Xlsx($spreadsheet);
            $validator = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        }
        $writer->save($filename);
        // validate output file using phpoffice
        try {
            $validator->load($filename);
            return true;
        } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
            return false;
        }
    }

    /**
     * Write the headers to the Excel sheet.
     *
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet The worksheet to write the headers to.
     *
     * @return void
     */
    protected function writeHeaders($sheet)
    {
        $columnIndex = 1;
        foreach ($this->headers as $header) {
            if (!in_array($header, $this->hiddens, true)) {
                $sheet->setCellValue([$columnIndex, 1], $this->getHeader($header));
                if (isset($this->subHeaders[$header])) {
                    $sheet->setCellValue([$columnIndex, 2], $this->getSubHeader($header));
                }
                $columnIndex++;
            }
        }
    }

    /**
     * Get a header value from the headers array.
     *
     * If the header does not exist, the value passed as an argument is returned.
     *
     * @param string $header The header for which to get the value
     *
     * @return string The header value
     */
    protected function getHeader($header)
    {
        return $header;
    }

    /**
     * Get a sub-header value from the sub headers array.
     *
     * If the sub-header does not exist, an empty string is returned.
     *
     * @param string $header The header for which to get the sub-header value
     *
     * @return string The sub-header value
     */
    protected function getSubHeader($header)
    {
        return $this->subHeaders[$header] ?? '';
    }

    /**
     * Write a single row of data to the spreadsheet.
     *
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet The worksheet to write the row to.
     * @param array                                         $row The row of data to write.
     * @param int                                           $rowIndex The index of the row to write.
     *
     * @return void
     */
    protected function writeRow($sheet, $row, $rowIndex)
    {
        $columnIndex = 1;
        foreach ($this->mapping as $key => $header) {
            if (!in_array($header, $this->hiddens, true)) {
                $value = $this->getValue($row, $key);
                $value = $this->formatValue($header, $value);
                $sheet->setCellValue([$columnIndex++, $rowIndex], $value);
            }
        }
    }

    /**
     * Get a value from the data row for export.
     *
     * This method is called once for each value that is exported.
     *
     * @param array  $row
     * @param string $key
     *
     * @return mixed
     */
    protected function getValue($row, $key)
    {
        return self::dataGet($row, $key);
    }

    /**
     * Get an item from an array or object using "dot" notation.
     *
     * @param mixed                 $target
     * @param string|array|int|null $key
     * @param mixed                 $default
     *
     * @return mixed
     */
    private static function dataGet($target, $key, $default = null)
    {
        if (is_null($key)) {
            return $target;
        }
        $key = is_array($key) ? $key : explode('.', $key);
        foreach ($key as $segment) {
            if (is_array($target) && array_key_exists($segment, $target)) {
                $target = $target[$segment];
            } elseif (is_object($target) && isset($target->{$segment})) {
                $target = $target->{$segment};
            } else {
                return $default;
            }
        }

        return $target;
    }

    /**
     * Format a value for export.
     *
     * This method is called once for each value that is exported.
     * The default implementation simply returns the value as is,
     * but you can override this method in your class to change the
     * behavior.
     *
     * @param string $mappingKey The key of the mapped column that is being exported.
     * @param mixed  $value The value that is being exported.
     *
     * @return mixed The formatted value.
     */
    protected function formatValue($mappingKey, $value)
    {
        return $this->parentFormat($mappingKey, $value);
    }

    /**
     * Apply the meta settings to the spreadsheet.
     *
     * The meta settings are applied to the spreadsheet properties. The
     * supported meta settings are:
     *
     * - `author`: The author of the spreadsheet.
     * - `title`: The title of the spreadsheet.
     *
     * @param Spreadsheet $spreadsheet The spreadsheet to apply the meta
     * settings to.
     *
     * @return void
     */
    protected function applyMetaSettings($spreadsheet)
    {
        if (isset($this->metaSettings['author'])) {
            $spreadsheet->getProperties()->setCreator($this->metaSettings['author']);
        }
        if (isset($this->metaSettings['title'])) {
            $spreadsheet->getProperties()->setTitle($this->metaSettings['title']);
        }
        // more meta settings as needed in the future
    }

    /**
     * Get the column letter for a mapping key.
     *
     * @param string $mappingKey
     *
     * @return string
     */
    protected function getColumnLetter($mappingKey)
    {
        if (!isset($this->columnLetters[$mappingKey])) {
            $index = array_search($mappingKey, array_keys($this->mapping));
            $this->columnLetters[$mappingKey] = Coordinate::stringFromColumnIndex($index + 1);
        }

        return $this->columnLetters[$mappingKey];
    }

    /**
     * Get the mapping key from a header.
     *
     * @param string $header The header
     *
     * @return string The mapping key if found, null otherwise
     */
    protected function getMappingKeyFromHeader($header)
    {
        return array_search($header, $this->mapping, true);
    }

    /**
     * Get a header from a mapping key.
     *
     * @param string $mappingKey The mapping key
     *
     * @return string The header if found, null otherwise
     */
    protected function getHeaderFromMappingKey($mappingKey)
    {
        return $this->mapping[$mappingKey] ?? null;
    }

    private static function first(array $array, $callback = null, $default = null) {
        if (is_null($callback)) {
            if (empty($array)) {
                return value($default);
            }

            foreach ($array as $item) {
                return $item;
            }
        }

        foreach ($array as $key => $value) {
            if ($callback($value, $key)) {
                return $value;
            }
        }

        return value($default);
    }
}
