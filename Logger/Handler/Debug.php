<?php
/**
 * @copyright Copyright (c) 2017 Icyd Colombia (https://www.imaginacolombia.com)
 */

namespace Icyd\Payulatam\Logger\Handler;

use Monolog\Logger;

class Debug extends \Magento\Framework\Logger\Handler\Base
{
    /**
     * @var string
     */
    protected $fileName = '/var/log/payulatam/debug.log';

    /**
     * @var int
     */
    protected $loggerType = Logger::DEBUG;
}
