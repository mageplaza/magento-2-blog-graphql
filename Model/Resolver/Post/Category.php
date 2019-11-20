<?php

declare(strict_types=1);

namespace Mageplaza\BlogGraphQl\Model\Resolver\Post;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\Resolver\Argument\SearchCriteria\Builder as SearchCriteriaBuilder;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Mageplaza\BlogGraphQl\Model\Resolver\Filter\Query\Filter;

/**
 * Class Category
 * @package Mageplaza\BlogGraphQl\Model\Resolver\Post
 */
class Category implements ResolverInterface
{
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var Filter
     */
    protected $filterQuery;

    /**
     * Category constructor.
     *
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param Filter $filterQuery
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Filter $filterQuery
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterQuery           = $filterQuery;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $post               = $value['model'];
        $categoryCollection = $post->getSelectedCategoriesCollection();
        $searchCriteria     = $this->searchCriteriaBuilder->build('categories', $args);
        $searchCriteria->setCurrentPage(1);
        $searchCriteria->setPageSize(10);
        $searchResult = $this->filterQuery->getResult($searchCriteria, $info, 'category', $categoryCollection);

        return [
            'total_count' => $searchResult->getTotalCount(),
            'items'       => $searchResult->getItemsSearchResult()
        ];
    }
}
