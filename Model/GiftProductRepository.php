<?php
declare(strict_types=1);

namespace Dimas\GiftProduct\Model;

use Dimas\GiftProduct\Api\Data\GiftProductInterfaceFactory;
use Dimas\GiftProduct\Api\Data\GiftProductSearchResultsInterfaceFactory;
use Dimas\GiftProduct\Api\GiftProductRepositoryInterface;
use Dimas\GiftProduct\Model\ResourceModel\GiftProduct as ResourceGiftProduct;
use Dimas\GiftProduct\Model\ResourceModel\GiftProduct\CollectionFactory as GiftProductCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;

class GiftProductRepository implements GiftProductRepositoryInterface
{

    protected $dataObjectProcessor;

    protected $resource;

    protected $extensionAttributesJoinProcessor;

    protected $dataGiftProductFactory;

    protected $giftProductFactory;

    protected $giftProductCollectionFactory;

    private $collectionProcessor;

    protected $extensibleDataObjectConverter;
    private $storeManager;

    protected $searchResultsFactory;

    protected $dataObjectHelper;


    /**
     * @param ResourceGiftProduct $resource
     * @param GiftProductFactory $giftProductFactory
     * @param GiftProductInterfaceFactory $dataGiftProductFactory
     * @param GiftProductCollectionFactory $giftProductCollectionFactory
     * @param GiftProductSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceGiftProduct $resource,
        GiftProductFactory $giftProductFactory,
        GiftProductInterfaceFactory $dataGiftProductFactory,
        GiftProductCollectionFactory $giftProductCollectionFactory,
        GiftProductSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->giftProductFactory = $giftProductFactory;
        $this->giftProductCollectionFactory = $giftProductCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataGiftProductFactory = $dataGiftProductFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Dimas\GiftProduct\Api\Data\GiftProductInterface $giftProduct
    ) {
        /* if (empty($giftProduct->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $giftProduct->setStoreId($storeId);
        } */

        $giftProductData = $this->extensibleDataObjectConverter->toNestedArray(
            $giftProduct,
            [],
            \Dimas\GiftProduct\Api\Data\GiftProductInterface::class
        );

        $giftProductModel = $this->giftProductFactory->create()->setData($giftProductData);

        try {
            $this->resource->save($giftProductModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the giftProduct: %1',
                $exception->getMessage()
            ));
        }
        return $giftProductModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function get($giftProductId)
    {
        $giftProduct = $this->giftProductFactory->create();
        $this->resource->load($giftProduct, $giftProductId);
        if (!$giftProduct->getId()) {
            throw new NoSuchEntityException(__('GiftProduct with id "%1" does not exist.', $giftProductId));
        }
        return $giftProduct->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->giftProductCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Dimas\GiftProduct\Api\Data\GiftProductInterface::class
        );

        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $items = [];
        foreach ($collection as $model) {
            $items[] = $model->getDataModel();
        }

        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \Dimas\GiftProduct\Api\Data\GiftProductInterface $giftProduct
    ) {
        try {
            $giftProductModel = $this->giftProductFactory->create();
            $this->resource->load($giftProductModel, $giftProduct->getGiftproductId());
            $this->resource->delete($giftProductModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the GiftProduct: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($giftProductId)
    {
        return $this->delete($this->get($giftProductId));
    }
}
