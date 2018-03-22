<?php

namespace DevTools\Cli\Model\Files;

use DevTools\Cli\Model\FileBuilder;
use DevTools\Cli\Helper\Data as Helper;

class ModelInterfaceSetterGetter extends FileBuilder
{
    /**
     * Constructor
     */
    public function __construct(
        Helper $helper
    )
    {
        $this->_helper = $helper;
        $this->_templateName = 'ModelInterfaceSetterGetter';
    }

    /**
     * The initializer that sets the config, loads the template,
     * Converts the template to the final file
     */
    public function create($config)
    {
        parent::create($config);

        $this->populateAttributes();
        return $this;
    }

    /**
     * Gets all the columns for the given table name and creates a setter getter pair for each column.
     * Collective template of all setters and getters is returned back to the main class file.
     */
    public function populateAttributes()
    {
        $attributes = $this->getAttributesFromTable(true);

        if (!$attributes || !is_array($attributes)) {
            return;
        }

        $allAttributes = '';

        foreach ($attributes as $attribute) {
            $templateCopy = $this->_template;
            $attributeName = $attribute['COLUMN_NAME'];
            $attributeType = $this->_helper->getParamTypeFromDataType($attribute['DATA_TYPE']);

            $attributeTemplate = $this->fillVariables($templateCopy, [
                'attribute_function_comment' => $this->_helper->titleCase($attributeName, false),
                'attribute_type' => $attributeType,
                'attribute_function_name' => $this->_helper->camelCase($attributeName, false),
                'attribute_as_variable_name' => $this->_helper->camelCase($attributeName)
            ]);

            $allAttributes .= $attributeTemplate;
        }

        $this->_template = $allAttributes;
        return $this;
    }
}
