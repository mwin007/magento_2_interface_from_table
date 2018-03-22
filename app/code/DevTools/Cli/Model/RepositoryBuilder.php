<?php

namespace DevTools\Cli\Model;

use Magento\Framework\Module\ModuleListInterface;

use DevTools\Cli\Model\Files\ModelFile;
use DevTools\Cli\Model\Files\ModelInterfaceFile;
use DevTools\Cli\Model\Files\ResourceModelFile;
use DevTools\Cli\Model\Files\ResourceModelCollectionFile;
use DevTools\Cli\Model\Files\ModelRepositoryFile;
use DevTools\Cli\Model\Files\ModelRepositoryInterfaceFile;
use DevTools\Cli\Model\Files\ModelSearchResultFile;
use DevTools\Cli\Model\Files\ModelSearchResultInterfaceFile;
use DevTools\Cli\Model\Files\WebapiRoutes;
use DevTools\Cli\Model\Files\DiPreferences;

class RepositoryBuilder
{
    protected $_tableName;
    protected $_modelPath;

    protected $_moduleNameSpace;
    protected $_modelName;
    protected $_moduleName;

    protected $_objectManager;

    protected $_modelFile;
    protected $_modelInterfaceFile;
    protected $_resourceModelFile;
    protected $_resourceModelCollectionFile;
    protected $_modelRepositoryFile;
    protected $_modelRepositoryInterfaceFile;
    protected $_modelSearchResultFile;
    protected $_modelSearchResultInterfaceFile;
    protected $_webapiRoutes;
    protected $_diPreferences;

    protected $_magentoCodeFolderPath;
    protected $_modulePath;
    protected $_explodedPath;

    private $_moduleList;

    /**
     * Constructor
     */
    public function __construct(
        ModuleListInterface $moduleList,
        ModelFile $modelFile,
        ModelInterfaceFile $modelInterfaceFile,
        ResourceModelFile $resourceModelFile,
        ResourceModelCollectionFile $resourceModelCollectionFile,
        ModelRepositoryFile $modelRepositoryFile,
        ModelRepositoryInterfaceFile $modelRepositoryInterfaceFile,
        ModelSearchResultFile $modelSearchResultFile,
        ModelSearchResultInterfaceFile $modelSearchResultInterfaceFile,
        WebapiRoutes $webapiRoutes,
        DiPreferences $diPreferences
    )
    {
        $this->_moduleList = $moduleList;
        $this->_modelFile = $modelFile;
        $this->_modelInterfaceFile = $modelInterfaceFile;
        $this->_resourceModelFile = $resourceModelFile;
        $this->_resourceModelCollectionFile = $resourceModelCollectionFile;
        $this->_modelRepositoryFile = $modelRepositoryFile;
        $this->_modelRepositoryInterfaceFile = $modelRepositoryInterfaceFile;
        $this->_modelSearchResultFile = $modelSearchResultFile;
        $this->_modelSearchResultInterfaceFile = $modelSearchResultInterfaceFile;
        $this->_webapiRoutes = $webapiRoutes;
        $this->_diPreferences = $diPreferences;

        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }

    /**
     * [Required] Set table name, must be already created using Setup
     */
    public function setTableName($tableName)
    {
        $this->_tableName = $tableName;
        return $this;
    }

    /**
     * [Required] Set model class name for e.g. Company\Namespace\Model\Name
     */
    public function setModelPath($modelPath)
    {
        $this->_modelPath = $modelPath;
        return $this;
    }

    /**
     * Think of this as the initializer that sets all required information
     */
    public function configure()
    {
        $this->initializeParams();
        $this->validateModelPath();
        $this->validateTableName();
        $this->createModuleFolderIfNotFound();

        return $this;
    }

    /**
     * Extracts the name of model, module, its folder path, etc.
     */
    public function initializeParams()
    {
        $exploded = $this->explodePath();

        $this->_modelName = $exploded[3];
        $this->_moduleName = $exploded[0] . '_' . $exploded[1];
        $this->_moduleNameSpace = $exploded[0] . '\\' . $exploded[1];

        $directory = $this->_objectManager->get('\Magento\Framework\Filesystem\DirectoryList');

        $this->_magentoCodeFolderPath = $directory->getRoot() . '/app/code';
        $this->_modulePath = $this->_magentoCodeFolderPath . '/' . $exploded[0] . '/' . $exploded[1];

        return $this;
    }

    /**
     * Explodes the Model class name
     */
    public function explodePath() {
        if (!$this->_explodedPath) {
            $this->_explodedPath = explode("\\", trim($this->_modelPath, "\\"));
        }

        return $this->_explodedPath;
    }

    /**
     * Validate if the givem model class is in proper format
     */
    public function validateModelPath()
    {
        if (count($this->_explodedPath) != 4 || $this->_explodedPath[2] != "Model") {
            throw new \InvalidArgumentException('Model path is invalid.');
            return $this;
        }

        if (class_exists($this->_modelPath, false)) {
            throw new \InvalidArgumentException('Class file ' . $this->_modelPath . ' already exists.');
            return $this;
        }

        return $this;
    }

    /**
     * Validate if the given table name actually exists
     */
    public function validateTableName()
    {
        $resource = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();

        if (!$connection->isTableExists($this->_tableName)) {
            throw new \InvalidArgumentException('Table ' . $this->_tableName . ' not found.');
            return $this;
        }

        return $this;
    }

    /**
     * Creates all the folders necessary for this process
     */
    public function createModuleFolderIfNotFound()
    {
        $moduleDetails = $this->_moduleList->getOne($this->_moduleName);

        $folderPaths = [
            '',
            '/etc',
            '/Model/ResourceModel/' . $this->_modelName,
            '/Api/Data'
        ];

        foreach ($folderPaths as $folderPath) {
            if (!file_exists($this->_modulePath . $folderPath)) {
                mkdir($this->_modulePath . $folderPath, 0755, true);
            }
        }

        return $this;
    }

    /**
     * Creates all the class files including models, resource models, repository,
     * search result class, interfaces for all these class files.
     * Also adds DI preferences and web route configs. Creates these files if not found.
     */
    public function createModelFiles()
    {
        $config = [
            "module_namespace" => $this->_moduleNameSpace,
            "module_path" => $this->_modulePath,
            "model_name" => $this->_modelName,
            "model_name_lowercase" => lcfirst($this->_modelName),
            "table_name" => $this->_tableName,
            "route_path" => lcfirst($this->explodePath()[1])
        ];

        $this->_modelFile->create($config)->save();
        $this->_modelInterfaceFile->create($config)->save();
        $this->_resourceModelFile->create($config)->save();
        $this->_resourceModelCollectionFile->create($config)->save();
        $this->_modelRepositoryFile->create($config)->save();
        $this->_modelRepositoryInterfaceFile->create($config)->save();
        $this->_modelSearchResultFile->create($config)->save();
        $this->_modelSearchResultInterfaceFile->create($config)->save();
        $this->_webapiRoutes->create($config)->save();
        $this->_diPreferences->create($config)->save();

        return $this;
    }
}
