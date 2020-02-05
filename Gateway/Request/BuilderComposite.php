<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Merchantesolutions\Gateway\Request;

use Magento\Framework\ObjectManager\TMap;
use Magento\Framework\ObjectManager\TMapFactory;
use Magento\Merchantesolutions\Gateway\Config\Config;
use Magento\Payment\Gateway\Request\BuilderInterface;

/**
 * Class BuilderComposite
 * @package Magento\Merchantesolutions\Gateway\Request
 */
class BuilderComposite implements BuilderInterface
{
    const LEVEL_THREE_BUILDER_KEY = 'l3processing';

    /**
     * @var BuilderInterface[] | TMap
     */
    protected $builders;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @param TMapFactory $tmapFactory
     * @param array $builders
     * @param Config $config
     */
    public function __construct(
        TMapFactory $tmapFactory,
        Config $config,
        array $builders = []
    ) {
        $this->config = $config;
        $this->builders = $tmapFactory->create(
            [
                'array' => $builders,
                'type' => BuilderInterface::class
            ]
        );
    }

    /**
     * Builds ENV request
     *
     * @param array $buildSubject
     * @return array
     */
    public function build(array $buildSubject)
    {
        $result = [];

        // Remove the builder for level 3 processing if disabled
        if (!$this->config->isLevelThreeEnabled()) {
            unset($this->builders[self::LEVEL_THREE_BUILDER_KEY]);
        }

        foreach ($this->builders as $key => $builder) {
            // @TODO implement exceptions catching
            $result = $this->merge($result, $builder->build($buildSubject));
        }

        return $result;
    }

    /**
     * Merge function for builders
     *
     * @param array $result
     * @param array $builder
     * @return array
     */
    protected function merge(array $result, array $builder)
    {
        return array_replace_recursive($result, $builder);
    }
}