<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Merchante\Merchante\Controller\Payment;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Phrase;
use Merchante\Merchante\Controller\HostedCheckout\AbstractAction;
use Merchante\Merchante\Gateway\Config\HostedCheckout\Config;
use Merchante\Merchante\Model\Transaction;
use Merchante\Merchante\Model\TransactionFactory;
use Merchante\Merchante\Model\TransactionRepository;

/**
 * Class Place
 * @package Merchante\Merchante\Controller\Payment
 */
class Place extends AbstractAction implements CsrfAwareActionInterface
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * @var TransactionRepository
     */
    protected $repository;

    /**
     * @var TransactionFactory
     */
    protected $transactionFactory;

    /**
     * @var JsonFactory
     */
    protected $jsonFactory;

    /**
     * Constructor
     *
     * @param Context $context
     * @param Config $config
     * @param Session $checkoutSession
     * @param TransactionRepository $repository
     * @param TransactionFactory $transactionFactory
     * @param JsonFactory $jsonFactory
     */
    public function __construct(
        Context $context,
        Config $config,
        Session $checkoutSession,
        TransactionRepository $repository,
        TransactionFactory $transactionFactory,
        JsonFactory $jsonFactory
    ) {
        $this->config = $config;
        $this->checkoutSession = $checkoutSession;
        $this->repository = $repository;
        $this->transactionFactory = $transactionFactory;
        $this->jsonFactory = $jsonFactory;
        parent::__construct($context, $config, $checkoutSession);
    }
    /**
     * @inheritDoc
     */
    public function execute()
    {
        $params = $this->translateParamFields($this->getRequest()->getParams());

        /** @var  Transaction $transaction */
        $transaction = $this->transactionFactory->create();

        foreach ($params as $field => $value) {
            $transaction->setData($field, $value);
        }

        try {
            $this->config->debug("Payment\Place::execute: Storing transaction. ", $params);
            $this->repository->save($transaction);
            return $this->createResponse(['success' => true]);
        } catch (\Exception $exception) {
            $this->config->debug("Payment\Place::execute: Could not save Transaction. " . $exception->getMessage());
            return $this->createResponse(['error' => $exception->getMessage()]);
        }
    }

    /**
     * @inheritDoc
     */
    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return new InvalidRequestException(
            $this->createResponse(['error' => true]),
            [new Phrase('Invalid Update Request.')]
        );
    }

    /**
     * @inheritDoc
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        $params = $request->getParams();

        if (!array_key_exists(Transaction::ERESP_QUOTE_ID, $params) || !$request->isSecure()) {
            return false;
        }

        return true;
    }

    /**
     * @param array|string $data
     * @return Json
     */
    private function createResponse($data)
    {
        $result = $this->jsonFactory->create();
        $result->setData($data);
        return $result;
    }
}
