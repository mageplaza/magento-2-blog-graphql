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

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\ConfigInterface;
use Magento\Framework\GraphQl\Query\Resolver\Argument\FieldEntityAttributesInterface;
use Mageplaza\Blog\Helper\Data;

/**
 * Class FilterArgument
 * @package Mageplaza\BlogGraphQl\Model\Resolver
 */
class FilterArgument implements FieldEntityAttributesInterface
{
    /** @var ConfigInterface */
    private $config;
    /**
     * @var Data
     */
    private $helperData;

    /**
     * FilterArgument constructor.
     *
     * @param ConfigInterface $config
     * @param Data $helperData
     */
    public function __construct(
        ConfigInterface $config,
        Data $helperData
    ) {
        $this->config = $config;
        $this->helperData = $helperData;
    }

    /**
     * @return array
     */
    public function getEntityAttributes(): array
    {
        $entities = ['Post', 'Topic', 'Category', 'Comment', 'Product'];
        $fields   = [];
        foreach ($entities as $entity) {
            /** @var Field $field */
            foreach ($this->config->getConfigElement($entity)->getFields() as $field) {
                $fieldName          = $field->getName();
                $fields[$fieldName] = ['fieldName' => $fieldName];
            }
        }

        return $this->helperData->versionCompare('2.3.4') ? $fields : array_keys($fields);
    }
}
