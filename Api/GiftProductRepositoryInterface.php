<?php
declare(strict_types=1);

namespace Dimas\GiftProduct\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface GiftProductRepositoryInterface
{

    /**
     * Save GiftProduct
     * @param \Dimas\GiftProduct\Api\Data\GiftProductInterface $giftProduct
     * @return \Dimas\GiftProduct\Api\Data\GiftProductInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Dimas\GiftProduct\Api\Data\GiftProductInterface $giftProduct
    );

    /**
     * Retrieve GiftProduct
     * @param string $giftproductId
     * @return \Dimas\GiftProduct\Api\Data\GiftProductInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($giftproductId);

    /**
     * Retrieve GiftProduct matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Dimas\GiftProduct\Api\Data\GiftProductSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete GiftProduct
     * @param \Dimas\GiftProduct\Api\Data\GiftProductInterface $giftProduct
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Dimas\GiftProduct\Api\Data\GiftProductInterface $giftProduct
    );

    /**
     * Delete GiftProduct by ID
     * @param string $giftproductId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($giftproductId);
}

