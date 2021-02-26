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
class UpdateGiftProduct implements ObserverInterface
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
     * @var \Dimas\GiftProduct\Observer\AddGiftProduct
     */
    private $addObserver;

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
        Registry $registry,
        AddGiftProduct $addObserver)
    {
        $this->_productRepository = $productRepository;
        $this->_cart = $cart;
        $this->collection = $collection;
        $this->registry = $registry;
        $this->_checkoutSession = $_checkoutSession;
        $this->addObserver = $addObserver;
    }

    /**
     * @return array
     */
    public function mainProductArray()  // main sku к которым идут подарки с учетом Status
    {
        $giftCollection = $this->collection->create();
        $product = $giftCollection->getItems();
        $a = [];
        foreach ($product as $item) {
            if ($item->getData('Status') === "Yes") {
                $tmp = $item->getData('MainProduct');
                if (strstr($tmp, ';')) {
                    $a = array_merge($a, explode(';', $tmp));
                    continue;
                }
                $a[] = $tmp;
            }
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
     * @param $sku
     * @return array|mixed|null
     */
    public function giftQtyMain($sku)
    {
        $giftCollection = $this->collection->create();
        $items = $giftCollection->getItems();
        $a = [];
        foreach ($items as $item) {
            $a = explode(';', $item->getData('MainProduct'));
            if (in_array($sku, $a)) {
                return $item->getData('Qty');
            }
        }
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
     * @param $giftSku
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStatus($giftSku) // получаю статус по mainSku
    {
        $giftCollection = $this->collection->create();
        $product = $giftCollection->getItems();
        $result = [];
        foreach ($product as $item) {
            $flag = false;
            foreach (explode(';', $item->getData('MainProduct')) as $mainSku) {
                if (in_array($mainSku, $this->getSkuQuote())) {
                    if ($item->getData('Qty') <= $this->_checkoutSession->getQuote()->getItemByProduct($this->_productRepository->get($mainSku))->getQty()) {
                        $flag = true;
                    }
                }
            }
            if ($flag === true) {
                $result[] = $item;
            }
        }
        $status = 'No';
        foreach ($result as $item) {
            if ($item->getData('GiftProduct') == $giftSku) {
                if ($item->getData("Status") == 'Yes') {
                    $status = 'Yes';
                }
            }
        }
        return $status;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $idToCheange = $observer->getEvent()->getData()['info']->getData();
        foreach ($idToCheange as $key => $value) {
            foreach ($this->_checkoutSession->getQuote()->getAllVisibleItems() as $item) {
                if ($item->getItemId() != $key) continue;
                $sku = $item->getSku();
                break;
            }
            if (in_array($sku, $this->giftSkuArrayTotal()) && $this->getStatus($sku) == "Yes") {
                $quotItem = $this->_checkoutSession->getQuote()->getAllVisibleItems();
                foreach ($quotItem as $item) {
                    if (in_array($item->getSku(), $this->giftSkuArrayTotal())) {
                        $_product = $item->getProduct()->setPrice(0)->setQty(1);
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
                        $giftItem->getProduct()->setIsSuperMode(true);
                    }
                }
            }
            if (in_array($sku, $this->mainProductArray())) {
                if ($value['qty'] < $this->giftQtyMain($sku)) {
                    $giftToRemove = $this->getGiftProductSku($sku);
                    foreach ($this->_checkoutSession->getQuote()->getAllVisibleItems() as $itemm) {
                        if (!is_null($itemm->getCustomPrice()) && $itemm->getCustomPrice() == 0) {
                            foreach ($giftToRemove as $gift) {
                                if ($itemm->getSku() == $gift && !in_array($itemm->getSku(), $this->getGiftForMainSku())) {
                                    $itemm->delete();
                                    break;
                                }
                            }
                        }
                    }
                }
                if ($value['qty'] >= $this->giftQtyMain($sku)) {
                    foreach ($this->getGiftProductSku($sku) as $gift) {
                        if (in_array($gift, $this->getSkuQuote()) && $this->_productRepository->get($gift)->getPrice() == 0) continue;
                        $_product = $this->_productRepository->get($gift)->setPrice(0)->setQty(1);
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
                        $giftItem->getProduct()->setIsSuperMode(true);
                    }
                }
            }
        }
    }
}



