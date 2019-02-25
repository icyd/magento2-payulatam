<?php

namespace Icyd\Payulatam\Block\Payment;

class Error extends \Magento\Framework\View\Element\Template
{
    protected $registry;
    protected $_escaper;
    protected $_checkoutSession;
    protected $_cart;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Registry $registry,
        \Magento\Checkout\Model\Cart $_cart,
        \Magento\Framework\Escaper $escaper
    )
    {
        parent::__construct($context);
        $this->_checkoutSession = $checkoutSession;
        $this->registry = $registry;
        $this->_cart = $_cart;
        $this->_escaper = $escaper;
    }

    public function returnMessage()
    {
        $message = $this->registry->registry('message');
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
