<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Mageplaza\BlogGraphQl\Model\Resolver\Filter;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Container for a product search holding the item result and the array in the GraphQL-readable product type format.
 */
class SearchResult
{
    /**
     * @var SearchResultsInterface
     */
    private $totalCount;

    /**
     * @var array
     */
    private $itemsSearchResult;

    /**
     * @param int $totalCount
     * @param array $itemsSearchResult
     */
    public function __construct(int $totalCount, array $itemsSearchResult)
    {
        $this->totalCount = $totalCount;
        $this->itemsSearchResult = $itemsSearchResult;
    }

    /**
     * Return total count of search and filtered result
     *
     * @return int
     */
    public function getTotalCount() : int
    {
        return $this->totalCount;
    }

    /**
     * Retrieve an array in the format of GraphQL-readable type containing product data.
     *
     * @return array
     */
    public function getItemsSearchResult() : array
    {
        return $this->itemsSearchResult;
    }
}
