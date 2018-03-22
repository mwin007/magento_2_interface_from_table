<?php

namespace DevTools\Cli\Model\Files;

use DevTools\Cli\Model\FileBuilder;

class ModelSearchResultInterfaceFile extends FileBuilder
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_templateName = 'ModelSearchResultInterfaceFile';
        $this->_pathRelativeToModule = 'Api/Data';
        $this->_filePrefix = '';
        $this->_fileSuffix = 'SearchResultInterface';
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
