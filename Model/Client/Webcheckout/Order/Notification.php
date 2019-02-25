<?php

namespace Icyd\Payulatam\Model\Client\Webcheckout\Order;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;

class Notification
{
    /**
     * @var \Icyd\Payulatam\Model\Client\Webcheckout\Config
     */
    protected $configHelper;

    /**
     * @param \Icyd\Payulatam\Model\Client\Webcheckout\Config $configHelper
     */
    public function __construct(
        \Icyd\Payulatam\Model\Client\Webcheckout\Config $configHelper
    ) {
        $this->configHelper = $configHelper;
    }

    public function getPayuplOrderId($request)
    {
        // if (!$request->isPost()) {
        //     throw new LocalizedException(new Phrase('POST request is required.'));
        // }
        $sig = $request->getParam('sign');
        $newValue = $this->getNewValue($request->getParam('value'));
        $currency = $request->getParam('currency');
        $state = $request->getParam('state_pol');
        $menchantId = $request->getParam('merchant_id');
        $reference = $request->getParam('reference_sale');
        $ApiKey = $this->configHelper->getConfig('ApiKey');
        if (md5($ApiKey . $menchantId . $reference . $newValue . $currency . $state) === $sig) {
            return $reference;
        }
        throw new LocalizedException(new Phrase('Invalid Signature.'));
    }

    public function getNewValue($value)
    {
        $pattern = '/(\d+(.|,)\d)0$/';
        return preg_replace($pattern, '\1', $value);
    }
}
