<?php

namespace Dimas\GiftProduct\Observer;

use Magento\Checkout\Model\Cart;
use Magento\Framework\Event\ObserverInterface;
use Dimas\GiftProduct\Model\ResourceModel\GiftProduct\CollectionFactory;
use Magento\Catalog\Model\ProductRepository;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductFactory;
use Magento\Framework\Registry;
use Magento\Checkout\Model\Session;

/**
 * Class AddGiftProduct
 * @package Dimas\GiftProduct\Observer
 */
class AddGiftProduct implements ObserverInterface
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
     * @var ProductFactory
     */
    private $productCollection;


    /**
     * AddGiftProduct constructor.
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
        Registry $registry,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection

    ) {
        $this->_productRepository = $productRepository;
        $this->_cart = $cart;
        $this->collection = $collection;
        $this->_registry = $registry;
        $this->_checkoutSession = $_checkoutSession;
        $this->productCollection = $productCollection;
    }

    /**
     * @param $product
     * @return mixed
     */
    public function currentSku($product)  // $sku текущщей страницы
    {
        $currentSku = $product->getData('sku');
        return $currentSku;
    }

    /**
     * @param $mainSku
     * @return array|false|string[]
     */
    public function getGiftProductSku($mainSku) // получаю масив $sku GiftProduct
    {
        $product1 = $this->productCollection->create();
        $product2 = $product1->getItems();
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

    public function getStatus($mainSku) // получаю статус по mainSku
    {
        $name = 'White Rabbit';
        $giftCollection = $this->collection->create();
        $giftCollection->addFieldToFilter('name', $name);
        $product = $giftCollection->getItems();
        $a = "";

        foreach ($product as $item) {
            $a = $item->getData('Status');
            if (in_array($mainSku, explode(';', $item->getData('MainProduct'))))
                return $a;
        }
        return [];
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
     * @return array
     */
    public function getItem()
    {
        $collection = $this->collection->create();
        $product = $collection->getItems();
        $a = [];
        foreach ($product as $item) {
            $a[$item->getData('giftproduct_id')] = explode(';', $item->getData('MainProduct'));
        }
        return $a;
    }

    /**
     * @param $current
     * @param $item
     * @return array
     */
    public function doCompare($current, $item)
    {
        foreach ($item as $key => $value) {
            if (in_array($current, $value)) {
                $name = implode(';', $value);
                $res = ['name' => $name, 'qty' => $this->collection->create()->getItemById($key)->getData('Qty')];
                return $res;
            }
        }
    }

    /**
     * @param $product
     * @return array
     */
    public function getGiftQty($product)
    {
        return $giftQty = $this->doCompare($this->currentSku($product), $this->getItem());
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
     * @param \Magento\Framework\Event\Observer $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $sku = $observer->getEvent()->getData('quote_item')->getData('sku');  //current sku
        $status = $this->getStatus($sku);
        if ($status == "Yes"){
            $giftToAdd = $this->getGiftProductSku($sku);
            $item = $observer->getEvent()->getData('quote_item');
            $product = $observer->getEvent()->getData('product');
            $mainProductQty = $this->_checkoutSession->getQuote()->getItemByProduct($product)->getQty();
            $item = ($item->getParentItem() ? $item->getParentItem() : $item);
            foreach ($giftToAdd as $gift) {
                if (in_array($gift, $this->getSkuQuote()) && $item->getPrice() == 0) continue;
                $_product = $this->_productRepository->get($gift)->setPrice(0)->setQty(1);
                if ($mainProductQty >= $this->getGiftQty($product)['qty']) {
                    $params = array(
                        'product' => $this->_productRepository->get($sku)->getId(),
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
                    $giftItem->setBasePrice(0);
                    $giftItem->getProduct()->setIsSuperMode(true);
                }
            }
            foreach ($this->_checkoutSession->getQuote()->getAllVisibleItems() as $item) {
                if (in_array($item->getSku(), $this->giftSkuArrayTotal()) && $item->getCustomPrice() == 0) {
                    $item->setQty(1);
                    $item->getProduct()->setIsSuperMode(true);
                }
            }
        }
    }
}
