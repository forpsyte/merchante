<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Merchante\Merchante\Gateway\Response;

use Merchante\Merchante\Gateway\Config\Config;
use Merchante\Merchante\Gateway\Http\Data\Response;
use Merchante\Merchante\Gateway\SubjectReader;
use Magento\Payment\Gateway\Helper\ContextHelper;
use Magento\Sales\Model\Order\Payment;

/**
 * Class RiskDataHandler
 * @package Merchante\Merchante\Gateway\Response
 */
class RiskDataHandler extends AbstractHandler
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * RiskDataHandler constructor.
     *
     * @param SubjectReader $subjectReader
     * @param Config $config
     */
    public function __construct(
        SubjectReader $subjectReader,
        Config $config
    ) {
        $this->config = $config;
        parent::__construct($subjectReader);
    }

    /**
     * @inheritDoc
     */
    public function handle(array $handlingSubject, array $response)
    {
        $paymentDO = $this->subjectReader->readPayment($handlingSubject);

        /** @var Response $transaction */
        $transaction = $this->subjectReader->readTransaction($response);

        if (!isset($transaction[Response::AVS_RESULT])) {
            return;
        }

        /** @var Payment $payment */
        $payment = $paymentDO->getPayment();
        ContextHelper::assertOrderPayment($payment);

        if (!array_key_exists($transaction[Response::AVS_RESULT], $this->config->getNonRiskAvsResult())) {
            $payment->setIsFraudDetected(true);
        }
    }
}
