<?php

namespace DevTools\Cli\Model\Files;

use DevTools\Cli\Model\FileBuilder;

class ResourceModelCollectionFile extends FileBuilder
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_templateName = 'ResourceModelCollectionFile';
        $this->_pathRelativeToModule = 'Model/ResourceModel';
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

        $config["table_primary_key"] = $this->getTablePrimaryKey();

        $this->prepareFilePath();
        $this->fillOwnVariables($config);

        return $this;
    }

    public function prepareFilePath()
    {
        $this->_filePath = $this->_config["module_path"] . '/' . $this->_pathRelativeToModule . '/' . $this->_config["model_name"] . '/Collection.php';
        return $this;
    }

    public function closeFile()
    {
        $this->_template .= "}\n";
        return $this;
    }
}
