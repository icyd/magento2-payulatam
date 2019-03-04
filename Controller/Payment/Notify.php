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
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $rawResultFactory;

    /**
     * @var \Icyd\Payulatam\Logger\Logger
     */
    protected $logger;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\Controller\Result\RawFactory $rawResultFactory,
        \Icyd\Payulatam\Model\ClientFactory $clientFactory,
        \Icyd\Payulatam\Logger\Logger $logger
    ) {
        parent::__construct($context);
        $this->context = $context;
        $this->clientFactory = $clientFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->rawResultFactory = $rawResultFactory;
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
        try {
            $data = $this->context->getRequest()->getParams();
            $client = $this->clientFactory->create();
            $clientOrderHelper = $client->getOrderHelper();
            if ($clientOrderHelper->ProcessNotification($data)) {
                $result = $this->rawResultFactory->create();
                $result->setHttpResponseCode(200)->setContents('OK');
                return $result;
            }
        } catch (LocalizedException $e) {
            $this->logger->critical($e);
        }

        $result = $this->resultForwardFactory->create();
        $result->forward('noroute');
        return $result;
    }
}
