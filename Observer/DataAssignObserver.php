<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Merchantesolutions\Observer;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Event\Observer;
use Magento\Merchantesolutions\Api\TransactionRepositoryInterface;
use Magento\Merchantesolutions\Gateway\Http\Data\Response;
use Magento\Merchantesolutions\Model\Transaction;
use Magento\Payment\Observer\AbstractDataAssignObserver;
use Magento\Quote\Api\Data\PaymentInterface;
use Psr\Log\LoggerInterface;

/**
 * Class DataAssignObserver
 * @package Magento\Merchantesolutions\Observer
 */
class DataAssignObserver extends AbstractDataAssignObserver
{
    const CARD_ID = 'card_id';
    const CC_TYPE = 'cc_type';
    const CC_NUMBER = 'cc_number';
    const CC_EXP_YEAR = 'cc_exp_year';
    const CC_EXP_MONTH = 'cc_exp_month';
    const STORE_CARD = 'store_card';
    const CVV2 = 'cvv2';
    const CARD_ON_FILE = 'card_on_file';

    /**
     * @var array
     */
    protected $additionalInformationList = [
        self::CARD_ID,
        self::CC_TYPE,
        self::CC_NUMBER,
        self::CC_EXP_YEAR,
        self::CC_EXP_MONTH,
        self::STORE_CARD,
        self::CVV2,
        Transaction::QUOTE_ID,
        Transaction::CARD_ID,
        Transaction::AUTH_CODE,
        Transaction::TRAN_AMOUNT,
        Transaction::TRAN_TYPE,
        Transaction::ERROR_CODE,
        Transaction::AUTH_RESPONSE_TEXT,
        Transaction::TRANSACTION_ID
    ];
    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var TransactionRepositoryInterface
     */
    protected $transactionRepository;

    /**
     * @var LoggerInterface
     */
    protected $logger;


    /**
     * DataAssignObserver constructor.
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param TransactionRepositoryInterface $transactionRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        TransactionRepositoryInterface $transactionRepository,
        LoggerInterface $logger
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->transactionRepository = $transactionRepository;
        $this->logger = $logger;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $data = $this->readDataArgument($observer);

        $additionalData = $data->getData(PaymentInterface::KEY_ADDITIONAL_DATA);
        if (!is_array($additionalData)) {
            return;
        }

        // Add payment information for hosted checkout.
        if (isset($additionalData[Transaction::QUOTE_ID])) {
            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilter(Transaction::QUOTE_ID, $additionalData[Transaction::QUOTE_ID], 'eq')
                ->create();
            try {
                /** @var Transaction[] $result */
                $result = $this->transactionRepository
                    ->getList($searchCriteria)
                    ->getItems();
                $transaction = array_shift($result);
                $additionalData = array_merge($additionalData, $transaction->getData());

                // Add expiration data for vault details handler
                $additionalData[self::CC_EXP_MONTH] = substr($transaction->getExpDate(), 0, 2);
                $additionalData[self::CC_EXP_YEAR] = substr($transaction->getExpDate(), 2, 2);
            } catch (\Exception $exception) {
                $this->logger->error("DataAssignObserver::execute: " . $exception->getMessage());
            }
        }

        $paymentInfo = $this->readPaymentModelArgument($observer);

        foreach ($this->additionalInformationList as $additionalInformationKey) {
            if (isset($additionalData[$additionalInformationKey])) {
                $paymentInfo->setAdditionalInformation(
                    $additionalInformationKey,
                    $additionalData[$additionalInformationKey]
                );
            }
        }
    }
}
