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

namespace Mageplaza\BlogGraphQl\Model\Resolver\Filter\Query;

use Magento\Catalog\Model\Product as ProductModel;
use Magento\Framework\Api\SearchCriteriaInterface;
use Mageplaza\Blog\Model\Category as CategoryModel;
use Mageplaza\Blog\Model\Post as PostModel;
use Mageplaza\Blog\Model\Tag as TagModel;
use Mageplaza\Blog\Model\Topic as TopicModel;
use Mageplaza\BlogGraphQl\Model\Resolver\Filter\DataProvider\Category;
use Mageplaza\BlogGraphQl\Model\Resolver\Filter\DataProvider\Post;
use Mageplaza\BlogGraphQl\Model\Resolver\Filter\DataProvider\Product;
use Mageplaza\BlogGraphQl\Model\Resolver\Filter\DataProvider\Tag;
use Mageplaza\BlogGraphQl\Model\Resolver\Filter\DataProvider\Topic;
use Mageplaza\BlogGraphQl\Model\Resolver\Filter\SearchResult;
use Mageplaza\BlogGraphQl\Model\Resolver\Filter\SearchResultFactory;

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
     * @var Post
     */
    private $postDataProvider;

    /**
     * @var Post
     */
    private $categoryDataProvider;

    /**
     * @var Tag
     */
    private $tagDataProvider;

    /**
     * @var Topic
     */
    private $topicDataProvider;

    /**
     * @var Product
     */
    private $productDataProvider;

    /**
     * Filter constructor.
     *
     * @param SearchResultFactory $searchResultFactory
     * @param Post $postDataProvider
     * @param Category $categoryDataProvider
     * @param Tag $tagDataProvider
     * @param Topic $topicDataProvider
     * @param Product $productDataProvider
     */
    public function __construct(
        SearchResultFactory $searchResultFactory,
        Post $postDataProvider,
        Category $categoryDataProvider,
        Tag $tagDataProvider,
        Topic $topicDataProvider,
        Product $productDataProvider
    ) {
        $this->searchResultFactory  = $searchResultFactory;
        $this->postDataProvider     = $postDataProvider;
        $this->categoryDataProvider = $categoryDataProvider;
        $this->tagDataProvider      = $tagDataProvider;
        $this->topicDataProvider    = $topicDataProvider;
        $this->productDataProvider  = $productDataProvider;
    }

    /**
     * Filter catalog product data based off given search criteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @param string $type
     *
     * @param null $collection
     *
     * @return SearchResult
     */
    public function getResult(
        SearchCriteriaInterface $searchCriteria,
        $type = 'post',
        $collection = null
    ): SearchResult {
        switch ($type) {
            case 'category':
                $list = $this->categoryDataProvider->getList($searchCriteria, $collection);
                break;
            case 'tag':
                $list = $this->tagDataProvider->getList($searchCriteria, $collection);
                break;
            case 'topic':
                $list = $this->topicDataProvider->getList($searchCriteria, $collection);
                break;
            case 'product':
                $list = $this->productDataProvider->getList($searchCriteria, $collection);
                break;
            case 'post':
            default:
                $list = $this->postDataProvider->getList($searchCriteria, $collection);
                break;
        }

        $listArray = [];
        /** @var PostModel|CategoryModel|TagModel|TopicModel $post */
        foreach ($list->getItems() as $item) {
            $item->load($item->getId());
            $item->getUrlImage();
            $item->getAuthorUrl();
            $item->getAuthorName();
            $item->getViewTraffic();
            $item->getAuthorUrlKey();

            if ($item instanceof ProductModel) {
                $images = $item->getMediaGalleryImages()->getSize() ? $item->getMediaGalleryImages() : [];
                $imagesData = [];
                if (is_object($images)) {
                    foreach ($images->getItems() as $it) {
                        $imagesData[] = $it->getUrl();
                    }
                }
                $item->setData('images', $imagesData);
            }

            $listArray[$item->getId()]          = $item->getData();
            $listArray[$item->getId()]['model'] = $item;
        }

        return $this->searchResultFactory->create($list->getTotalCount(), $listArray);
    }
}
