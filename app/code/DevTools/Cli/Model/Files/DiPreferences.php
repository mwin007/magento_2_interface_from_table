<?php

namespace DevTools\Cli\Model\Files;

use DevTools\Cli\Model\FileBuilder;

class DiPreferences extends FileBuilder
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_templateName = 'DiPreferences';
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
        $this->_filePath = $this->_config["module_path"] . '/' . $this->_pathRelativeToModule . '/di.xml';
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
            $this->_template = str_replace("</config>", $this->_template, $existingFile);
        } else {
            $atTheStart = '<?xml version="1.0"?>
<!--
/**
 * Copyright 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
-->
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="../../../../../lib/internal/Magento/Framework/ObjectManager/etc/config.xsd">';
            $this->_template = $atTheStart . $this->_template;
        }

        parent::save();
        return $this;
    }
}
