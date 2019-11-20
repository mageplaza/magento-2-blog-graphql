<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Mageplaza\BlogGraphQl\Model\Resolver\Filter;

use Magento\Framework\ObjectManagerInterface;

/**
 * Generate SearchResult based off of total count from query and array of products and their data.
 */
class SearchResultFactory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Instantiate SearchResult
     *
     * @param int $totalCount
     * @param array $itemsSearchResult
     * @return SearchResult
     */
    public function create(int $totalCount, array $itemsSearchResult) : SearchResult
    {
        return $this->objectManager->create(
            SearchResult::class,
            ['totalCount' => $totalCount, 'itemsSearchResult' => $itemsSearchResult]
        );
    }
}
