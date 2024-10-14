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

namespace Zuko\Flex2Cell;


use Zuko\Flex2Cell\Traits\ExcelExportable;

/**
 * Class ExcelExporter
 *
 * @package Zuko\Flex2Cell
 */
class ExcelExporter
{
    use ExcelExportable {
        export as private exportExcel;
    }

    /**
     * @param array|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection $data
     * @param string                                                                        $filename
     * @param array                                                                         $options
     *
     * @return bool
     */
    public static function export($data, $filename, array $options = [])
    {
// Example usage:
// Quick export
// Example usage:
//        ExcelExporter::export($data, 'export.xlsx', [
//            'headers' => ['#', 'Name', 'Product Group', 'Owner Name', 'Business Type', 'District', 'Commune'],
//            'mapping' => [
//                'id' => '#',
//                'name' => 'Name',
//                'product_group' => 'Product Group',
//                'owner_name' => 'Owner Name',
//                'owner_business_type' => 'Business Type',
//                'district.name' => 'District',
//                'village.name' => 'Commune',
//            ],
//            'formatters' => [
//                'owner_business_type' => new BusinessTypeFormatter(),
//                'district.name' => 'FullyQualifiedFormatterClassName',
//            ],
//            'columnMergeRules' => [
//                ['start' => 'district.name', 'end' => 'village.name', 'label' => 'Address', 'shiftDown' => true]
//            ],
//            'rowMergeRules' => [
//                'product_group', 'owner_name', 'owner_business_type'
//            ]
//        ]);
// Or using the fluent interface
//        ExcelExporter::make()
//                     ->setData($data)
//                     ->setHeaders(['ID', 'Name', 'Product Area', 'Nursery Area', 'Category'])
//                     ->setMapping([
//                                      'id' => 'ID',
//                                      'name' => 'Name',
//                                      'product_area' => 'Product Area',
//                                      'nursery_area' => 'Nursery Area',
//                                      'category.name' => 'Category'
//                                  ])
//                     ->setFormatters([
//                                         'Category' => fn($value) => ucfirst($value)
//                                     ])
//                     ->setColumnMergeRules([
//                                               ['start' => 'C', 'end' => 'D', 'label' => 'AREA']
//                                           ])
//                     ->setRowMergeRules([
//                                            'E' => ['field' => 'category.name']
//                                        ])
//                     ->export('export.xlsx');
        return static::make()
                     ->setData($data)
                     ->setHeaders($options['headers'] ?? array_values($options['mapping'] ?? []))
                     ->setMapping($options['mapping'] ?? array_combine(array_keys(self::first($data)), array_keys(self::first($data))))
                     ->setFormatters($options['formatters'] ?? [])
                     ->setColumnMergeRules($options['columnMergeRules'] ?? [])
                     ->setRowMergeRules($options['rowMergeRules'] ?? [])
                     ->exportExcel($filename);
    }

    public static function make()
    {
        return new static;
    }

}
