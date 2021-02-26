<?php

namespace Dimas\GiftProduct\Observer;

use Magento\Checkout\Model\Cart;
use Magento\Checkout\Model\Session;
use Magento\Framework\Event\ObserverInterface;
use Dimas\GiftProduct\Model\ResourceModel\GiftProduct\CollectionFactory;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Registry;

/**
 * Class DelGiftProduct
 * @package Dimas\GiftProduct\Observer
 */
class DelGiftProduct implements ObserverInterface
{
    /**
     * @var ProductRepository
     */
    protected $_productRepository;
    /**
     * @var Cart
     */
    protected $_cart;
    /**
     * @var CollectionFactory
     */
    private $collection;
    /**
     * @var Registry
     */
    public $registry;
    /**
     * @var Session
     */
    protected $_checkoutSession;

    /**
     * DelGiftProduct constructor.
     * @param Session $_checkoutSession
     * @param ProductRepository $productRepository
     * @param Cart $cart
     * @param CollectionFactory $collection
     * @param Registry $registry
     */
    public function __construct(
        Session $_checkoutSession,
        ProductRepository $productRepository,
        Cart $cart,
        CollectionFactory $collection,
        Registry $registry)
    {
        $this->_productRepository = $productRepository;
        $this->_cart = $cart;
        $this->collection = $collection;
        $this->registry = $registry;
        $this->_checkoutSession = $_checkoutSession;
    }

    /**
     * @return array
     */
    public function mainProductArray()
    {
        $giftCollection = $this->collection->create();
        $product = $giftCollection->getItems();
        $a = [];
        foreach ($product as $item) {
            $tmp = $item->getData('MainProduct');
            if (strstr($tmp, ';')) {
                $a = array_merge($a, explode(';', $tmp));
                continue;
            }
            $a[] = $tmp;
        }
        return $a;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getGiftForMainSku()  // получаю массив Sku в коллекции с учетом Status
    {
        $giftCollection = $this->collection->create();
        $product = $giftCollection->getItems();
        $a = [];
        foreach ($product as $item) {
            foreach (explode(';', $item->getData('MainProduct')) as $mainProduct) {
                if ($item->getData('Status') === "Yes" &&
                    in_array($mainProduct, $this->getSkuQuote())) {
                    if ($this->_checkoutSession->getQuote()->getItemByProduct($this->_productRepository->get($mainProduct))) {
                        if ($this->_checkoutSession->getQuote()->getItemByProduct($this->_productRepository->get($mainProduct))->getQty() >= $item->getData('Qty')) {
                            $tmp = $item->getData('GiftProduct');
                            if (strstr($tmp, ';')) {
                                $a = array_merge($a, explode(';', $tmp));
                                continue;
                            }
                            if (!in_array($tmp, $a)) {
                                $a[] = $tmp;
                            }
                        }
                    }
                }
            }
        }
        return $a;
    }

    /**
     * @param $mainSku
     * @return array|false|string[]
     */
    public function getGiftProductSku($mainSku) // получаю масив $sku GiftProduct
    {
        $giftCollection = $this->collection->create();
        $product = $giftCollection->getItems();
        $a = [];
        foreach ($product as $item) {
            $a = explode(';', $item->getData('GiftProduct'));
            if (in_array($mainSku, explode(';', $item->getData('MainProduct'))))
                return $a;
        }
        return [];
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function quoteItam()
    {
        $result = [];
        $quoteItam = $this->_checkoutSession->getQuote()->getItems();
        foreach ($quoteItam as $item) {
            $result[] = $item;
        }
        return $result;
    }

    /**
     * @param $product
     * @return mixed
     */
    public function mainId($product)
    {
        $mainId = $product->getData('id');
        return $mainId;
    }

    /**
     * @return mixed
     */
    public function giftSkuArrayTotal()
    {
        $giftCollection = $this->collection->create();
        $items = $giftCollection->getItems();
        $a = [];
        foreach ($items as $item) {
            $a[] = explode(';', $item->getData('GiftProduct'));
        }
        foreach ($a as $value) {
            foreach ($value as $tmp) {
                $totalArray[] = $tmp;
            }
        }
        return $totalArray;
    }

    /**
     * @return array
     */
    public function getSkuQuote()
    {
        $data = [];
        foreach ($this->_cart->getQuote()->getAllVisibleItems() as $item) {
            $data[] = $item->getSku();
        }
        return $data;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $giftSku = $observer->getEvent()->getData('quote_item')->getData('sku');
        $productPrice = $observer->getEvent()->getData('quote_item')->getPrice();
        if (in_array($giftSku, $this->giftSkuArrayTotal()) && $productPrice == 0) {
            $_product = $this->_productRepository->get($giftSku)->setPrice(0)->setQty(1);
            $params = array(
                'product' => $this->_productRepository->get($giftSku)->getId(),
                'qty' => 1,
                'price' => 0
            );
            $this->_cart->addProduct($_product, $params);
            $this->_cart->save();
            $quote = $this->_checkoutSession->getQuote();
            $giftItem = $quote->getItemByProduct($_product);
            $giftItem->setQty(1);
            $giftItem->setPrice(0);
            $giftItem->setCustomPrice(0);
            $giftItem->setOriginalPrice(0);
            $giftItem->setOriginalCustomPrice(0);
            $giftItem->getProduct()->setIsSuperMode(true);
        }
        if (in_array($giftSku, $this->mainProductArray())) {
            $giftToRemove = $this->getGiftProductSku($giftSku);
            foreach ($this->_checkoutSession->getQuote()->getAllVisibleItems() as $key => $item) {
                if ($item->getPrice() == 0) {
                    foreach ($giftToRemove as $gift) {
                        if ($item->getSku() == $gift && !in_array($item->getSku(), $this->getGiftForMainSku())) {
                            $item->delete();
                            break;
                        }
                    }
                }
            }
        }
    }
}

