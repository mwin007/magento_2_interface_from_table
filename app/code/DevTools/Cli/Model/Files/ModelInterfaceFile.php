<?php

namespace DevTools\Cli\Model\Files;

use DevTools\Cli\Model\FileBuilder;

class ModelInterfaceFile extends FileBuilder
{
    protected $_modelInterfaceSetterGetter;

    /**
     * Constructor
     */
    public function __construct(
        ModelInterfaceSetterGetter $modelInterfaceSetterGetter
    )
    {
        $this->_modelInterfaceSetterGetter = $modelInterfaceSetterGetter;

        $this->_templateName = 'ModelInterfaceFile';
        $this->_pathRelativeToModule = 'Api/Data';
        $this->_filePrefix = '';
        $this->_fileSuffix = 'Interface';
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
     * The model interface file contains declarations for setters and getters for each column name.
     * They are created using a separate class and appended to the main class template.
     */
    public function addSettersGetters()
    {
        $this->_template .= $this->_modelInterfaceSetterGetter->create($this->_config)->getTemplate();
        return $this;
    }
}
