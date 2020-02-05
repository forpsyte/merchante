<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Merchantesolutions\Gateway\Http\Data;

/**
 * Class Request
 * @package Magento\Merchantesolutions\Model\Http\Data
 */
class Request
{
    const GET = 'GET';
    const POST = 'POST';
    const PUT = 'PUT';
    const DELETE = 'DELETE';
    const HEAD = 'HEAD';
    const PURGE = 'PURGE';
    const OPTIONS = 'OPTIONS';
    const FIELD_PROFILE_ID = 'profile_id';
    const FIELD_PROFILE_KEY = 'profile_key';
    const FIELD_ACCOUNT_DATA_SOURCE = 'account_data_source';
    const FIELD_CARD_NUMBER = 'card_number';
    const FIELD_CARD_ID = 'card_id';
    const FIELD_CARD_EXP_DATE = 'card_exp_date';
    const FIELD_CARD_ON_FILE = 'card_on_file';
    const FIELD_TRANSACTION_AMOUNT = 'transaction_amount';
    const FIELD_TRANSACTION_TYPE = 'transaction_type';
    const FIELD_TRANSACTION_ID = 'transaction_id';
    const FIELD_INVOICE_NUMBER = 'invoice_number';
    const FIELD_CARDHOLDER_STREET_ADDRESS = 'cardholder_street_address';
    const FIELD_CARDHOLDER_ZIP = 'cardholder_zip';
    const FIELD_MOTO_ECOMMERCE_IND = 'moto_ecommerce_ind';
    const FIELD_CLIENT_REFERENCE_NUMBER = 'client_reference_number';
    const FIELD_CVV2 = 'cvv2';
    const FIELD_RETRY_ID = 'retry_id';
    const FIELD_SHIP_TO_ZIP = 'ship_to_zip';
    const FIELD_SHIP_FROM_ZIP = 'ship_from_zip';
    const FIELD_DEST_COUNTRY_CODE = 'dest_country_code';
    const FIELD_MERCHANT_TAX_ID = 'merchant_tax_id';
    const FIELD_SUMMARY_COMMODITY_CODE = 'summary_commodity_code';
    const FIELD_DISCOUNT_AMOUNT = 'discount_amount';
    const FIELD_SHIPPING_AMOUNT = 'shipping_amount';
    const FIELD_DUTY_AMOUNT = 'duty_amount';
    const FIELD_VAT_INVOICE_NUMBER = 'vat_invoice_number';
    const FIELD_ORDER_DATE = 'order_date';
    const FIELD_VAT_AMOUNT = 'vat_amount';
    const FIELD_PO_NUMBER = 'po_number';

    /**
     * URL for request
     * @var string
     */
    protected $url;

    /**
     * Request Method
     * @var string
     */
    protected $method;

    /**
     * Request port
     * @var int
     */
    protected $port;

    /**
     * Request parameters for GET
     * @var array
     */
    protected $parameters = [];

    /**
     * Request body
     * @var mixed
     */
    protected $body;

    /**
     * Request headers
     * @var array
     */
    protected $headers = [];

    /**
     * Request cookies
     * @var array
     */
    protected $cookies = [];

    /**
     * Request timeout
     * @var int type
     */
    protected $timeout = 300;

    /**
     * User overrides options
     * Are applied before curl_exec
     *
     * @var array
     */
    protected $curlUserOptions = [];

    /**
     * The response class that should be returned
     * @var string
     */
    public $responseClass = Response::class;

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param $url
     * @param array $params
     */
    public function setUrl($url, $params = [])
    {
        $this->url = $this->buildUrl($url, $params);
    }

    /**
     * @param string $url
     * @param array $params
     * @return string
     */
    protected function buildUrl($url, $params = [])
    {
        foreach ($params as $key => $value) {
            // replace possible variables in url
            $url = str_replace('{' . $key . '}', $value, $url);
        }

        return $url;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param int $port
     */
    public function setPort($port)
    {
        $this->port = $port;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param array $parameters
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @param $param
     * @param $value
     */
    public function addParameter($param, $value)
    {
        $this->parameters[$param] = $value;
    }

    /**
     * @param $param
     */
    public function removeParameter($param)
    {
        unset($this->parameters[$param]);
    }

    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param mixed $body
     */
    public function setBody($body)
    {
        // If it's a string, assume it's a query string. Parse it out and put it in the body.
        if (is_string($body)) {
            parse_str($body, $this->body);
            return;
        }

        $this->body = $body;
    }

    /**
     * @param $key
     * @param $value
     */
    public function setBodyKey($key, $value)
    {
        $this->body[$key] = $value;
    }

    /**
     * @param $key
     * @param null $default
     * @return null|mixed
     */
    public function getBodyKey($key, $default = null)
    {
        if (!isset($this->body[$key])) {
            return $default;
        }

        return $this->body[$key];
    }

    /**
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * @param int $timeout
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param array $headers
     * @return void
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;
    }

    /**
     * @param string $name name, ex. "Location"
     * @param string $value value ex. "http://google.com"
     * @return void
     */
    public function addHeader($name, $value)
    {
        $this->headers[$name] = $value;
    }

    /**
     * @param string $name
     * @return void
     */
    public function removeHeader($name)
    {
        unset($this->headers[$name]);
    }

    /**
     * @param string $login username
     * @param string $pass password
     * @return void
     */
    public function setCredentials($login, $pass)
    {
        $val = base64_encode($login . ":" . $pass);
        $this->addHeader("Authorization", "Basic " . $val);
    }

    /**
     * @return array
     */
    public function getCookies()
    {
        return $this->cookies;
    }

    /**
     * Add cookie
     *
     * @param string $name
     * @param string $value
     * @return void
     */
    public function addCookie($name, $value)
    {
        $this->cookies[$name] = $value;
    }

    /**
     * Remove cookie
     *
     * @param string $name
     * @return void
     */
    public function removeCookie($name)
    {
        unset($this->cookies[$name]);
    }

    /**
     * Set cookies array
     *
     * @param array $cookies
     * @return void
     */
    public function setCookies($cookies)
    {
        $this->cookies = $cookies;
    }

    /**
     * Clear cookies
     * @return void
     */
    public function removeCookies()
    {
        $this->setCookies([]);
    }

    /**
     * @return array
     */
    public function getCurlOptions()
    {
        return $this->curlUserOptions;
    }

    /**
     * Set CURL options overrides array
     * @param array $arr
     * @return void
     */
    public function setCurlOptions($arr)
    {
        $this->curlUserOptions = $arr;
    }

    /**
     * Set curl option
     *
     * @param string $name
     * @param string $value
     * @return void
     */
    public function setCurlOption($name, $value)
    {
        $this->curlUserOptions[$name] = $value;
    }

    /**
     * @param $functionName
     * @param $arguments
     * @return mixed|null
     */
    public function __call($functionName, $arguments)
    {
        $prefix = substr($functionName, 0, 3);
        $property = null;
        if (in_array($prefix, ['get', 'set', 'has'])) {
            $property = strtolower(preg_replace('/(.)([A-Z])/', "$1_$2", substr($functionName, 3)));
        }

        if (!isset($property) || !$property) {
            trigger_error("Call to undefined method " . $functionName . " on " . get_class($this), E_USER_ERROR);
            return null;
        }

        if ($prefix === 'get') {
            return $this->getBodyKey($property);
        }

        if ($prefix === 'set') {
            $this->setBodyKey($property, $arguments[0]);
            return null;
        }

        if ($prefix === 'has') {
            return $this->getBodyKey($property) !== null;
        }
    }
}
