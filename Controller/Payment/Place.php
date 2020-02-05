<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Merchantesolutions\Controller\Payment;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Phrase;
use Magento\Merchantesolutions\Controller\HostedCheckout\AbstractAction;
use Magento\Merchantesolutions\Gateway\Config\HostedCheckout\Config;
use Magento\Merchantesolutions\Model\TransactionFactory;
use Magento\Merchantesolutions\Model\TransactionRepository;

/**
 * Class Place
 * @package Magento\Merchantesolutions\Controller\Payment
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

        /** @var  \Magento\Merchantesolutions\Model\Transaction $transaction */
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

        if (!array_key_exists(self::ERESP_QUOTE_ID, $params) || !$request->isSecure()) {
            return false;
        }

        return true;
    }

    /**
     * @param array|string $data
     * @return \Magento\Framework\Controller\Result\Json
     */
    private function createResponse($data)
    {
        $result = $this->jsonFactory->create();
        $result->setData($data);
        return $result;
    }
}
