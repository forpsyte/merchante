<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Merchantesolutions\Api\Data;

/**
 * Class TransactionInterface
 * @api
 * @package Magento\Merchantesolutions\Api\Data
 */
interface TransactionInterface
{
    const ENTITY_ID = 'entity_id';

    const QUOTE_ID = 'quote_id';

    const TRAN_TYPE = 'tran_type';

    const TRAN_AMOUNT = 'tran_amount';

    const CARD_ID = 'card_id';

    const INVOICE_NUMBER = 'invoice_number';

    const CURRENCY_CODE = 'currency_code';

    const CLIENT_REF_NUMBER = 'client_ref_number';

    const ACCT_NUMBER = 'acct_number';

    const EXP_DATE = 'exp_date';

    const RETRIEVAL_REF_NUMBER = 'retrieval_ref_number';

    const AUTH_CODE = 'auth_code';

    const RESP_CODE = 'resp_code';

    const RESP_TEXT = 'resp_text';

    const TRAN_ID = 'tran_id';

    const TRAN_DATE = 'tran_date';

    /**
     * Get transaction ID.
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set transaction ID.
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * Get quote ID.
     *
     * @return int|null
     */
    public function getQuoteId();

    /**
     * Set quote ID.
     *
     * @param int $quoteId
     * @return $this
     */
    public function setQuoteId($quoteId);

    /**
     * Get transaction type.
     *
     * @return string
     */
    public function getTranType();

    /**
     * Set the transaction type.
     *
     * @param string $tranType
     * @return $this
     */
    public function setTranType($tranType);

    /**
     * Get transaction amount.
     *
     * @return string
     */
    public function getTranAmount();

    /**
     * Set transaction amount.
     *
     * @param string $tranAmount
     * @return $this
     */
    public function setTranAmount($tranAmount);

    /**
     * Get card id.
     *
     * @return string
     */
    public function getCardId();

    /**
     * Set card id.
     *
     * @param string $cardId
     * @return $this
     */
    public function setCardId($cardId);

    /**
     * Get invoice number.
     *
     * @return string
     */
    public function getInvoiceNumber();

    /**
     * Set invoice number.
     *
     * @param string $invoiceNumber
     * @return $this
     */
    public function setInvoiceNumber($invoiceNumber);

    /**
     * Get currency code.
     *
     * @return string
     */
    public function getCurrencyCode();

    /**
     * Set currency code.
     *
     * @param string $currencyCode
     * @return $this
     */
    public function setCurrencyCode($currencyCode);

    /**
     * Get currency code.
     *
     * @return string
     */
    public function getClientRefNumber();

    /**
     * Set currency code.
     *
     * @param string $clientRefNumber
     * @return $this
     */
    public function setClientRefNumber($clientRefNumber);

    /**
     * Get account number.
     *
     * @return string
     */
    public function getAcctNumber();

    /**
     * Set account number.
     *
     * @param string $acctNumber
     * @return $this
     */
    public function setAcctNumber($acctNumber);

    /**
     * Get expiration date.
     *
     * @return string
     */
    public function getExpDate();

    /**
     * Set expiration date.
     *
     * @param string $expData
     * @return $this
     */
    public function setExpDate($expData);

    /**
     * Get retrieval reference number.
     *
     * @return string
     */
    public function getRetrievalRefNumber();

    /**
     * Set retrieval reference number.
     *
     * @param string $retrievalRefNumber
     * @return $this
     */
    public function setRetrievalRefNumber($retrievalRefNumber);

    /**
     * Get authorization code.
     *
     * @return mixed
     */
    public function getAuthCode();

    /**
     * Set authorization code.
     *
     * @param string $authCode
     * @return $this
     */
    public function setAuthCode($authCode);

    /**
     * Get response code.
     *
     * @return string
     */
    public function getRespCode();

    /**
     * Set response code.
     *
     * @param string $respCode
     * @return $this
     */
    public function setRespCode($respCode);

    /**
     * Get response text.
     *
     * @return string
     */
    public function getRespText();

    /**
     * Set response text.
     *
     * @param string $respText
     * @return $this
     */
    public function setRespText($respText);

    /**
     * Get transaction id.
     *
     * @return string
     */
    public function getTranId();

    /**
     * Set transaction id.
     *
     * @param string $tranId
     * @return $this
     */
    public function setTranId($tranId);

    /**
     * Get the transaction date
     *
     * @return string
     */
    public function getTranDate();

    /**
     * Set transaction date.
     *
     * @param string $tranDate
     * @return $this
     */
    public function setTranDate($tranDate);
}
