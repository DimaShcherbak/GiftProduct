<?php
declare(strict_types=1);

namespace Dimas\GiftProduct\Model\ResourceModel;

class GiftProduct extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('dimas_giftproduct_giftproduct', 'giftproduct_id');
    }
}

