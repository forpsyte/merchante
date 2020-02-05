<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Merchantesolutions\Model\ResourceModel\Transaction;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Merchantesolutions\Model\Transaction;

/**
 * Class Collection
 * @package Magento\Merchantesolutions\Model\ResourceModel\Transaction
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'merchantesolutions_transaction_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'transaction_collection';

    /**
     * Define resource model.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Transaction::class, \Magento\Merchantesolutions\Model\ResourceModel\Transaction::class);
        $this->_map['fields']['entity_id'] = 'main_table.entity_id';
    }
}