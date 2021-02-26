<?php
declare(strict_types=1);

namespace Dimas\GiftProduct\Api\Data;

interface GiftProductInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const TITLE = 'Title';
    const MAINPRODUCT = 'MainProduct';
    const ID = 'ID';
    const QTY = 'Qty';
    const GIFTPRODUCT_ID = 'giftproduct_id';
    const GIFTPRODUCT = 'GiftProduct';
    const LABEL = 'Label';
    const STATUS = 'Status';

    /**
     * Get giftproduct_id
     * @return string|null
     */
    public function getGiftproductId();

    /**
     * Set giftproduct_id
     * @param string $giftproductId
     * @return \Dimas\GiftProduct\Api\Data\GiftProductInterface
     */
    public function setGiftproductId($giftproductId);

    /**
     * Get ID
     * @return string|null
     */
    public function getID();

    /**
     * Set ID
     * @param string $iD
     * @return \Dimas\GiftProduct\Api\Data\GiftProductInterface
     */
    public function setID($iD);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Dimas\GiftProduct\Api\Data\GiftProductExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Dimas\GiftProduct\Api\Data\GiftProductExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Dimas\GiftProduct\Api\Data\GiftProductExtensionInterface $extensionAttributes
    );

    /**
     * Get Status
     * @return string|null
     */
    public function getStatus();

    /**
     * Set Status
     * @param string $status
     * @return \Dimas\GiftProduct\Api\Data\GiftProductInterface
     */
    public function setStatus($status);

    /**
     * Get Title
     * @return string|null
     */
    public function getTitle();

    /**
     * Set Title
     * @param string $title
     * @return \Dimas\GiftProduct\Api\Data\GiftProductInterface
     */
    public function setTitle($title);

    /**
     * Get MainProduct
     * @return string|null
     */
    public function getMainProduct();

    /**
     * Set MainProduct
     * @param string $mainProduct
     * @return \Dimas\GiftProduct\Api\Data\GiftProductInterface
     */
    public function setMainProduct($mainProduct);

    /**
     * Get GiftProduct
     * @return string|null
     */
    public function getGiftProduct();

    /**
     * Set GiftProduct
     * @param string $giftProduct
     * @return \Dimas\GiftProduct\Api\Data\GiftProductInterface
     */
    public function setGiftProduct($giftProduct);

    /**
     * Get Label
     * @return string|null
     */
    public function getLabel();

    /**
     * Set Label
     * @param string $label
     * @return \Dimas\GiftProduct\Api\Data\GiftProductInterface
     */
    public function setLabel($label);

    /**
     * Get Qty
     * @return string|null
     */
    public function getQty();

    /**
     * Set Qty
     * @param string $qty
     * @return \Dimas\GiftProduct\Api\Data\GiftProductInterface
     */
    public function setQty($qty);
}

