<?php


namespace Dimas\GiftProduct\Model;


class Product extends \Magento\Catalog\Model\Product
{
    public function getName()
    {
        $changeName = $this->_getData('name'). 'modified';
        return $changeName;
    }

}
