<?php

namespace Icyd\Payulatam\Controller\Payment;

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

    /**
     * @var \Icyd\Payulatam\Logger\Logger
     */
    protected $logger;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Icyd\Payulatam\Model\ClientFactory $clientFactory,
        \Icyd\Payulatam\Logger\Logger $logger
    ) {
        parent::__construct($context);
        $this->context = $context;
        $this->clientFactory = $clientFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->logger = $logger;
         // CsrfAwareAction Magento2.3 compatibility
        if (interface_exists("\Magento\Framework\App\CsrfAwareActionInterface")) {
            $request = $this->context->getRequest();
            if ($request->isPost() && empty($request->getParam('form_key'))) {
                $formKey = $this->_objectManager->get(\Magento\Framework\Data\Form\FormKey::class);
                $request->setParam('form_key', $formKey->getFormKey());
            }
        }
    }

    public function execute()
    {
        $request = $this->context->getRequest();
        $params = $request->getPost();
        $this->logger->debug("Debug POST Resquest");
        foreach ($params as $key => $value) {
            $this->logger->debug("{$key} => {$value}");
        }
        $resultForward = $this->resultForwardFactory->create();
        $resultForward->forward('noroute');
        return $resultForward;
    }
}
//         /**
//          * @var $client \Icyd\Payulatam\Model\Client
//          */
//     //     $request = $this->context->getRequest();
//         $this->logger->debug("Content of request");
//     //     foreach ($request as $key => $value) {
//     //         $this->logger->debug("{$key} => {$value}");
//     //     }
//     //     try {
//     //         $client = $this->clientFactory->create();
//     //         $response = $client->orderConsumeNotification($request);
//     //         // $this->logger->debug(print_r($response,true));
//     //         // throw new \Exception ('Test exception');
//     //         $this->logger->debug("Content of response");
//     //         foreach ($response as $key => $value) {
//     //             $this->logger->debug("{$key} => {$value}");
//     //         }
//     //         $clientOrderHelper = $client->getOrderHelper();
//     //         if ($clientOrderHelper->canProcessNotification($response['referece_sale'])) {
//     //             return $clientOrderHelper->processNotification(
//     //                 $response['reference_sale'],
//     //                 $response['state_pol'],
//     //                 $response['value']
//     //             );
//     //         }
//     //     } catch (LocalizedException $e) {
//     //         $this->logger->critical($e);
//     //     }
//     //     /**
//     //      * @var $resultForward \Magento\Framework\Controller\Result\Forward
//     //      */
//     }
// }
