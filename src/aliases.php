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
 * @FILE       : aliases.php
 * @CREATED    : 14:08 , 13/Oct/2024
 */
(static function () {
    $classes = [
        \Zuko\Flex2Cell\ExcelExporter::class,
        \Zuko\Flex2Cell\Flex2CellServiceProvider::class,
    ];
    $namespaces = [
        'FlexExcel',
        'FlexToExcel',
        'FlexibleExcelExport',
        'LaravelExcelExport',
    ];

    foreach ($classes as $originalClass) {
        foreach ($namespaces as $namespace) {
            $aliasClass = str_replace('Flex2Cell', $namespace, $originalClass);
            class_alias($originalClass, $aliasClass);

            /**
             * @alias $aliasClass
             */
        }
    }
})();
