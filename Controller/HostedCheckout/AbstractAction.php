<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Merchantesolutions\Controller\HostedCheckout;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Merchantesolutions\Api\Data\TransactionInterface;
use Magento\Merchantesolutions\Gateway\Config\HostedCheckout\Config;
use Magento\Merchantesolutions\Gateway\Http\Data\Response;
use Magento\Quote\Api\Data\CartInterface;

/**
 * Class AbstractAction
 * @package Magento\Merchantesolutions\Controller\HostedCheckout
 */
abstract class AbstractAction extends Action
{
    const ERESP_QUOTE_ID = 'eresp_quoteid';

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * @var array
     */
    protected $fieldMappings = [
        self::ERESP_QUOTE_ID => TransactionInterface::QUOTE_ID,
        TransactionInterface::RESP_CODE => Response::ERROR_CODE,
        TransactionInterface::RESP_TEXT => Response::AUTH_RESPONSE_TEXT,
        TransactionInterface::TRAN_ID => Response::TRANSACTION_ID
    ];

    /**
     * Constructor
     *
     * @param Context $context
     * @param Config $config
     * @param Session $checkoutSession
     */
    public function __construct(
        Context $context,
        Config $config,
        Session $checkoutSession
    ) {
        parent::__construct($context);
        $this->config = $config;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * Check whether payment method is enabled
     *
     * @inheritdoc
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->config->isActive() || !$this->config->isDisplayShoppingCart()) {
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);

            /** @var Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('noRoute');

            return $resultRedirect;
        }

        return parent::dispatch($request);
    }

    /**
     * @param CartInterface $quote
     * @return void
     * @throws \InvalidArgumentException
     */
    protected function validateQuote($quote)
    {
        if (!$quote || !$quote->getItemsCount()) {
            throw new \InvalidArgumentException(__('Checkout failed to initialize. Verify and try again.'));
        }
    }

    /**
     * @param $params
     * @return array
     */
    protected function translateParamFields($params)
    {
        $result = [];
        foreach ($params as $field => $value) {
            $fieldName = array_key_exists($field, $this->fieldMappings) ? $this->fieldMappings[$field] : $field;
            $result[$fieldName] = $value;
        }
        return $result;
    }
}
