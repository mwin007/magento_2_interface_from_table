<?php

namespace DevTools\Cli\Model;

class FileBuilder
{
    protected $_helper;
    protected $_config;
    protected $_template;
    protected $_templateName;
    protected $_pathRelativeToModule;
    protected $_filePrefix;
    protected $_fileSuffix;
    protected $_fileExtension = '.php';
    protected $_filePath;
    protected $_file;

    /**
     * Constructor
     */
    public function __construct()
    {
        //
    }

    /**
     * Returns the compiled template
     */
    public function getTemplate()
    {
        return $this->_template;
    }

    /**
     * Returns the file handler for writing to the file
     */
    public function getFile()
    {
        if (!$this->_file) {
            $this->_file = fopen($this->_filePath, "w") or die("Unable to open file!");
        }

        return $this->_file;
    }

    /**
     * Think of this as the initializer that sets the config and loads the txt template
     */
    public function create($config)
    {
        if (!$config) {
            return;
        }

        $this->_config = $config;

        if (!$this->_templateName) {
            return;
        }

        $this->_template = $this->getFileTemplate($this->_templateName);

        if (!$this->_template) {
            return;
        }

        return $this;
    }

    /**
     * Loads a text file corresponding to the given template name.
     * This text file is the skeleton that is used to create the actual file.
     */
    public function getFileTemplate($template)
    {
        if (!$template) {
            return;
        }

        return file_get_contents(__DIR__ . '/../Templates/' . $template . '.txt', FILE_USE_INCLUDE_PATH);
    }

    /**
     * This is the path where the finished template will be saved eventually
     */
    public function prepareFilePath()
    {
        $this->_filePath = $this->_config["module_path"] . '/' . $this->_pathRelativeToModule . '/' . $this->_filePrefix . $this->_config["model_name"] . $this->_fileSuffix . $this->_fileExtension;
        return $this;
    }

    /**
     * Fill any provided template (string, not path) with the given variables.
     * The template contains variables in {{variable_key}} format.
     * The variables array contains key value pairs in the "variable_key" => "its_value" format.
     */
    public function fillVariables($template, $variables)
    {
        if (!$template) {
            return;
        }

        foreach ($variables as $variable => $value) {
            $template = str_replace("{{" . $variable . "}}", $value, $template);
        }

        return $template;
    }

    /**
     * Fills the member template with the given variables
     */
    public function fillOwnVariables($variables)
    {
        $this->_template = $this->fillVariables($this->_template, $variables);
        return $this;
    }

    /**
     * Gets the table name from the config and loads its columns.
     * @param boolean $full
     * If $full is true, entire table description is returned, otherwise only column names.
     * Full table is required when the columns' data types or table's primary key is required.
     */
    public function getAttributesFromTable($full = false)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();

        $tableSchema = $connection->describeTable($this->_config["table_name"]);

        if ($full) {
            return $tableSchema;
        }

        $columnNames = [];
        foreach ($tableSchema as $tableColumn) {
            $columnNames[] = $tableColumn['COLUMN_NAME'];
        }

        return $columnNames;
    }

    /**
     * Returns the primary key of the table name specified in config
     */
    public function getTablePrimaryKey()
    {
        $tableSchema = $this->getAttributesFromTable(true);

        foreach ($tableSchema as $tableColumn) {
            if ($tableColumn['PRIMARY']) {
                return $tableColumn['COLUMN_NAME'];
            }
        }

        return 'id';
    }

    /**
     * Adds a closing brace to close the class
     */
    public function closeFile()
    {
        $this->_template .= "}\n";
        return $this;
    }

    /**
     * Writes the compiled template to the designated file
     */
    public function save()
    {
        fwrite($this->getFile(), $this->_template);
        fclose($this->getFile());

        return $this;
    }
}
