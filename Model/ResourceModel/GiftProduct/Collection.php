<?php
declare(strict_types=1);

namespace Dimas\GiftProduct\Model\ResourceModel\GiftProduct;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'giftproduct_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Dimas\GiftProduct\Model\GiftProduct::class,
            \Dimas\GiftProduct\Model\ResourceModel\GiftProduct::class
        );
    }
}

