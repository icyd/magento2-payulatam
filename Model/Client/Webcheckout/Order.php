<?php

namespace Icyd\Payulatam\Model\Client\Webcheckout;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;

class Order implements \Icyd\Payulatam\Model\Client\OrderInterface
{
    const STATUS_PRE_NEW            = 0;
    const STATUS_NEW                = 1;
    const STATUS_APPROVED           = 4;
    const STATUS_DECLINED           = 6;
    const STATUS_ERROR              = 104;
    const STATUS_EXPIRED            = 5;
    const STATUS_PENDING            = 7;
    /**
     * @var string[]
     */
    protected $statusDescription = [
        self::STATUS_PRE_NEW => 'New',
        self::STATUS_NEW => 'New',
        self::STATUS_APPROVED => 'Approved',
        self::STATUS_DECLINED => 'Rejected',
        self::STATUS_ERROR => 'Error',
        self::STATUS_EXPIRED => 'Expired',
        self::STATUS_PENDING => 'Pending'
    ];

    /**
     * @var Order\DataValidator
     */
    protected $dataValidator;

    /**
     * @var Order\DataGetter
     */
    protected $dataGetter;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Icyd\Payulatam\Model\Session
     */
    protected $session;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Icyd\Payulatam\Logger\Logger
     */
    protected $logger;

    /**
     * @var Order\Notification
     */
    protected $notificationHelper;

    /**
     * @var MethodCaller
     */
    protected $methodCaller;

    /**
     * @var \Icyd\Payulatam\Model\ResourceModel\Transaction
     */
    protected $transactionResource;

    /**
     * @var Order\Processor
     */
    protected $orderProcessor;

    /**
     * @param \Magento\Framework\View\Context $context
     * @param Order\DataValidator $dataValidator
     * @param Order\DataGetter $dataGetter
     * @param \Icyd\Payulatam\Model\Session $session
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Icyd\Payulatam\Logger\Logger $logger
     * @param Order\Notification $notificationHelper
     * @param MethodCaller $methodCaller
     * @param \Icyd\Payulatam\Model\ResourceModel\Transaction $transactionResource
     * @param Order\Processor $orderProcessor
     * @param \Magento\Framework\Controller\Result\RawFactory $rawResultFactory
     */
    public function __construct(
        \Magento\Framework\View\Context $context,
        \Magento\Framework\App\RequestInterface $request,
        \Icyd\Payulatam\Model\Session $session,
        \Icyd\Payulatam\Logger\Logger $logger,
        \Icyd\Payulatam\Model\ResourceModel\Transaction $transactionResource,
        Order\DataValidator $dataValidator,
        Order\DataGetter $dataGetter,
        Order\Notification $notificationHelper,
        Order\Processor $orderProcessor,
        MethodCaller $methodCaller
    ) {
        $this->urlBuilder = $context->getUrlBuilder();
        $this->dataValidator = $dataValidator;
        $this->dataGetter = $dataGetter;
        $this->session = $session;
        $this->request = $request;
        $this->logger = $logger;
        $this->notificationHelper = $notificationHelper;
        $this->methodCaller = $methodCaller;
        $this->transactionResource = $transactionResource;
        $this->orderProcessor = $orderProcessor;
    }

    /**
     * @inheritDoc
     */
    public function validateCreate(array $data = [])
    {
        return
            $this->dataValidator->validateEmpty($data) &&
            $this->dataValidator->validateBasicData($data);
    }

    /**
     * @inheritDoc
     */
    public function validateCancel($payulatamOrderId)
    {
        return $this->dataValidator->validateEmpty($payulatamOrderId);
    }

    /**
     * @inheritDoc
     */
    // public function validateStatusUpdate(array $data = [])
    // {
    //     // TODO: Implement validateStatusUpdate() method.
    // }

    /**
     * @inheritDoc
     */
    public function create(array $data)
    {
        $this->session->setOrderCreateData($data);
        return [
            'orderId' => md5($data['referenceCode']),
            'extOrderId' => $data['referenceCode'],
            'redirectUri' => $this->urlBuilder->getUrl('payulatam/webcheckout/form')
        ];
    }

    /**
     * @inheritDoc
     */
    // public function retrieve($payulatamOrderId)
    // {
    //     $posId = $this->dataGetter->getPosId();
    //     $ts = $this->dataGetter->getTs();
    //     $sig = $this->dataGetter->getSigForOrderRetrieve([
    //         'pos_id' => $posId,
    //         'referenceCode' => $payulatamOrderId,
    //         'ts' => $ts
    //     ]);
    //     $result = $this->methodCaller->call('orderRetrieve', [
    //         $posId,
    //         $payulatamOrderId,
    //         $ts,
    //         $sig
    //     ]);
    //     if ($result) {
    //         return [
    //             'status' => $result->transStatus,
    //             'amount' => $result->transAmount / 100
    //         ];
    //     }
    //     return false;
    // }
    //
    // /**
    //  * @inheritDoc
    //  */
    // public function cancel($payulatamOrderId)
    // {
    //     // TODO: Implement cancel() method.
    // }
    //
    // /**
    //  * @inheritDoc
    //  */
    // public function statusUpdate(array $data = [])
    // {
    //     // TODO: Implement statusUpdate() method.
    // }

    /**
     * @inheritDoc
     */
    // public function consumeNotification(\Magento\Framework\App\Request\Http $request)
    // {
    //     $payulatamOrderId = $this->notificationHelper->getPayuplOrderId($request);
    //     $orderData = $this->retrieve($payulatamOrderId);
    //     if ($orderData) {
    //         return [
    //             'payulatamOrderId' => md5($payulatamOrderId),
    //             'status' => $orderData['status'],
    //             'amount' => $orderData['amount']
    //         ];
    //     }
    //     return false;
    // }

    /**
     * @inheritDoc
     */
    public function getDataForOrderCreate(\Magento\Sales\Model\Order $order)
    {
        return $this->dataGetter->getBasicData($order);
    }

    /**
     * @inheritDoc
     */
    public function addSpecialDataToOrder(array $data = [])
    {
        if ($this->dataGetter->getTestMode()) {
            $data['test'] = $this->dataGetter->getTestMode();
        }
        $data['merchantId'] = $this->dataGetter->getMerchantId();
        $data['accountId'] = $this->dataGetter->getAccountId();
        $data['signature'] = $this->dataGetter->getSigForOrderCreate($data);

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function getNewStatus()
    {
        return Order::STATUS_PRE_NEW;
    }

    /**
     * @inheritDoc
     * Check if the transaction had error
     */
    public function paymentSuccessCheck()
    {
        $errorCode = $this->request->getParam('transactionState');
        $extOrderId = $this->request->getParam('lapResponseCode');

        if ($errorCode == Self::STATUS_ERROR) {
            $this->session->setErrorMsg("Error durante tansacción.");
            $this->logger->error('Payment error ' . $errorCode . ' for transaction ' . $extOrderId . '.');
        } elseif ($errorCode == Self::STATUS_DECLINED) {
            $this->session->setErrorMsg("Transacción rechazada.");
        } elseif ($errorCode == Self::STATUS_PENDING) {
            $this->session->setErrorMsg("Transacción pendiente por pago.");
        } elseif ($errorCode == Self::STATUS_EXPIRED) {
            $this->session->setErrorMsg("Transacción expirada.");
        } elseif ($errorCode == Self::STATUS_APPROVED) {
            return true;
        }
        return false;
    }

    /**
     * @inheritDoc
     */
    public function canProcessNotification($payulatamOrderId)
    {
        return !in_array(
             $this->transactionResource->getStatusByPayuplOrderId($payulatamOrderId),
             [self::STATUS_APPROVED, self::STATUS_EXPIRED]
        );
    }

    /**
     * @inheritDoc
     */
    public function processNotification(array $data = [])
    {
        if ($this->dataValidator->validateEmpty($data)) {
            $payulatamOrderId = md5($data['reference_sale']);
            if ($this->canProcessNotification($payulatamOrderId)) {
               if ($this->dataGetter->getSigForOrderRetrieve($data) != $data['sign']) {
                    throw new LocalizedException(new Phrase('Invalid Signature.'));
                }
                    $newest = $this->transactionResource->checkIfNewestByPayuplOrderId($payulatamOrderId);
                    $this->orderProcessor->processStatusChange($payulatamOrderId, $data['state_pol'], $data['value'], $newest);
                    return true;
                } else {
                    $this->logger->error("{$payulatamOrderId} already closed");
                }
            }
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getStatusDescription($status)
    {
        if (isset($this->statusDescription[$status])) {
            return (string) __($this->statusDescription[$status]);
        }
        return false;
    }
}
