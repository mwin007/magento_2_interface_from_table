<?php

namespace DevTools\Cli\Model\Files;

use DevTools\Cli\Model\FileBuilder;
use DevTools\Cli\Helper\Data as Helper;

class ModelSetterGetter extends FileBuilder
{
    /**
     * Constructor
     */
    public function __construct(
        Helper $helper
    )
    {
        $this->_helper = $helper;
        $this->_templateName = 'ModelSetterGetter';
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
        $attributes = $this->getAttributesFromTable();

        if (!$attributes || !is_array($attributes)) {
            return;
        }

        $allAttributes = '';

        foreach ($attributes as $attribute) {
            $templateCopy = $this->_template;

            $attributeTemplate = $this->fillVariables($templateCopy, [
                'attribute_function_name' => $this->_helper->camelCase($attribute, false),
                'attribute_code' => $attribute,
                'attribute_as_variable_name' => $this->_helper->camelCase($attribute)
            ]);

            $allAttributes .= $attributeTemplate;
        }

        $this->_template = $allAttributes;
        return $this;
    }
}
