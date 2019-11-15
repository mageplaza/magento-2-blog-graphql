<?php

declare(strict_types=1);

namespace Mageplaza\BlogGraphQl\Model\Resolver;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Mageplaza\Blog\Helper\Data;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\Argument\SearchCriteria\Builder as SearchCriteriaBuilder;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 * Class GetPosts
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
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * PickUpStoresList constructor.
     *
     * @param Data $helperData
     * @param CollectionProcessorInterface $collectionProcessor
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        Data $helperData,
        CollectionProcessorInterface $collectionProcessor,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->_helperData           = $helperData;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->collectionProcessor   = $collectionProcessor;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $this->vaildateArgs($args);
        $searchCriteria = $this->searchCriteriaBuilder->build('get_posts', $args);
        $searchCriteria->setCurrentPage($args['currentPage']);
        $searchCriteria->setPageSize($args['pageSize']);

        switch ($args['action']) {
            case 'get_post_list':
                $collection = $this->getPostList();
                break;
            case 'get_post_by_authorName':
                $collection = $this->getPostViewByAuthorName($args);
                break;
            case 'get_post_by_tagName':
                $collection = $this->getPostViewByTagName($args);
                break;
            case 'get_related_post':
                $collection = $this->getRelatedPost($args);
                break;
            default:
                throw new GraphQlInputException(__('No find your function'));
        }
        $this->collectionProcessor->process($searchCriteria, $collection);
        $collection->setSearchCriteria($searchCriteria);

        return [
            'total_count' => $collection->getTotalCount(),
            'items'       => $collection->getItems()
        ];
    }

    /**
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     */
    protected function getPostList()
    {
        $collection = $this->_helperData->getFactoryByType()->create()->getCollection();
        return $collection;
    }

    /**
     * @param $args
     *
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
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
        $collection = $tag->getSelectedPostsCollection();

        return $collection;
    }

    /**
     * @param $args
     *
     * @return \Mageplaza\Blog\Model\ResourceModel\Post\Collection|null
     * @throws GraphQlInputException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getRelatedPost($args)
    {
        if (!isset($args['postId'])) {
            throw new GraphQlInputException(__('postId value is not null'));
        }
        $post = $this->_helperData->getFactoryByType()->create()->load($args['postId']);
        $collection = $post->getRelatedPostsCollection();

        return $collection;
    }

    /**
     * @param array $args
     *
     * @throws GraphQlInputException
     */
    private function vaildateArgs(array $args): void
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