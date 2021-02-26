<?php
declare(strict_types=1);

namespace Dimas\GiftProduct\Api\Data;

interface GiftProductSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get GiftProduct list.
     * @return \Dimas\GiftProduct\Api\Data\GiftProductInterface[]
     */
    public function getItems();

    /**
     * Set ID list.
     * @param \Dimas\GiftProduct\Api\Data\GiftProductInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

