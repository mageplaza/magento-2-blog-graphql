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

namespace Mageplaza\BlogGraphQl\Model\Resolver;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Mageplaza\Blog\Helper\Data;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\Argument\SearchCriteria\Builder as SearchCriteriaBuilder;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Mageplaza\Blog\Model\ResourceModel\Post\Collection;
use Mageplaza\BlogGraphQl\Model\Resolver\Filter\Query\Filter;

/**
 * Class Posts
 * @package Mageplaza\BlogGraphQl\Model\Resolver
 */
class Posts implements ResolverInterface
{

    /**
     * @var Data
     */
    private $_helperData;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var Filter
     */
    protected $filterQuery;

    /**
     * Posts constructor.
     *
     * @param Data $helperData
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param Filter $filterQuery
     */
    public function __construct(
        Data $helperData,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Filter $filterQuery
    ) {
        $this->_helperData           = $helperData;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterQuery           = $filterQuery;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $this->validateArgs($args);
        $searchCriteria = $this->searchCriteriaBuilder->build('posts', $args);
        $searchCriteria->setCurrentPage($args['currentPage']);
        $searchCriteria->setPageSize($args['pageSize']);

        switch ($args['action']) {
            case 'get_post_list':
                $collection = null;
                break;
            case 'get_post_by_authorName':
                $collection = $this->getPostViewByAuthorName($args);
                break;
            case 'get_post_by_tagName':
                $collection = $this->getPostViewByTagName($args);
                break;
            case 'get_post_by_topic':
                $collection = $this->getPostViewByTopic($args);
                break;
            case 'get_related_post':
                $collection = $this->getRelatedPost($args);
                break;
            case 'get_post_by_categoryId':
                $collection = $this->getPostByCategoryId($args);
                break;
            case 'get_post_by_categoryKey':
                $collection = $this->getPostByCategoryKey($args);
                break;
            default:
                throw new GraphQlInputException(__('No find your function'));
        }
        $searchResult = $this->filterQuery->getResult($searchCriteria, 'post', $collection);

        //possible division by 0
        if ($searchCriteria->getPageSize()) {
            $maxPages = ceil($searchResult->getTotalCount() / $searchCriteria->getPageSize());
        } else {
            $maxPages = 0;
        }

        $currentPage = $searchCriteria->getCurrentPage();
        if ($searchCriteria->getCurrentPage() > $maxPages && $searchResult->getTotalCount() > 0) {
            throw new GraphQlInputException(
                __(
                    'currentPage value %1 specified is greater than the %2 page(s) available.',
                    [$currentPage, $maxPages]
                )
            );
        }

        return [
            'total_count' => $searchResult->getTotalCount(),
            'items'       => $searchResult->getItemsSearchResult()
        ];
    }

    /**
     * @return AbstractCollection
     */
    protected function getPostList()
    {
        return $this->_helperData->getFactoryByType()->create()->getCollection();
    }

    /**
     * @param $args
     *
     * @return AbstractCollection
     * @throws GraphQlInputException
     */
    protected function getPostViewByAuthorName($args)
    {
        if (!isset($args['authorName'])) {
            throw new GraphQlInputException(__('AuthorName value is not null'));
        }
        $collection = $this->_helperData->getFactoryByType()->create()->getCollection();
        $author     = $this->_helperData->getFactoryByType('author')->create()
            ->getAuthorByName($args['authorName']);
        $collection->addFieldToFilter('author_id', $author->getId());

        return $collection;
    }

    /**
     * @param $args
     *
     * @return Collection
     * @throws GraphQlInputException
     */
    public function getPostByCategoryId($args)
    {
        if (!isset($args['categoryId'])) {
            throw new GraphQlInputException(__('categoryId value is not null'));
        }
        $category   = $this->_helperData->getFactoryByType('category')->create()->load($args['categoryId']);

        return $category->getSelectedPostsCollection();
    }

    /**
     * @param $args
     *
     * @return mixed
     * @throws GraphQlInputException
     */
    protected function getPostByCategoryKey($args)
    {
        if (!isset($args['categoryKey'])) {
            throw new GraphQlInputException(__('categoryKey value is not null'));
        }
        $category   = $this->_helperData->getFactoryByType('category')->create()->getCollection()
            ->addFieldToFilter('url_key', $args['categoryKey'])->getFirstItem();

        return $category->getSelectedPostsCollection();
    }

    /**
     * @param $args
     *
     * @return mixed
     * @throws GraphQlInputException
     */
    protected function getPostViewByTagName($args)
    {
        if (!isset($args['tagName'])) {
            throw new GraphQlInputException(__('tagName value is not null'));
        }
        $tag        = $this->_helperData->getFactoryByType('tag')->create()->getCollection()
            ->addFieldToFilter('name', $args['tagName'])->getFirstItem();

        return $tag->getSelectedPostsCollection();
    }

    /**
     * @param $args
     *
     * @return mixed
     * @throws GraphQlInputException
     */
    protected function getPostViewByTopic($args)
    {
        if (!isset($args['topicId'])) {
            throw new GraphQlInputException(__('topicId value is not null'));
        }
        $topic      = $this->_helperData->getFactoryByType('topic')->create()->load($args['topicId']);

        return $topic->getSelectedPostsCollection();
    }

    /**
     * @param $args
     *
     * @return Collection|null
     * @throws GraphQlInputException
     * @throws LocalizedException
     */
    protected function getRelatedPost($args)
    {
        if (!isset($args['postId'])) {
            throw new GraphQlInputException(__('postId value is not null'));
        }
        $post       = $this->_helperData->getFactoryByType()->create()->load($args['postId']);

        return $post->getRelatedPostsCollection();
    }

    /**
     * @param array $args
     *
     * @throws GraphQlInputException
     */
    protected function validateArgs(array $args)
    {
        if (!isset($args['action'])) {
            throw new GraphQlInputException(__('Action value is not null'));
        }

        if (isset($args['currentPage']) && $args['currentPage'] < 1) {
            throw new GraphQlInputException(__('currentPage value must be greater than 0.'));
        }

        if (isset($args['pageSize']) && $args['pageSize'] < 1) {
            throw new GraphQlInputException(__('pageSize value must be greater than 0.'));
        }
    }
}
