<?php
declare(strict_types=1);

namespace Dimas\GiftProduct\Model\Data;

use Dimas\GiftProduct\Api\Data\GiftProductInterface;

class GiftProduct extends \Magento\Framework\Api\AbstractExtensibleObject implements GiftProductInterface
{

    /**
     * Get giftproduct_id
     * @return string|null
     */
    public function getGiftproductId()
    {
        return $this->_get(self::GIFTPRODUCT_ID);
    }

    /**
     * Set giftproduct_id
     * @param string $giftproductId
     * @return \Dimas\GiftProduct\Api\Data\GiftProductInterface
     */
    public function setGiftproductId($giftproductId)
    {
        return $this->setData(self::GIFTPRODUCT_ID, $giftproductId);
    }

    /**
     * Get ID
     * @return string|null
     */
    public function getID()
    {
        return $this->_get(self::ID);
    }

    /**
     * Set ID
     * @param string $iD
     * @return \Dimas\GiftProduct\Api\Data\GiftProductInterface
     */
    public function setID($iD)
    {
        return $this->setData(self::ID, $iD);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Dimas\GiftProduct\Api\Data\GiftProductExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Dimas\GiftProduct\Api\Data\GiftProductExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Dimas\GiftProduct\Api\Data\GiftProductExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * Get Status
     * @return string|null
     */
    public function getStatus()
    {
        return $this->_get(self::STATUS);
    }

    /**
     * Set Status
     * @param string $status
     * @return \Dimas\GiftProduct\Api\Data\GiftProductInterface
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Get Title
     * @return string|null
     */
    public function getTitle()
    {
        return $this->_get(self::TITLE);
    }

    /**
     * Set Title
     * @param string $title
     * @return \Dimas\GiftProduct\Api\Data\GiftProductInterface
     */
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * Get MainProduct
     * @return string|null
     */
    public function getMainProduct()
    {
        return $this->_get(self::MAINPRODUCT);
    }

    /**
     * Set MainProduct
     * @param string $mainProduct
     * @return \Dimas\GiftProduct\Api\Data\GiftProductInterface
     */
    public function setMainProduct($mainProduct)
    {
        return $this->setData(self::MAINPRODUCT, $mainProduct);
    }

    /**
     * Get GiftProduct
     * @return string|null
     */
    public function getGiftProduct()
    {
        return $this->_get(self::GIFTPRODUCT);
    }

    /**
     * Set GiftProduct
     * @param string $giftProduct
     * @return \Dimas\GiftProduct\Api\Data\GiftProductInterface
     */
    public function setGiftProduct($giftProduct)
    {
        return $this->setData(self::GIFTPRODUCT, $giftProduct);
    }

    /**
     * Get Label
     * @return string|null
     */
    public function getLabel()
    {
        return $this->_get(self::LABEL);
    }

    /**
     * Set Label
     * @param string $label
     * @return \Dimas\GiftProduct\Api\Data\GiftProductInterface
     */
    public function setLabel($label)
    {
        return $this->setData(self::LABEL, $label);
    }

    /**
     * Get Qty
     * @return string|null
     */
    public function getQty()
    {
        return $this->_get(self::QTY);
    }

    /**
     * Set Qty
     * @param string $qty
     * @return \Dimas\GiftProduct\Api\Data\GiftProductInterface
     */
    public function setQty($qty)
    {
        return $this->setData(self::QTY, $qty);
    }
}

