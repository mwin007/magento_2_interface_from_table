<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/*
 * USAGE:
 * php bin/magento devtools:module:create-repository-interface <name_of_table> "\Fully\Qualified\Class\Name"
 * php bin/magento devtools:module:create-repository-interface customer_product_review "\Ustraa\Testimonials\Model\ProductReview"
 */

namespace DevTools\Cli\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use DevTools\Cli\Model\RepositoryBuilder;

class CreateRepositoryInterface extends Command
{
    const MODEL_PATH_ARGUMENT = 'model';
    const TABLE_NAME_ARGUMENT = 'table';

    protected $_repositoryBuilder;

    public function __construct(
        RepositoryBuilder $repositoryBuilder
    )
    {
        $this->_repositoryBuilder = $repositoryBuilder;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('devtools:module:create-repository-interface')
            ->setDescription("Creates a model, resource model, repository and their interfaces using given table's columns. See help for more info.")
            ->setDefinition([
                new InputArgument(
                    self::TABLE_NAME_ARGUMENT,
                    InputArgument::REQUIRED,
                    'Table Name'
                ),
                new InputArgument(
                    self::MODEL_PATH_ARGUMENT,
                    InputArgument::REQUIRED,
                    'Model Name'
                )
            ])
            ->setHelp(<<<EOT
Create all model related files for a table in a flash.

This command creates the following files:
<info>*</info> A model class and its interface, with setters and getters populated from the given table's columns
<info>*</info> Its resource and resource collection model classes
<info>*</info> Its repository class and interface for web APIs
<info>*</info> A SearchResult class and interface for querying and filtering its collection
<info>*</info> Model, repository, and search result DI pairings added in the module's di.xml
<info>*</info> Standard routes added to the module's webapi.xml for getById(), getList(), save(), delete() methods
<info>*</info> di.xml and webapi.xml files are created first if not found

Process is aborted if:
(A) The given table doesn't exist in the database.
(B) The given class already exists in the module.

Usage:
    <comment>php %command.full_name% <name_of_table> "\Fully\Qualified\Class\Name"</comment>
    <comment>php %command.full_name% customer_product_review "\Ustraa\Testimonials\Model\ProductReview"</comment>

Outputs <info>Job finished.<info>
EOT
        );

        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tableName = $input->getArgument(self::TABLE_NAME_ARGUMENT);
        $modelPath = $input->getArgument(self::MODEL_PATH_ARGUMENT);

        // Initialize the repository builder by proving user input table name and model class file path
        $this->_repositoryBuilder->setTableName($tableName)
            ->setModelPath($modelPath)
            ->configure();

        // Create all the required files
        $this->_repositoryBuilder->createModelFiles();

        $output->writeln('<info>Job finished.<info>');
    }
}
