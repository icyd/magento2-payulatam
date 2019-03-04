<?php

namespace Icyd\Payulatam\Block\Payment;

class Error extends \Magento\Framework\View\Element\Template
{
    protected $_escaper;
    protected $_session;
    protected $_cart;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Checkout\Model\Cart $cart,
        \Icyd\Payulatam\Model\Session $session,
        \Magento\Framework\Escaper $escaper
    )
    {
        parent::__construct($context);
        $this->_session = $session;
        $this->_cart = $cart;
        $this->_escaper = $escaper;
    }

    public function returnMessage()
    {
        $message = $this->_session->getErrorMsg();
        if(is_null($message))
            $message = 'Ocurrió un error en la página, por favor intentalo de nuevo más tarde';
        return $message;
    }

    public function getContinueShoppingUrl()
    {
        $quote=$this->_cart->getQuote();
        $totalItems=count($quote->getAllItems());
        if($totalItems==0) {
            $url = $this->getBaseUrl();
        } else {
            $url = $this->getUrl('checkout/cart');
        }

        return $url;
        }
}
?>
