<?php

declare(strict_types=1);

namespace Magento\BlogGraphQl\Model\Resolver\Post\Query;

use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\BlogGraphQl\Model\Resolver\Post\DataProvider\Post;
use Mageplaza\BlogGraphQl\Model\Resolver\Post\SearchResult;
use Mageplaza\BlogGraphQl\Model\Resolver\Post\SearchResultFactory;
use Magento\Framework\GraphQl\Query\FieldTranslator;

/**
 * Retrieve filtered product data based off given search criteria in a format that GraphQL can interpret.
 */
class Filter
{
    /**
     * @var SearchResultFactory
     */
    private $searchResultFactory;

    /**
     * @var \Magento\CatalogGraphQl\Model\Resolver\Products\DataProvider\Product
     */
    private $postDataProvider;

    /**
     * @var FieldTranslator
     */
    private $fieldTranslator;

    /**
     * @var \Magento\Catalog\Model\Layer\Resolver
     */
    private $layerResolver;

    /**
     * @param SearchResultFactory $searchResultFactory
     * @param Post $postDataProvider
     * @param \Magento\Catalog\Model\Layer\Resolver $layerResolver
     * @param FieldTranslator $fieldTranslator
     */
    public function __construct(
        SearchResultFactory $searchResultFactory,
        Post $postDataProvider,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        FieldTranslator $fieldTranslator
    ) {
        $this->searchResultFactory = $searchResultFactory;
        $this->postDataProvider = $postDataProvider;
        $this->fieldTranslator = $fieldTranslator;
        $this->layerResolver = $layerResolver;
    }

    /**
     * Filter catalog product data based off given search criteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @param ResolveInfo $info
     * @param bool $isSearch
     * @return SearchResult
     */
    public function getResult(
        SearchCriteriaInterface $searchCriteria,
        ResolveInfo $info,
        bool $isSearch = false
    ): SearchResult {
        $fields = $this->getPostFields($info);
        $posts = $this->postDataProvider->getList($searchCriteria);
        $postArray = [];
        /** @var \Mageplaza\Blog\Model\Post $post */
        foreach ($posts->getItems() as $post) {
            $postArray[$post->getId()] = $post->getData();
            $postArray[$post->getId()]['model'] = $post;
        }

        return $this->searchResultFactory->create($posts->getTotalCount(), $postArray);
    }

    /**
     * Return field names for all requested product fields.
     *
     * @param ResolveInfo $info
     * @return string[]
     */
    private function getPostFields(ResolveInfo $info) : array
    {
        $fieldNames = [];
        foreach ($info->fieldNodes as $node) {
            if ($node->name->value !== 'Posts') {
                continue;
            }
            foreach ($node->selectionSet->selections as $selection) {
                if ($selection->name->value !== 'items') {
                    continue;
                }

                foreach ($selection->selectionSet->selections as $itemSelection) {
                    if ($itemSelection->kind === 'InlineFragment') {
                        foreach ($itemSelection->selectionSet->selections as $inlineSelection) {
                            if ($inlineSelection->kind === 'InlineFragment') {
                                continue;
                            }
                            $fieldNames[] = $this->fieldTranslator->translate($inlineSelection->name->value);
                        }
                        continue;
                    }
                    $fieldNames[] = $this->fieldTranslator->translate($itemSelection->name->value);
                }
            }
        }

        return $fieldNames;
    }
}
