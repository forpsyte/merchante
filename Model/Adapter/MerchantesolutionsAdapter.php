<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Merchantesolutions\Model\Adapter;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Merchantesolutions\Gateway\Http\Client;
use Magento\Merchantesolutions\Gateway\Http\Data\Response;
use Magento\Merchantesolutions\Gateway\Http\Data\ResponseFactory;
use Magento\Merchantesolutions\Gateway\Http\RequestFactory;
use Magento\Merchantesolutions\Model\Adminhtml\Source\Environment;


/**
 * Class MerchantesolutionsAdapter
 * @package Magento\Merchantesolutions\Model\Adapter
 */
class MerchantesolutionsAdapter
{
    /**
     * @var string
     */
    protected $profileId;

    /**
     * @var string
     */
    protected $profileKey;

    /**
     * @var string
     */
    protected $environmentUrl;

    /**
     * @var RequestFactory
     */
    protected $requestFactory;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var ResponseFactory
     */
    protected $responseFactory;

    /**
     * @var Json
     */
    protected $serializer;

    /**
     * MerchantesolutionsAdapter constructor.
     *
     * @param string $profileId
     * @param string $profileKey
     * @param string $environmentUrl
     * @param RequestFactory $requestFactory
     * @param ResponseFactory $responseFactory
     * @param Client $client
     * @param Json $serializer
     */
    public function __construct(
        $profileId,
        $profileKey,
        $environmentUrl,
        RequestFactory $requestFactory,
        ResponseFactory $responseFactory,
        Client $client,
        Json $serializer
    ) {
        $this->profileId = $profileId;
        $this->profileKey = $profileKey;
        $this->environmentUrl = $environmentUrl;
        $this->requestFactory = $requestFactory;
        $this->responseFactory = $responseFactory;
        $this->client = $client;
        $this->serializer = $serializer;
    }

    /**
     * @param array $data
     * @return Response
     * @throws LocalizedException
     */
    public function execute(array $data)
    {
        $request = $this->requestFactory->create();
        $request->setUrl($this->environmentUrl);
        $request->setMethod($request::POST);
        $request->setBody($data);
        $request->setBodyKey($request::FIELD_PROFILE_KEY, $this->profileKey);
        $request->setBodyKey($request::FIELD_PROFILE_ID, $this->profileId);
        return $this->client->execute($request);
    }

    /**
     * @param array $data
     * @return Response
     */
    public function buildResponse(array $data)
    {
        /** @var Response $response */
        $response = $this->responseFactory->create();
        $response->setBody(http_build_query($data));
        return $response;
    }
}
