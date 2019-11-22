<?php

declare(strict_types=1);

namespace Mageplaza\BlogGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Mageplaza\Blog\Model\ResourceModel\PostLike\CollectionFactory;

/**
 * Class Like
 * @package Mageplaza\BlogGraphQl\Model\Resolver
 */
class Likes implements ResolverInterface
{

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Likes constructor.
     *
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $this->validateArgs($args);
        $collection = $this->collectionFactory->create()->addFieldToFilter('post_id', $args['postId']);

        return [
            'total' => $collection->count()
        ];
    }

    /**
     * @param array $args
     *
     * @throws GraphQlInputException
     */
    protected function validateArgs(array $args)
    {
        if (!isset($args['postId'])) {
            throw new GraphQlInputException(__('postId value is not null'));
        }
    }
}
