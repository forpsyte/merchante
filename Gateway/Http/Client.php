<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Merchante\Merchante\Gateway\Http;

use Magento\Framework\Exception\LocalizedException;
use Merchante\Merchante\Gateway\Config\Config;
use Merchante\Merchante\Gateway\Http\Data\Request;
use Merchante\Merchante\Gateway\Http\Data\Response;

/**
 * Class Client
 * @package Merchante\Merchante\Model\Adapter\Http
 */
class Client
{
    const SANDBOX_HOSTNAME = 'https://cert.merchante-solutions.com/';
    const PRODUCTION_HOSTNAME = 'https://api.merchante-solutions.com/';

    /**
     * @var resource
     */
    protected $curl;

    /**
     * @var string
     */
    protected $proxyType;

    /**
     * @var string
     */
    protected $proxyUrl;

    /**
     * @var bool
     */
    protected $verifyPeer;

    /**
     * @var bool
     */
    protected $verifyHost;

    /**
     * @var array
     */
    protected $optionsSet = [];

    /**
     * @var int
     */
    protected $responseHeaderCount;

    /**
     * @var array
     */
    protected $responseHeaders;

    /**
     * @var string
     */
    protected $responseBody;

    /**
     * @var int
     */
    protected $responseStatus;

    /**
     * @var int
     */
    protected $headerCount = 0;

    /**
     * @var Config
     */
    protected $config;

    /**
     * Client constructor.
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @param $option
     * @param $value
     */
    protected function setCurlOption($option, $value)
    {
        $this->optionsSet[$option] = $value;
        curl_setopt($this->curl, $option, $value);
    }

    /**
     * @return string
     */
    public function getProxyType()
    {
        return $this->proxyType;
    }

    /**
     * @param string $proxyType
     */
    public function setProxyType($proxyType)
    {
        $this->proxyType = $proxyType;
    }

    /**
     * @return string
     */
    public function getProxyUrl()
    {
        return $this->proxyUrl;
    }

    /**
     * @param string $proxyUrl
     */
    public function setProxyUrl($proxyUrl)
    {
        $this->proxyUrl = $proxyUrl;
    }

    /**
     * @return bool
     */
    public function isVerifyPeer()
    {
        return $this->verifyPeer;
    }

    /**
     * @param bool $verifyPeer
     */
    public function setVerifyPeer($verifyPeer)
    {
        $this->verifyPeer = $verifyPeer;
    }

    /**
     * @return bool
     */
    public function isVerifyHost()
    {
        return $this->verifyHost;
    }

    /**
     * @param bool $verifyHost
     */
    public function setVerifyHost($verifyHost)
    {
        $this->verifyHost = $verifyHost;
    }

    /**
     * @param Request $request
     * @return Response
     * @throws LocalizedException
     */
    public function execute(Request $request)
    {
        $this->curl = curl_init();

        $url = $request->getUrl();

        /**
         * Do some checks to see if we should hit the sandbox or the production server
         */
        if (stristr($url, "http://") === false && stristr($url, "https://") === false) {
            // The request doesn't have their own full url, we can prepend our own hostname in this case.
            $url = $this->config->getEnvironmentUrl();
        }

        // GET parameters
        $parameters = $request->getParameters();
        $url = !empty($parameters) ? $url . '?' . http_build_query($parameters) : $url;

        $this->setCurlOption(CURLOPT_URL, $url);

        $requestMethod = $request->getMethod();
        if ($requestMethod === "POST") {
            $this->setCurlOption(CURLOPT_POST, 1);
        } elseif ($requestMethod === "GET") {
            $this->setCurlOption(CURLOPT_HTTPGET, 1);
        } else {
            $this->setCurlOption(CURLOPT_CUSTOMREQUEST, $requestMethod);
        }

        // Request body
        $body = $request->getBody();
        $body = is_string($body) ? $body : http_build_query($body);
        $body = preg_replace('/%5B(?:[0-9]|[1-9][0-9]+)%5D=/', '=', $body);

        $request->addHeader('Content-Type', 'application/x-www-form-urlencoded');

        if (!empty($body)) {
            $this->setCurlOption(CURLOPT_POSTFIELDS, $body);
        }

        $headers = $request->getHeaders();

        if (count($headers) > 0) {
            $headerStrings = [];
            foreach ($request->getHeaders() as $header => $value) {
                $headerStrings[] = $header . ": " . $value;
            }

            $this->setCurlOption(CURLOPT_HTTPHEADER, $headerStrings);
        }

        $cookies = $request->getCookies();
        if (count($cookies) > 0) {
            $cookieStrings = [];
            foreach ($cookies as $k => $v) {
                $cookieStrings[] = "{$k}={$v}";
            }

            $this->setCurlOption(CURLOPT_COOKIE, implode(";", $cookieStrings));
        }

        if ($request->getTimeout()) {
            $this->setCurlOption(CURLOPT_TIMEOUT, $request->getTimeout());
        }

        if ($request->getPort()) {
            $this->setCurlOption(CURLOPT_PORT, $request->getPort());
        }

        if ($this->getProxyType() && $this->getProxyUrl()) {
            $this->setCurlOption(CURLOPT_PROXYTYPE, $this->getProxyType());
            $this->setCurlOption(CURLOPT_PROXY, $this->getProxyUrl());
        }

        if ($this->isVerifyHost() !== null) {
            $this->setCurlOption(CURLOPT_SSL_VERIFYHOST, $this->isVerifyHost());
        }

        if ($this->isVerifyPeer() !== null) {
            $this->setCurlOption(CURLOPT_SSL_VERIFYPEER, !!$this->isVerifyPeer());
        }

        $this->setCurlOption(CURLOPT_RETURNTRANSFER, 1);
        $this->setCurlOption(CURLOPT_HEADER, 0);
        $this->setCurlOption(CURLOPT_HEADERFUNCTION, [$this, 'parseHeaders']);

        $curlOptions = $request->getCurlOptions();
        if (count($curlOptions) > 0) {
            foreach ($curlOptions as $k => $v) {
                $this->setCurlOption($k, $v);
            }
        }

        $this->responseHeaderCount = 0;
        $this->responseHeaders = [];

        $this->config->debug("Gateway\Http\Client::execute(): Sending request...", [
            "curlOptions" => $this->optionsSet
        ]);

        $this->responseBody = curl_exec($this->curl);
        $err = curl_errno($this->curl);

        /**
         * If a cURL error occurred, throw an exception.
         */
        if ($err !== 0) {
            throw new LocalizedException(
                __("An error occurred while sending a %1 request to %2. Error code: %3", $request->getMethod(), $url, $err)
            );
        }

        curl_close($this->curl);

        if (!isset($request->responseClass)) {
            throw new LocalizedException(
                __("Could not create a response class because your request class does not have a responseClass property.")
            );
        }

        /** @var Response $response */
        $response = new $request->responseClass();
        $response->setStatus($this->responseStatus);
        $response->setHeaders($this->responseHeaders);
        $response->setBody($this->responseBody);

        $this->config->debug("Gateway\Http\Client::execute(): Response received...", [
            "curlOptions" => $response->getBody()
        ]);

        return $response;
    }

    /**
     * @param $ch
     * @param $data
     * @return int
     * @throws LocalizedException
     */
    protected function parseHeaders($ch, $data)
    {
        if ($this->headerCount == 0) {
            $line = explode(" ", trim($data), 3);

            if (count($line) != 3) {
                throw new LocalizedException(__("Invalid response line returned from server: %1", $data));
            }

            $this->responseStatus = intval($line[1]);
        } else {
            $name = $value = '';
            $out = explode(": ", trim($data), 2);
            if (count($out) == 2) {
                $name = $out[0];
                $value = $out[1];
            }

            if (strlen($name)) {
                if ("Set-Cookie" == $name) {
                    if (!isset($this->responseHeaders[$name])) {
                        $this->responseHeaders[$name] = [];
                    }
                    $this->responseHeaders[$name][] = $value;
                } else {
                    $this->responseHeaders[$name] = $value;
                }
            }
        }

        $this->headerCount++;

        return strlen($data);
    }
}
