<?php

namespace Icyd\Payulatam\Controller\Payment;

// class Error extends \Magento\Checkout\Controller\Onepage\Success
// {
//     public function execute()
//     {
//         $session = $this->getOnepage()->getCheckout();
//         if (!$this->_objectManager->get('Magento\Checkout\Model\Session\SuccessValidator')->isValid()) {
//             return $this->resultRedirectFactory->create()->setPath('checkout/cart');
//         }
//         $session->clearQuote();
//         //@todo: Refactor it to match CQRS
//         $resultPage = $this->resultPageFactory->create();
//         $this->_eventManager->dispatch(
//             'checkout_onepage_controller_success_action',
//             ['order_ids' => [$session->getLastOrderId()]]
//         );
//         return $resultPage;
//     }
// }

    class Error extends \Magento\Framework\App\Action\Action
    {
        protected $_pageFactory;
        protected $request;

        public function __construct(
            \Magento\Framework\App\Action\Context $context,
            \Magento\Framework\App\RequestInterface $request,
            \Magento\Framework\View\Result\PageFactory $pageFactory
        )
        {
            $this->_pageFactory = $pageFactory;
            $this->request = $request;
            return parent::__construct($context);
        }

        public function execute()
        {
            $resultPage = $this->_pageFactory->create();
            $resultPage->getConfig()->getTitle()->set(__('Error durante pago'));
            if(!is_null($this->request->getParam('exception'))) {
                $resultPage->getConfig()->getTitle()->set(__('Error de página'));
            }
            return $resultPage;
        }
    }
