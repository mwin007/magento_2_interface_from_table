<?php

namespace DevTools\Cli\Model\Files;

use DevTools\Cli\Model\FileBuilder;

class WebapiRoutes extends FileBuilder
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_templateName = 'WebapiRoutes';
        $this->_pathRelativeToModule = 'etc';
        $this->_filePrefix = '';
        $this->_fileSuffix = '';
    }

    /**
     * The initializer that sets the config, loads the template,
     * Converts the template to the final file
     */
    public function create($config)
    {
        parent::create($config);

        $this->prepareFilePath();
        $this->fillOwnVariables($config);

        return $this;
    }

    /**
     * Overridden the method from the parent, because the file path is different.
     */
    public function prepareFilePath()
    {
        $this->_filePath = $this->_config["module_path"] . '/' . $this->_pathRelativeToModule . '/webapi.xml';
        return $this;
    }

    /**
     * Overridden the method from the parent to add extra functionality.
     * First check if the file already exists. If not, attempt to create it.
     * Append the compiled template to it and save it using parent function.
     */
    public function save()
    {
        if (file_exists($this->_filePath)) {
            $existingFile = file_get_contents($this->_filePath, FILE_USE_INCLUDE_PATH);
            $this->_template = str_replace("</routes>", $this->_template, $existingFile);
        } else {
            $atTheStart = '<?xml version="1.0"?>
<routes xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:module:Magento_Webapi:etc/webapi.xsd">';
            $this->_template = $atTheStart . $this->_template;
        }

        parent::save();
        return $this;
    }
}
