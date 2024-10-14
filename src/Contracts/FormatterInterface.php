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
 * @FILE       : FormatterInterface.php
 * @CREATED    : 19:57 , 13/Oct/2024
 */

namespace Zuko\Flex2Cell\Contracts;


/**
 * Class FormatterInterface
 *
 * @package Zuko\Flex2Cell\Contracts
 */
interface FormatterInterface
{
    /**
     * Format a value for export.
     *
     * This method is called once for each cell value that is exported.
     * The default implementation simply returns the value as is,
     * but you can override this method in your class to change the
     * behavior.
     *
     * @param mixed  $value The value that is being exported.
     * @param string $mappingKey The key of the mapped column that is being exported.
     *
     * @return mixed The formatted value.
     */
    public function formatValue($value, $mappingKey);
}
