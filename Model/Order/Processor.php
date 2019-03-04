<?php
/**
 * @copyright Copyright (c) 2017 Icyd Colombia (https://www.imaginacolombia.com)
 */

namespace Icyd\Payulatam\Model\Order;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;

class Processor
{
    /**
     * @var \Icyd\Payulatam\Model\Order
     */
    protected $orderHelper;

    /**
     * @var \Icyd\Payulatam\Model\Transaction\Service
     */
    protected $transactionService;

    /**
     * @param \Icyd\Payulatam\Model\Order $orderHelper
     * @param \Icyd\Payulatam\Model\Transaction\Service $transactionService
     */
    public function __construct(
        \Icyd\Payulatam\Model\Order $orderHelper,
        \Icyd\Payulatam\Model\Transaction\Service $transactionService
    ) {
        $this->orderHelper = $orderHelper;
        $this->transactionService = $transactionService;
    }

    /**
     * @param string $payulatamOrderId
     * @param string$status
     * @param bool $close
     * @throws LocalizedException
     */
    public function processOld($payulatamOrderId, $status, $close = false)
    {
        $this->transactionService->updateStatus($payulatamOrderId, $status, $close);
    }

    /**
     * @param string $payulatamOrderId
     * @param string $status
     * @throws LocalizedException
     */
    public function processPending($payulatamOrderId, $status)
    {
        $this->transactionService->updateStatus($payulatamOrderId, $status);
    }

    /**
     * @param string $payulatamOrderId
     * @param string $status
     * @throws LocalizedException
     */
    public function processHolded($payulatamOrderId, $status)
    {
        $order = $this->loadOrderByPayuplOrderId($payulatamOrderId);
        $this->orderHelper->setHoldedOrderStatus($order, $status);
        $this->transactionService->updateStatus($payulatamOrderId, $status, true);
    }

    /**
     * @param string $payulatamOrderId
     * @param string $status
     * @throws LocalizedException
     * @todo Implement some additional logic for transaction confirmation by store owner.
     */
    public function processWaiting($payulatamOrderId, $status)
    {
        $this->transactionService->updateStatus($payulatamOrderId, $status);
    }

    /**
     * @param string $payulatamOrderId
     * @param string $status
     * @param float $amount
     * @throws LocalizedException
     */
    public function processCompleted($payulatamOrderId, $status, $amount)
    {
        $order = $this->loadOrderByPayuplOrderId($payulatamOrderId);
        $this->orderHelper->completePayment($order, $amount, $payulatamOrderId);
        // $this->orderHelper->createShipment($order);
        $this->orderHelper->changeOrderStateToCustom($order, \Magento\Sales\Model\Order::STATE_COMPLETE, 'Payment from PayU received');
        $this->transactionService->updateStatus($payulatamOrderId, $status, true);
    }

    /**
     * @param string $payulatamOrderId
     * @return \Icyd\Payulatam\Model\Sales\Order
     * @throws LocalizedException
     */
    protected function loadOrderByPayuplOrderId($payulatamOrderId)
    {
        $order = $this->orderHelper->loadOrderByPayuplOrderId($payulatamOrderId);
        if (!$order) {
            throw new LocalizedException(new Phrase('Order not found.'));
        }
        return $order;
    }
}
