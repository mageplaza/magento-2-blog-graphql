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
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\Argument\SearchCriteria\Builder as SearchCriteriaBuilder;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Mageplaza\Blog\Api\BlogRepositoryInterface;
use Mageplaza\Blog\Helper\Data;
use Mageplaza\BlogGraphQl\Model\Resolver\Filter\Query\Filter;

/**
 * Class MonthlyArchive
 * @package Mageplaza\BlogGraphQl\Model\Resolver
 */
class MonthlyArchive implements ResolverInterface
{
    /**
     * @var Data
     */
    private $_helperData;

    /**
     * @var BlogRepositoryInterface
     */
    private $blogRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var Filter
     */
    private $filterQuery;

    /**
     * MonthlyArchive constructor.
     *
     * @param Data $helperData
     * @param BlogRepositoryInterface $blogRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param Filter $filterQuery
     */
    public function __construct(
        Data $helperData,
        BlogRepositoryInterface $blogRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Filter $filterQuery
    ) {
        $this->_helperData           = $helperData;
        $this->blogRepository        = $blogRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterQuery           = $filterQuery;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $this->validateArgs($args);
        $year           = null;
        $storeId        = null;
        $monthly        = null;
        $monthlyArchive = null;

        if (isset($args['monthly'])) {
            $monthly = $args['monthly'];
        }

        if (isset($args['storeId'])) {
            $storeId = $args['storeId'];
        }

        if (isset($args['year'])) {
            $year = $args['year'];
        }

        if ($monthly || $year) {
            $monthlyArchive = $year . '-' . $monthly;
        }

        $archives = $this->getMonthlyArchive($monthlyArchive, $storeId, $args);

        return [
            'total_count' => count($archives),
            'items'       => $archives
        ];
    }

    /**
     * @param $monthlyArchive
     * @param $storeId
     * @param $args
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getMonthlyArchive($monthlyArchive, $storeId, $args)
    {
        $items = [];
        $monthlyAr = $this->blogRepository->getMonthlyArchive();
        foreach ($monthlyAr as $archive) {
            $date = str_replace(',', '-', $archive->getLabel());
            $dateMonthlyArchive = $this->_helperData->dateTime->date('Y-m', $date);
            if ($monthlyArchive) {
                if (strpos($dateMonthlyArchive, $monthlyArchive) !== false) {
                    $collection     = $this->_helperData->getPostCollection('month', $dateMonthlyArchive, $storeId);
                    $searchCriteria = $this->searchCriteriaBuilder->build('posts', $args);
                    $searchResult   = $this->filterQuery->getResult($searchCriteria, 'post', $collection);
                    $items[] = [
                        'label'    => $archive->getLabel(),
                        'quantity' => $searchResult->getTotalCount(),
                        'items'    => $searchResult->getItemsSearchResult()
                    ];
                }
                continue;
            } else {
                $collection     = $this->_helperData->getPostCollection('month', $dateMonthlyArchive, $storeId);
                $searchCriteria = $this->searchCriteriaBuilder->build('posts', $args);
                $searchResult   = $this->filterQuery->getResult($searchCriteria, 'post', $collection);
                $items[] = [
                    'label'    => $archive->getLabel(),
                    'quantity' => $searchResult->getTotalCount(),
                    'items'    => $searchResult->getItemsSearchResult()
                ];
            }
        }

        return $items;
    }

    /**
     * @param array $args
     *
     * @throws GraphQlInputException
     */
    protected function validateArgs(array $args)
    {
        if (isset($args['monthly']) && ($args['monthly'] < 0 || $args['monthly'] > 12)) {
            throw new GraphQlInputException(__('The monthly value must be greater than 0 and equals or less than 12.'));
        }

        if (isset($args['monthly']) && !isset($args['year'])) {
            throw new GraphQlInputException(__('Please enter the year you want to search.'));
        }
    }
}
