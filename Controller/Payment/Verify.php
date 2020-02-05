<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Merchantesolutions\Controller\Payment;

use Magento\Checkout\Model\Session;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Merchantesolutions\Controller\HostedCheckout\AbstractAction;
use Magento\Merchantesolutions\Gateway\Config\HostedCheckout\Config;
use Magento\Merchantesolutions\Api\TransactionRepositoryInterface;

/**
 * Class Verify
 * @package Magento\Merchantesolutions\Controller\Payment
 */
class Verify extends AbstractAction
{
    /**
     * @var TransactionRepositoryInterface
     */
    protected $transactionRepository;

    /**
     * @var JsonFactory
     */
    protected $jsonFactory;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * Verify constructor.
     *
     * @param Context $context
     * @param Config $config
     * @param Session $checkoutSession
     * @param TransactionRepositoryInterface $transactionRepository
     * @param JsonFactory $jsonFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        Context $context,
        Config $config,
        Session $checkoutSession,
        TransactionRepositoryInterface $transactionRepository,
        JsonFactory $jsonFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->config = $config;
        $this->checkoutSession = $checkoutSession;
        $this->transactionRepository = $transactionRepository;
        $this->jsonFactory = $jsonFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        parent::__construct($context, $config, $checkoutSession);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();

        if (!array_key_exists('quote_id', $params)) {
            return $this->createResponse([
                'error' => true,
                'message' => 'Invalid Request'
            ]);
        }

        try {
            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilter('quote_id', $params['quote_id'], 'eq')
                ->create();
            $result = $this->transactionRepository->getList($searchCriteria);
            $response = $result->getTotalCount() > 0 ? 'success' : 'error';
            $message = $result->getTotalCount() > 0 ? 'Transaction verified.' : 'Transaction not verified.';
            return $this->createResponse([
                $response => true,
                'message' => $message
            ]);
        } catch (\Exception $exception) {
            $this->config->debug("Payment\Verify::execute: Could not verify Transaction. " . $exception->getMessage());
            return $this->createResponse([
                'error' => true,
                'message' => 'Could not verify Transaction'
            ]);
        }
    }

    /**
     * @param array $data
     * @return \Magento\Framework\Controller\Result\Json
     */
    private function createResponse(array $data = [])
    {
        $result = $this->jsonFactory->create();
        $result->setData($data);
        return $result;
    }
}
