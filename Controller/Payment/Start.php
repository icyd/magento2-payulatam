<?php

namespace Icyd\Payulatam\Controller\Payment;

use Magento\Framework\Exception\LocalizedException;

class Start extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Icyd\Payulatam\Model\ClientFactory
     */
    protected $clientFactory;

    /**
     * @var \Icyd\Payulatam\Model\Order
     */
    protected $orderHelper;

    /**
     * @var \Icyd\Payulatam\Model\Session
     */
    protected $session;

    /**
     * @var \Icyd\Payulatam\Logger\Logger
     */
    protected $logger;
    protected $request;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Icyd\Payulatam\Model\ClientFactory $clientFactory
     * @param \Icyd\Payulatam\Model\Order $orderHelper
     * @param \Icyd\Payulatam\Model\Session $session
     * @param \Icyd\Payulatam\Logger\Logger $logger
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\RequestInterface $request,
        \Icyd\Payulatam\Model\ClientFactory $clientFactory,
        \Icyd\Payulatam\Model\Order $orderHelper,
        \Icyd\Payulatam\Model\Session $session,
        \Icyd\Payulatam\Logger\Logger $logger
    ) {
        parent::__construct($context);
        $this->clientFactory = $clientFactory;
        $this->orderHelper = $orderHelper;
        $this->session = $session;
        $this->logger = $logger;
        $this->request =  $request;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        /**
         * @var $clientOrderHelper \Icyd\Payulatam\Model\Client\OrderInterface
         * @var $resultRedirect \Magento\Framework\Controller\Result\Redirect
         */
        $resultRedirect = $this->resultRedirectFactory->create();
        $redirectUrl = 'checkout/cart';
        $redirectParams = [];
        if(is_null($this->request->getParam('repeat'))) {
            $orderId = $this->orderHelper->getOrderIdForPaymentStart();
        } else {
            $orderId = $this->session->getLastOrderId();
        }
        if ($orderId) {
            $order = $this->orderHelper->loadOrderById($orderId);
            if ($this->orderHelper->canStartFirstPayment($order)) {
                try {
                    $client = $this->clientFactory->create();
                    $clientOrderHelper = $client->getOrderHelper();
                    $orderData = $clientOrderHelper->getDataForOrderCreate($order);
                    $result = $client->orderCreate($orderData);
                    $this->orderHelper->addNewOrderTransaction(
                        $order,
                        $result['orderId'],
                        $result['extOrderId'],
                        $clientOrderHelper->getNewStatus()
                    );
                    $this->orderHelper->setNewOrderStatus($order);
                    $configHelper = $client->getConfigHelper();
                    $this->session->setGatewayUrl($configHelper->getConfig('url'));
                    $redirectUrl = $result['redirectUri'];
                } catch (LocalizedException $e) {
                    $this->logger->critical($e);
                    $redirectUrl = 'payulatam/payment/error';
                    $redirectParams = ['exception' => '1'];
                }
                $this->session->setLastOrderId($orderId);
            }
        } else {
            $redirectParams = ['exception' => '1'];
            $redirectUrl = 'payulatam/payment/error';
        }
        $resultRedirect->setPath($redirectUrl, $redirectParams);
        return $resultRedirect;
    }
}
