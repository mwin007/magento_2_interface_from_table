<?php

namespace DevTools\Cli\Model\Files;

use DevTools\Cli\Model\FileBuilder;

class ModelFile extends FileBuilder
{
    protected $_modelSetterGetter;

    /**
     * Constructor
     */
    public function __construct(
        ModelSetterGetter $modelSetterGetter
    )
    {
        $this->_modelSetterGetter = $modelSetterGetter;

        $this->_templateName = 'ModelFile';
        $this->_pathRelativeToModule = 'Model';
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
        $this->addSettersGetters();
        $this->closeFile();

        return $this;
    }

    /**
     * The model file contains setters and getters for each column name.
     * They are created using a separate class and appended to the main class template.
     */
    public function addSettersGetters()
    {
        $this->_template .= $this->_modelSetterGetter->create($this->_config)->getTemplate();
        return $this;
    }
}
