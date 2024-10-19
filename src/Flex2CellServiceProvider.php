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
 * @FILE       : Flex2CellServiceProvider.php
 * @CREATED    : 14:41 , 13/Oct/2024
 */

namespace Zuko\Flex2Cell;


/**
 * Class Flex2CellServiceProvider
 *
 * @alias \Zuko\Flex2Cell\Flex2CellServiceProvider \Zuko\FlexExcel\Flex2CellServiceProvider
 * @alias \Zuko\Flex2Cell\Flex2CellServiceProvider \Zuko\FlexToExcel\Flex2CellServiceProvider
 * @alias \Zuko\Flex2Cell\Flex2CellServiceProvider \Zuko\FlexibleExcelExport\Flex2CellServiceProvider
 * @alias \Zuko\Flex2Cell\Flex2CellServiceProvider \Zuko\LaravelExcelExport\Flex2CellServiceProvider
 * @package Zuko\Flex2Cell
 */
class Flex2CellServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register()
    {
        $names = ['LaravelExcelExport', 'Flex2Cell'];
        foreach ($names as $name) {
            $this->app->bind($name, function ($app) {
                return new ExcelExporter();
            });
        }
    }

    public function boot() {}
}
