<?php
declare(strict_types=1);

namespace Mageplaza\BlogGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\ConfigInterface;
use Magento\Framework\GraphQl\Query\Resolver\Argument\FieldEntityAttributesInterface;

/**
 * Class FilterArgument
 * @package Mageplaza\BlogGraphQl\Model\Resolver
 */
class FilterArgument implements FieldEntityAttributesInterface
{
    /** @var ConfigInterface */
    private $config;

    /**
     * FilterArgument constructor.
     *
     * @param ConfigInterface $config
     */
    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * @return array
     */
    public function getEntityAttributes(): array
    {
        $fields = [];
        /** @var Field $field */
        foreach ($this->config->getConfigElement('Post')->getFields() as $field) {
            $fields[$field->getName()] = '';
        }

        return array_keys($fields);
    }
}