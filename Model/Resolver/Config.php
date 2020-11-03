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
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Mageplaza\Blog\Helper\Data;

/**
 * Class Config
 * @package Mageplaza\BlogGraphQl\Model\Resolver
 */
class Config implements ResolverInterface
{
    /**
     * @var Data
     */
    private $_helperData;

    /**
     * Posts constructor.
     *
     * @param Data $helperData
     */
    public function __construct(
        Data $helperData
    ) {
        $this->_helperData = $helperData;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $storeId = null;

        if (isset($args['storeId'])) {
            $storeId = $args['storeId'];
        }

        return [
            'general' => $this->getGeneralConfig($storeId),
            'sidebar' => $this->getSidebar($storeId),
            'seo'     => $this->getSeo($storeId)
        ];
    }

    /**
     * @param null $storeId
     * @return array
     */
    public function getGeneralConfig($storeId = null)
    {
        return [
            'name' => $this->_helperData->getConfigGeneral('name', $storeId),
            'toplinks' => $this->_helperData->getConfigGeneral('toplinks', $storeId),
            'display_author' => $this->_helperData->getConfigGeneral('display_author', $storeId),
            'display_style' => $this->_helperData->getConfigGeneral('display_style', $storeId),
            'font_color' => $this->_helperData->getConfigGeneral('font_color', $storeId)
        ];
    }

    /**
     * @param null $storeId
     * @return array
     */
    public function getSidebar($storeId = null)
    {
        return [
            'number_recent_posts'   => $this->_helperData->getConfigValue(
                'blog/sidebar/number_recent_posts', $storeId
            ),
            'number_mostview_posts' => $this->_helperData->getConfigValue(
                'blog/sidebar/number_mostview_posts', $storeId
             )
        ];
    }

    /**
     * @param null $storeId
     * @return array
     */
    public function getSeo($storeId = null)
    {
        return [
            'meta_title'       => $this->_helperData->getConfigValue('blog/seo/meta_title', $storeId),
            'meta_description' => $this->_helperData->getConfigValue('blog/sidebar/meta_description', $storeId)
        ];
    }
}
