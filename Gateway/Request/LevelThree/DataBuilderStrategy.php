<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Merchantesolutions\Gateway\Request\LevelThree;

use Magento\Framework\ObjectManager\TMapFactory;
use Magento\Merchantesolutions\Gateway\Config\Config;
use Magento\Merchantesolutions\Gateway\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;

/**
 * Class PaymentDataBuilderComposite
 * @package Magento\Merchantesolutions\Gateway\Request\LevelThree
 */
class DataBuilderStrategy implements BuilderInterface
{
    const CC_TYPE = 'cc_type';

    /**
     * @var BuilderInterface[]
     */
    protected $builders;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * @param TMapFactory $tmapFactory
     * @param Config $config
     * @param SubjectReader $subjectReader
     * @param array $builders
     */
    public function __construct(
        TMapFactory $tmapFactory,
        Config $config,
        SubjectReader $subjectReader,
        array $builders = []
    ) {
        $this->config = $config;
        $this->subjectReader = $subjectReader;
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
        $builder = $this->getBuilder($buildSubject);
        return !is_null($builder) ? $builder->build($buildSubject) : [];
    }

    /**
     * Get the builder specified by the selected card type
     *
     * @param array $buildSubject
     * @return BuilderInterface|null
     */
    private function getBuilder(array $buildSubject)
    {
        $paymentDO = $this->subjectReader->readPayment($buildSubject);
        $ccType = $paymentDO->getPayment()->getAdditionalInformation(self::CC_TYPE);
        return isset($this->builders[$ccType]) ? $this->builders[$ccType] : null;
    }
}
