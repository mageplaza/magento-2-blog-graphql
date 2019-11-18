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
 * Class Categories
 * @package Mageplaza\BlogGraphQl\Model\Resolver
 */
class Categories implements ResolverInterface
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
        $searchCriteria = $this->searchCriteriaBuilder->build('categories', $args);
        $searchCriteria->setCurrentPage($args['currentPage']);
        $searchCriteria->setPageSize($args['pageSize']);

        switch ($args['action']) {
            case 'get_category_list':
                $collection = $this->_helperData->getFactoryByType('category')->create()->getCollection();
                break;
            case 'get_category_by_postId':
                $collection = $this->getCategoryByPostId($args);
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
     * @param $args
     *
     * @return mixed
     * @throws GraphQlInputException
     */
    protected function getCategoryByPostId($args)
    {
        if (!isset($args['postId'])) {
            throw new GraphQlInputException(__('postId value is not null'));
        }
        $post       = $this->_helperData->getFactoryByType()->create()->load($args['postId']);
        $collection = $post->getSelectedCategoriesCollection();

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
