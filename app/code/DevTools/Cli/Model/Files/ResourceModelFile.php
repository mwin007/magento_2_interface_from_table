<?php

namespace DevTools\Cli\Model\Files;

use DevTools\Cli\Model\FileBuilder;

class ResourceModelFile extends FileBuilder
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_templateName = 'ResourceModelFile';
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
}
