<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Merchantesolutions\Gateway\Http;

use Magento\Framework\ObjectManagerInterface;
use Magento\Merchantesolutions\Gateway\Http\Data\Request;

/**
 * Class RequestFactory
 * @package Magento\Merchantesolutions\Model\Adapter\Http
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
     * Creates instance of Merchantesolutions Request.
     *
     * @return Request
     */
    public function create()
    {
        return $this->objectManager->create(Request::class);
    }
}