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
 * @FILE       : HasExportAttributes.php
 * @CREATED    : 12:51 , 13/Oct/2024
 */

namespace Zuko\Flex2Cell\Traits;


/**
 * Class HasExportAttributes
 *
 * @package App\Traits\ExcelExport
 */
trait HasExportAttributes
{
    protected $formatters = [];

    public function setFormatters(array $formatters)
    {
        foreach ($formatters as $key => $formatter) {
            if (is_string($formatter) && class_exists($formatter)) {
                $formatter = new $formatter();
            }
            if (is_object($formatter) && method_exists($formatter, 'formatValue')) {
                $this->formatters[$key] = [$formatter, 'formatValue'];
            } else {
                $this->formatters[$key] = $formatter;
            }
        }
        return $this;
    }

    protected function formatValue($mappingKey, $value)
    {
        if (isset($this->formatters[$mappingKey])) {
            return call_user_func($this->formatters[$mappingKey], $value);
        }
        if (method_exists($this, 'format' . str_replace('.', '', ucwords($mappingKey, '.')) . 'Attribute')) {
            return $this->{'format' . str_replace('.', '', ucwords($mappingKey, '.')) . 'Attribute'}($value);
        }
        return $value;
    }

    // Example of a custom formatter method
    // protected function formatProductNameAttribute($value)
    // {
    //     return strtoupper($value);
    // }
}
