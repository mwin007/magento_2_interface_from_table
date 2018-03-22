<?php

namespace DevTools\Cli\Model\Files;

use DevTools\Cli\Model\FileBuilder;

class ModelSearchResultFile extends FileBuilder
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_templateName = 'ModelSearchResultFile';
        $this->_pathRelativeToModule = 'Model';
        $this->_filePrefix = '';
        $this->_fileSuffix = 'SearchResult';
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
}
