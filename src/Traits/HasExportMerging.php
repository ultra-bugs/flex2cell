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
 * @FILE       : HasExportMerging.php
 * @CREATED    : 12:52 , 13/Oct/2024
 */

namespace Zuko\Flex2Cell\Traits;


use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

/**
 * Class HasExportMerging
 *
 * @package Zuko\Flex2Cell\Traits
 */
trait HasExportMerging
{
    protected $columnMergeRules = [];
    protected $rowMergeRules    = [];

    public function setColumnMergeRules(array $rules)
    {
        foreach ($rules as &$rule) {
            if (isset($rule['start']) && !ctype_alpha($rule['start'])) {
                $rule['start'] = $this->getColumnLetter($rule['start']);
            }
            if (isset($rule['end']) && !ctype_alpha($rule['end'])) {
                $rule['end'] = $this->getColumnLetter($rule['end']);
            }
            $rule['shiftDown'] = $rule['shiftDown'] ?? false;
        }
        $this->columnMergeRules = $rules;

        return $this;
    }

    public function setRowMergeRules(array $rules)
    {
        foreach ($rules as $key => $rule) {
            if (is_numeric($key)) {
                $this->rowMergeRules[$this->getColumnLetter($rule)] = ['field' => $rule];
            } else {
                $this->rowMergeRules[$key] = $rule;
            }
        }

        return $this;
    }
    protected function applyMerging($sheet)
    {
        $this->applyColumnMerging($sheet);
        $this->applyRowMerging($sheet);
    }
    protected function applyColumnMerging($sheet)
    {
        $mergedRanges = [];
        $hasShifted = false;
        $targetRow = $this->headerRowIndex;
        foreach ($this->columnMergeRules as $rule) {
            $startColumn = $rule['start'];
            $endColumn = $rule['end'];
            if ($rule['shiftDown'] ?? false) {
                // Insert a new row above the current header row
                if(!$hasShifted) {
                    $sheet->insertNewRowBefore($this->headerRowIndex);
                    $this->headerRowIndex++;
                    $targetRow = ($this->headerRowIndex - 1);
                    $hasShifted = true;
                }
                // Set the merged header value
                $sheet->setCellValue($startColumn . $targetRow, $rule['label']);
                // Merge the cells
                $sheet->mergeCells($startColumn . $targetRow . ':' . $endColumn . $targetRow);
                $startColumn = Coordinate::columnIndexFromString($startColumn);
                $endColumn = Coordinate::columnIndexFromString($endColumn);
                if(!in_array($ranges = range($startColumn, $endColumn), $mergedRanges, true)){
                    $mergedRanges[] = $ranges;
                }
            } else {
                // If not shifting down, merge at the current header row
                $sheet->mergeCells($startColumn . $this->headerRowIndex . ':' . $endColumn . $this->headerRowIndex);
                $sheet->setCellValue($startColumn . $this->headerRowIndex, $rule['label']);
            }
        }
        if($hasShifted){
            $flat = array_merge(...$mergedRanges);
            foreach ($this->headers as $index => $header) {
                if(!in_array($index, $flat, true)){
                    $curLetter = Coordinate::stringFromColumnIndex($index);
                    $sheet->mergeCells($curLetter . $this->headerRowIndex . ':' . $curLetter . ($this->headerRowIndex - 1));
                }
            }
        }
    }

    protected function applyRowMerging($sheet)
    {
        $lastRow = $sheet->getHighestRow();
        foreach ($this->rowMergeRules as $column => $rule) {
            $columnLetter = ctype_alpha($column) ? $column : $this->getColumnLetter($rule['field']);
            $startRow = 2;
            $currentValue = null;
            for ($row = $startRow;$row <= $lastRow;$row++) {
                $cellValue = $sheet->getCell($columnLetter . $row)->getValue();
                if ($cellValue !== $currentValue) {
                    if ($currentValue !== null) {
                        $sheet->mergeCells($columnLetter . $startRow . ':' . $columnLetter . ($row - 1));
                    }
                    $currentValue = $cellValue;
                    $startRow = $row;
                }
            }
            if ($startRow < $lastRow) {
                $sheet->mergeCells($columnLetter . $startRow . ':' . $columnLetter . $lastRow);
            }
        }
    }
}
