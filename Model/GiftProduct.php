<?php
declare(strict_types=1);

namespace Dimas\GiftProduct\Model;

use Dimas\GiftProduct\Api\Data\GiftProductInterface;
use Dimas\GiftProduct\Api\Data\GiftProductInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;

class GiftProduct extends \Magento\Framework\Model\AbstractModel
{

    protected $_eventPrefix = 'dimas_giftproduct_giftproduct';
    protected $giftproductDataFactory;

    protected $dataObjectHelper;


    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param GiftProductInterfaceFactory $giftproductDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Dimas\GiftProduct\Model\ResourceModel\GiftProduct $resource
     * @param \Dimas\GiftProduct\Model\ResourceModel\GiftProduct\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        GiftProductInterfaceFactory $giftproductDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Dimas\GiftProduct\Model\ResourceModel\GiftProduct $resource,
        \Dimas\GiftProduct\Model\ResourceModel\GiftProduct\Collection $resourceCollection,
        array $data = []
    ) {
        $this->giftproductDataFactory = $giftproductDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve giftproduct model with giftproduct data
     * @return GiftProductInterface
     */
    public function getDataModel()
    {
        $giftproductData = $this->getData();

        $giftproductDataObject = $this->giftproductDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $giftproductDataObject,
            $giftproductData,
            GiftProductInterface::class
        );

        return $giftproductDataObject;
    }
}

