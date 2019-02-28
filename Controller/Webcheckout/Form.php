<?php

namespace Icyd\Payulatam\Controller\Webcheckout;

class Form extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Icyd\Payulatam\Model\Session
     */
    protected $session;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Icyd\Payulatam\Model\Session $session
     */
    protected $logger;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Icyd\Payulatam\Model\Session $session,
        \Icyd\Payulatam\Logger\Logger $logger
    ) {
        parent::__construct($context);
        $this->session = $session;
        $this->resultPageFactory = $resultPageFactory;
        $this->logger = $logger;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        /**
         * @var $resultRedirect \Magento\Framework\Controller\Result\Redirect
         * @var $resultPage \Magento\Framework\View\Result\Page
         */
        $orderCreateData = $this->session->getOrderCreateData();
        $gatewayUrl = $this->session->getGatewayUrl();

        if ($orderCreateData) {
            $this->session->setOrderCreateData(null);
            $resultPage = $this->resultPageFactory->create(true, ['template' => 'Icyd_Payulatam::emptyroot.phtml']);
            $resultPage->addHandle($resultPage->getDefaultLayoutHandle());
            $resultPage->getLayout()->getBlock('payulatam.webcheckout.form')->setOrderCreateData($orderCreateData);
            $resultPage->getLayout()->getBlock('payulatam.webcheckout.form')->setGatewayUrl($gatewayUrl);
            return $resultPage;
        } else {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('/');
            return $resultRedirect;
        }
    }
}
