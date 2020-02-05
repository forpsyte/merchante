<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Merchantesolutions\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Merchantesolutions\Api\Data\TransactionInterface;

/**
 * Class Transaction
 * @package Magento\Merchantesolutions\Model
 */
class Transaction extends AbstractModel implements TransactionInterface
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'merchantesolutions_transaction';

    /**
     * Initialize resource model.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Magento\Merchantesolutions\Model\ResourceModel\Transaction::class);
    }

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->getData(self::ENTITY_ID);
    }

    /**
     * @inheritDoc
     */
    public function setId($id)
    {
        return $this->setData(self::ENTITY_ID, $id);
    }

    /**
     * @inheritDoc
     */
    public function getQuoteId()
    {
        return $this->getData(self::QUOTE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setQuoteId($quoteId)
    {
        return $this->setData(self::QUOTE_ID, $quoteId);
    }

    /**
     * @inheritDoc
     */
    public function getTranType()
    {
        return $this->getData(self::TRAN_TYPE);
    }

    /**
     * @inheritDoc
     */
    public function setTranType($tranType)
    {
        return $this->setData(self::TRAN_TYPE, $tranType);
    }

    /**
     * @inheritDoc
     */
    public function getTranAmount()
    {
        return $this->getData(self::TRAN_AMOUNT);
    }

    /**
     * @inheritDoc
     */
    public function setTranAmount($tranAmount)
    {
        return $this->setData(self::TRAN_AMOUNT, $tranAmount);
    }

    /**
     * @inheritDoc
     */
    public function getCardId()
    {
        return $this->getData(self::CARD_ID);
    }

    /**
     * @inheritDoc
     */
    public function setCardId($cardId)
    {
        return $this->setData(self::CARD_ID);
    }

    /**
     * @inheritDoc
     */
    public function getInvoiceNumber()
    {
        return $this->getData(self::INVOICE_NUMBER);
    }

    /**
     * @inheritDoc
     */
    public function setInvoiceNumber($invoiceNumber)
    {
        return $this->setData(self::INVOICE_NUMBER, $invoiceNumber);
    }

    /**
     * @inheritDoc
     */
    public function getCurrencyCode()
    {
        return $this->getData(self::CURRENCY_CODE);
    }

    /**
     * @inheritDoc
     */
    public function setCurrencyCode($currencyCode)
    {
        return $this->setData(self::CURRENCY_CODE, $currencyCode);
    }

    /**
     * @inheritDoc
     */
    public function getClientRefNumber()
    {
        return $this->getData(self::CLIENT_REF_NUMBER);
    }

    /**
     * @inheritDoc
     */
    public function setClientRefNumber($clientRefNumber)
    {
        return $this->setData(self::CLIENT_REF_NUMBER, $clientRefNumber);
    }

    /**
     * @inheritDoc
     */
    public function getAcctNumber()
    {
        return $this->getData(self::ACCT_NUMBER);
    }

    /**
     * @inheritDoc
     */
    public function setAcctNumber($acctNumber)
    {
        return $this->setData(self::ACCT_NUMBER, $acctNumber);
    }

    /**
     * @inheritDoc
     */
    public function getExpDate()
    {
        return $this->getData(self::EXP_DATE);
    }

    /**
     * @inheritDoc
     */
    public function setExpDate($expData)
    {
        return $this->setData(self::EXP_DATE, $expData);
    }

    /**
     * @inheritDoc
     */
    public function getRetrievalRefNumber()
    {
        return $this->getData(self::RETRIEVAL_REF_NUMBER);
    }

    /**
     * @inheritDoc
     */
    public function setRetrievalRefNumber($retrievalRefNumber)
    {
        return $this->setData(self::RETRIEVAL_REF_NUMBER, $retrievalRefNumber);
    }

    /**
     * @inheritDoc
     */
    public function getAuthCode()
    {
        return $this->getData(self::AUTH_CODE);
    }

    /**
     * @inheritDoc
     */
    public function setAuthCode($authCode)
    {
        return $this->setData(self::AUTH_CODE, $authCode);
    }

    /**
     * @inheritDoc
     */
    public function getRespCode()
    {
        return $this->getData(self::RESP_CODE);
    }

    /**
     * @inheritDoc
     */
    public function setRespCode($respCode)
    {
        return $this->setData(self::RESP_CODE, $respCode);
    }

    /**
     * @inheritDoc
     */
    public function getRespText()
    {
        return $this->getData(self::RESP_TEXT);
    }

    /**
     * @inheritDoc
     */
    public function setRespText($respText)
    {
        return $this->setData(self::RESP_TEXT, $respText);
    }

    /**
     * @inheritDoc
     */
    public function getTranId()
    {
        return $this->getData(self::TRAN_ID);
    }

    /**
     * @inheritDoc
     */
    public function setTranId($tranId)
    {
        return $this->setData(self::TRAN_ID, $tranId);
    }

    /**
     * @inheritDoc
     */
    public function getTranDate()
    {
        return $this->getData(self::TRAN_DATE);
    }

    /**
     * @inheritDoc
     */
    public function setTranDate($tranDate)
    {
        return $this->setData(self::TRAN_DATE, $tranDate);
    }
}
