<?php

namespace Icyd\Payulatam\Model\Client\Webcheckout\Order;

class DataGetter
{
    /**
     * @var \Icyd\Payulatam\Model\Order\ExtOrderId
     */
    protected $extOrderIdHelper;

    /**
     * @var \Icyd\Payulatam\Model\Client\Webcheckout\Config
     */
    protected $configHelper;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $dateTime;

    /**
     * @var \Icyd\Payulatam\Model\Session
     */
    protected $session;

    /**
     * @param \Icyd\Payulatam\Model\Order\ExtOrderId $extOrderIdHelper
     * @param \Icyd\Payulatam\Model\Client\Webcheckout\Config $configHelper
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param \Icyd\Payulatam\Model\Session $session
     */
    public function __construct(
        \Icyd\Payulatam\Model\Order\ExtOrderId $extOrderIdHelper,
        \Icyd\Payulatam\Model\Client\Webcheckout\Config $configHelper,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Icyd\Payulatam\Model\Session $session,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->extOrderIdHelper = $extOrderIdHelper;
        $this->configHelper = $configHelper;
        $this->dateTime = $dateTime;
        $this->session = $session;
        $this->storeManager = $storeManager;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return array
     */
    public function getBasicData(\Magento\Sales\Model\Order $order)
    {
        $incrementId = $order->getIncrementId();
        $billingAddress = $order->getBillingAddress();
        $address = $order->getShippingAddress();

        $taxReturnBase = number_format(($order->getGrandTotal() - $order->getTaxAmount()),2,'.','');
        if($order->getTaxAmount() == 0) $taxReturnBase = 0;

        $data = [
            'amount' => number_format($order->getGrandTotal(),2,'.',''),
            'description' => __('Order # %1', [$incrementId]) . " ",
            'extra1' => $incrementId,
            'extra2' => 'Icyd_Payulatam_M2',
            'buyerFullName' => $billingAddress->getFirstname(). ' '.$billingAddress->getLastname(),
            'buyerEmail' => $order->getCustomerEmail(),
            'referenceCode' => $this->extOrderIdHelper->generate($order),
            'currency' => $order->getOrderCurrencyCode(),
            'tax' => number_format($order->getTaxAmount(),2,'.',''),
            'taxReturnBase' => $taxReturnBase,
            'shippingAddress' => implode(",", $address->getStreet()),
            'shippingCity' => $address->getCity(),
            'shippingCountry' => $address->getCountryId(),
            'mobilePhone' => $billingAddress->getTelephone(),
            'telephone' => $billingAddress->getTelephone(),
            'responseUrl' => $this->storeManager->getstore()->getUrl('payulatam/payment/end'),
            'confirmationUrl' => $this->storeManager->getstore()->getUrl('payulatam/payment/notify')
        ];

        return $data;
    }

    /**
     * @return string
     */
    public function getMerchantId()
    {
        if ($this->getTestMode()) {
            return '508029';
        }

        return $this->configHelper->getConfig('merchantId');
    }

    /**
     * @return string
     */
    public function getAccountId()
    {
        if ($this->getTestMode()) {
            return $this->getCountry();
        }

        return $this->configHelper->getConfig('accountId');
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        if ($this->getTestMode()) {
            return '4Vj8eK4rloUd272L48hsrarnUA';
        }

        return $this->configHelper->getConfig('ApiKey');
    }

    /**
     * @return string
     */
    public function getApiLogin()
    {
        if ($this->getTestMode()) {
            return 'pRRXKOl8ikMmt9u';
        }

        return $this->configHelper->getConfig('ApiLogin');
    }

    /**
     * @return string
     */
    public function getTestMode()
    {
        return $this->configHelper->getConfig('test');
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->configHelper->getConfig('country');
    }

    /**
     * @return string
     */
    public function getClientIp()
    {
        return $_SERVER['REMOTE_ADDR'];
    }

    /**
     * @return int
     */
    public function getTs()
    {
        return $this->dateTime->timestamp();
    }

    /**
     * @param array $data
     * @return string
     */
    public function getSigForOrderCreate(array $data = [])
    {
        //Signature Format
        //“ApiKey~merchantId~referenceCode~amount~currency”.

        return md5(
            $this->getApiKey()."~".
            $data['merchantId'] ."~".
            $data['referenceCode'] ."~".
            $data['amount']."~".
            $data['currency']
        );
    }

    /**
     * @param array $data
     * @return string
     */
    public function getSigForOrderRetrieve(array $data = [])
    {
        return md5(
            $this->getApiKey()."~".
            $data['merchant_id']."~".
            $data['reference_sale']."~".
            $this->processValue($data['value'])."~".
            $data['currency']."~".
            $data['state_pol']
        );
    }

    public function processValue($value)
    {
        $pattern = '/(\d+(.|,)\d)0$/';
        return preg_replace($pattern, '\1', $value);
    }
}
