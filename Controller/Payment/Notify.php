<?php

namespace Icyd\Payulatam\Controller\Payment;

use Magento\Framework\Exception\LocalizedException;

class Notify extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\App\Action\Context
     */
    protected $context;

    /**
     * @var \Icyd\Payulatam\Model\ClientFactory
     */
    protected $clientFactory;

    /**
     * @var \Magento\Framework\Controller\Result\ForwardFactory
     */
    protected $resultForwardFactory;
    protected $resultPageFactory;

    /**
     * @var \Icyd\Payulatam\Logger\Logger
     */
    protected $logger;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Icyd\Payulatam\Model\ClientFactory $clientFactory
     * @param \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory
     * @param \Icyd\Payulatam\Logger\Logger $logger
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Icyd\Payulatam\Model\ClientFactory $clientFactory,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Icyd\Payulatam\Logger\Logger $logger
    ) {
        parent::__construct($context);
        $this->context = $context;
        $this->clientFactory = $clientFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->logger = $logger;
    }

    public function execute()
    {
        throw new \Exception("MAMALO");
        // $resutPage = $this->resultPageFactory->create();
        /**
         * @var $client \Icyd\Payulatam\Model\Client
         */
    //     $request = $this->context->getRequest();
        $this->logger->debug("Content of request");
    //     foreach ($request as $key => $value) {
    //         $this->logger->debug("{$key} => {$value}");
    //     }
    //     try {
    //         $client = $this->clientFactory->create();
    //         $response = $client->orderConsumeNotification($request);
    //         // $this->logger->debug(print_r($response,true));
    //         // throw new \Exception ('Test exception');
    //         $this->logger->debug("Content of response");
    //         foreach ($response as $key => $value) {
    //             $this->logger->debug("{$key} => {$value}");
    //         }
    //         $clientOrderHelper = $client->getOrderHelper();
    //         if ($clientOrderHelper->canProcessNotification($response['referece_sale'])) {
    //             return $clientOrderHelper->processNotification(
    //                 $response['reference_sale'],
    //                 $response['state_pol'],
    //                 $response['value']
    //             );
    //         }
    //     } catch (LocalizedException $e) {
    //         $this->logger->critical($e);
    //     }
    //     /**
    //      * @var $resultForward \Magento\Framework\Controller\Result\Forward
    //      */
        $resultForward = $this->resultForwardFactory->create();
        $resultForward->forward('noroute');
        return $resultForward;
    }
}
