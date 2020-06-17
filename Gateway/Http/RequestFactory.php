<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Merchante\Merchante\Gateway\Http;

use Magento\Framework\ObjectManagerInterface;
use Merchante\Merchante\Gateway\Http\Data\Request;

/**
 * Class RequestFactory
 * @package Merchante\Merchante\Model\Adapter\Http
 */
class RequestFactory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * RequestFactory constructor.
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * Creates instance of Merchante Request.
     *
     * @return Request
     */
    public function create()
    {
        return $this->objectManager->create(Request::class);
    }
}
