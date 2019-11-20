<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Mageplaza\BlogGraphQl\Model\Resolver\Filter\DataProvider;

use Magento\Framework\Api\SearchCriteriaInterface;
use Mageplaza\Blog\Model\ResourceModel\Post\CollectionFactory;
use Magento\Catalog\Api\Data\ProductSearchResultsInterfaceFactory;
use Magento\Framework\Api\SearchResultsInterface;

/**
 * Product field data provider, used for GraphQL resolver processing.
 */
class Post
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var ProductSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * Post constructor.
     *
     * @param CollectionFactory $collectionFactory
     * @param ProductSearchResultsInterfaceFactory $searchResultsFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        ProductSearchResultsInterfaceFactory $searchResultsFactory
    ) {
        $this->collectionFactory    = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    /**
     * Gets list of product data with full data set. Adds eav attributes to result set from passed in array
     *
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @param $collection
     *
     * @return SearchResultsInterface
     */
    public function getList(
        SearchCriteriaInterface $searchCriteria,
        $collection
    ): SearchResultsInterface {
        /** @var \Mageplaza\Blog\Model\ResourceModel\Post\Collection $collection */
        if (!$collection) {
            $collection = $this->collectionFactory->create();
        }
        $searchResult = $this->searchResultsFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setItems($collection->getItems());
        $searchResult->setTotalCount($collection->getSize());

        return $searchResult;
    }
}
