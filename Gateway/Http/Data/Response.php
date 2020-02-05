<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Merchantesolutions\Gateway\Http\Data;

use ArrayAccess;

/**
 * Class Response
 * @package Magento\Merchantesolutions\Model\Http\Data
 */
class Response implements ArrayAccess
{
    const TRANSACTION_ID = 'transaction_id';
    const ERROR_CODE = 'error_code';
    const AUTH_RESPONSE_TEXT = 'auth_response_text';
    const AVS_RESULT = 'avs_result';
    const AUTH_CODE = 'auth_code';
    const CVV2_RESULT = 'cvv2_result';

    /**
     * @var int
     */
    protected $status;

    /**
     * @var array
     */
    protected $headers;

    /**
     * @var string
     */
    protected $body;

    /**
     * @var array
     */
    protected $decodedBody;

    /**
     * @return bool
     */
    public function isSuccessful()
    {
        return $this->getErrorCode() === '000';
    }

    /**
     * @return mixed|null
     */
    public function getErrorCode()
    {
        return $this['error_code'];
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param string $header
     * @return string
     */
    public function getHeader($header)
    {
        return $this->headers[$header];
    }

    /**
     * @return string
     */
    public function getBodyAsString()
    {
        return $this->body;
    }

    /**
     * @return array|string
     */
    public function getBody()
    {
        return $this->getDecodedBody() ?: $this->__toString();
    }

    /**
     * @return array
     */
    public function getDecodedBody()
    {
        return $this->decodedBody;
    }

    /**
     * @param int $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @param array $headers
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;
    }

    /**
     * @param $body
     */
    public function setBody($body)
    {
        $this->body = $body;
        parse_str($this->body, $this->decodedBody);
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->decodedBody) && isset($this->decodedBody[$offset]);
    }

    /**
     * @param mixed $offset
     * @return mixed|null
     */
    public function offsetGet($offset)
    {
        return $this->offsetExists($offset) ? $this->decodedBody[$offset] : null;
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->decodedBody[$offset] = $value;
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->decodedBody[$offset]);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->body;
    }
}
