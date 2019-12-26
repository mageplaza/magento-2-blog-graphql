<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_BlogGraphQl
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

declare(strict_types=1);

namespace Mageplaza\BlogGraphQl\Model\Resolver\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\Resolver\Argument\SearchCriteria\Builder as SearchCriteriaBuilder;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Mageplaza\BlogGraphQl\Model\Resolver\Filter\Query\Filter;

/**
 * Class Post
 * @package Mageplaza\BlogGraphQl\Model\Resolver\Post
 */
class Post implements ResolverInterface
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
     * Post constructor.
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
        $category       = $value['model'];
        $postCollection = $category->getSelectedPostsCollection();
        $searchCriteria = $this->searchCriteriaBuilder->build('posts', $args);
        $searchCriteria->setCurrentPage(1);
        $searchCriteria->setPageSize(10);
        $searchResult = $this->filterQuery->getResult($searchCriteria, 'post', $postCollection);

        return [
            'total_count' => $searchResult->getTotalCount(),
            'items'       => $searchResult->getItemsSearchResult()
        ];
    }
}
