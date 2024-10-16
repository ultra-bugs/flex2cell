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


use Zuko\Flex2Cell\Contracts\FormatterInterface;

/**
 * Class HasExportAttributes
 *
 * @package Zuko\Flex2Cell\Traits
 */
trait HasExportAttributes
{
    protected $formatters = [];

    /**
     * Set the formatters to be used for formatting values when exporting.
     *
     * $formatters is an associative array, where the keys are the column names
     * and the values are either a callable or an object that implements
     * FormatterInterface. If the value is a string, it will be treated as a class
     * name and an instance of that class will be created.
     *
     * @param array|callable[]|\Zuko\Flex2Cell\Contracts\FormatterInterface[] $formatters An associative array of formatters.
     *
     * @return static
     */
    public function setFormatters(array $formatters)
    {
        foreach ($formatters as $key => $formatter) {
            if (is_string($formatter) && class_exists($formatter) &&(new $formatter()) instanceof FormatterInterface) {
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
     * @param mixed $rowItem The current processing row item
     *
     * @return mixed The formatted value.
     */
    protected function formatValue($mappingKey, $value, $rowItem = null)
    {
        // TODO : support for wildcard keys
        if(@!$mappingKey){
            return $value;
        }
        if (isset($this->formatters[$mappingKey])) {
            if(is_callable($this->formatters[$mappingKey])){

                return call_user_func($this->formatters[$mappingKey], $value, $mappingKey, $rowItem);
            }
            return $this->formatters[$mappingKey];
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
