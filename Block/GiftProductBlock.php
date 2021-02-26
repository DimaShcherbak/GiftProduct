<?php

namespace Dimas\GiftProduct\Block;

use Magento\Catalog\Model\Product;
use \Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Dimas\GiftProduct\Model\ResourceModel\GiftProduct\CollectionFactory;
use Dimas\GiftProduct\Api\GiftProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Catalog\Model\ProductRepository;

/**
 * Class GiftProduct
 * @package Dimas\GiftProduct\Block
 */
class GiftProductBlock extends Template
{
    private $productRepository;
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var GiftProductRepositoryInterface
     */
    private $giftProductRepository;
    /**
     * @var Registry
     */
    public $registry;
    /**
     * @var Product
     */
    protected $_product;
    /**
     * @var CollectionFactory
     */
    protected $giftProductCollection;

    /**
     * GiftProduct constructor.
     * @param Template\Context $context
     * @param Product $product
     * @param CollectionFactory $giftProductCollection
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param Registry $registry
     * @param GiftProductRepositoryInterface $giftProductRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param array $data
     */
    public function __construct(Template\Context $context, Product $product, CollectionFactory $giftProductCollection,
                                \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
                                Registry $registry, GiftProductRepositoryInterface $giftProductRepository,
                                ProductRepository $productRepository,
                                SearchCriteriaBuilder $searchCriteriaBuilder, array $data = [])
    {
        parent::__construct($context, $data);
        $this->registry = $registry;
        $this->_product = $product;
        $this->giftProductCollection = $giftProductCollection;
        $this->giftProductRepository = $giftProductRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_productCollectionFactory= $productCollectionFactory;
        $this->productRepository = $productRepository;

    }
    public function getName($skus)
    {
        $name = [];
        foreach ($skus as $sku) {
            $name[] = $this->productRepository->get($sku)->getName();
        }

        return $res = implode(', ', $name);
    }

    /**
     * @param $product
     * @return $this
     */
    public function setProduct($product)
    {
        $this->_product = $product;

        return $this;
    }

    public function getCurrentProduct()
    {
        return $this->registry->registry('current_product');
    }

    public function doCompare($current, $item)
    {
        foreach ($item as $key => $value) {
            if (in_array($current, $value)) {
                $name = implode(';', $value);
                $res = ['name' => $name, 'qty' => $this->giftProductCollection->create()->getItemById($key)->getData('Qty')];
                return $res;
            }
        }
    }

    public function getItem()
    {
        $collection = $this->giftProductCollection->create();
        $product = $collection->getItems();
        $a = [];
        foreach ($product as $item) {
            $a[$item->getData('giftproduct_id')] = explode(';', $item->getData('MainProduct'));
        }
        return $a;
    }

    public function currentSku()  // $sku текущщей страницы
    {
        $currentProduct = $this->registry->registry('current_product');
        $currentSku = $currentProduct->getSku();
        return $currentSku;
    }

    public function getGiftProductSku() // получаю масив $sku GiftProduct
    {
        $collection = $this->giftProductCollection->create();
        $product = $collection->getItems();
        $a = [];
        foreach ($product as $item) {
            $a = explode(';', $item->getData('GiftProduct'));
            if(in_array($this->currentSku(), explode(';', $item->getData('MainProduct'))))
                return $a;
        }
        return $a;
    }
    public function getStatus($mainSku) // получаю статус по mainSku
    {
        $giftCollection = $this->giftProductCollection->create();
        $product = $giftCollection->getItems();
        $a = "";

        foreach ($product as $item) {
            $a = $item->getData('Status');
            if (in_array($mainSku, explode(';', $item->getData('MainProduct'))))
                return $a;
        }
        return [];
    }
}
