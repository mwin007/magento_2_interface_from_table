## Expose any table in Magento 2 for REST API usage with one command.

### Usage:

```
cd <path_to_magento_2_dir>
php bin/magento devtools:module:create-repository-interface <name_of_the_table> "\<Company>\<Module>\Model\<ModelName>"
```

### This command creates the following files:

#### (1) A model class and its interface, with setters and getters populated from the given table's columns

```
app/code/<Company>/<Module>/Model/<ModelName>.php
app/code/<Company>/<Module>/Api/Data/<ModelNameInterface>.php
```

#### (2) Its resource and resource collection model classes

```
app/code/<Company>/<Module>/Model/ResourceModel/<ModelName>.php
app/code/<Company>/<Module>/Model/ResourceModel/<ModelName>/Collection.php
```

> Primary key is automatically identified from the table schema.

#### (3) Its repository class and interface for web APIs

```
app/code/<Company>/<Module>/Model/<ModelName>Repository.php
app/code/<Company>/<Module>/Api/<ModelName>RepositoryInterface.php
```

The repository exposes the following methods:

- __Get table entity by the primary key of the table__
```
[GET] {{base_url}}/V1/api/<module>/<model>/:id
```

- __Get table entity collection__
```
[GET] {{base_url}}/V1/api/<module>/<model>
```

- __* Note__

  - The repository makes use of Magento 2's powerful `SearchCriteriaInterface`.
  - You can read more about how to build a search criteria from Magento 2's official Docs: http://devdocs.magento.com/guides/v2.1/rest/performing-searches.html


- __Add a new table entity__
```
[POST] {{base_url}}/V1/api/<module>/<model>
```

- __Update a table entity__
```
[PUT] {{base_url}}/V1/api/<module>/<model>
```

#### (4) A SearchResult class and interface for querying and filtering its collection

```
app/code/<Company>/<Module>/Model/<ModelName>SearchResult.php
app/code/<Company>/<Module>/Api/Data/<ModelName>SearchResultInterface.php
```

#### (5) Model, repository, and search result DI pairings are added in the module's `di.xml`

```xml
<preference for="<Company>\<Module>\Api\<ModelName>RepositoryInterface" type="<Company>\<Module>\Model\<ModelName>Repository" />
<preference for="<Company>\<Module>\Api\Data\<ModelName>Interface" type="<Company>\<Module>\Model\<ModelName>" />
<preference for="<Company>\<Module>\Api\Data\<ModelName>SearchResultInterface" type="<Company>\<Module>\Model\<ModelName>SearchResult"/>
```

#### (6) Standard routes are added to the module's `webapi.xml` for getById(), getList(), save(), delete() methods

```xml
    <!-- Get table entity by ID -->
    <route method="GET" url="/V1/api/<module>/<model>/:id">
        <service class="<Company>\<Module>\Api\<ModelName>RepositoryInterface" method="getById"/>
        <resources>
            <resource ref="Magento_Backend::admin"/>
        </resources>
    </route>

    <!-- Get table entity collection -->
    <route method="GET" url="/V1/api/<module>/<model>">
        <service class="<Company>\<Module>\Api\<ModelName>RepositoryInterface" method="getList"/>
        <resources>
            <resource ref="Magento_Backend::admin"/>
        </resources>
    </route>

    <!-- Save a table entity -->
    <route method="POST" url="/V1/api/<module>/<model>">
        <service class="<Company>\<Module>\Api\<ModelName>RepositoryInterface" method="save"/>
        <resources>
            <resource ref="Magento_Backend::admin"/>
        </resources>
    </route>

    <!-- Update a table entity -->
    <route method="PUT" url="/V1/api/<module>/<model>">
        <service class="<Company>\<Module>\Api\<ModelName>RepositoryInterface" method="save"/>
        <resources>
            <resource ref="Magento_Backend::admin"/>
        </resources>
    </route>
```

#### Note:

- If no module exists in `<Company>\<Module>` directory, it will be automatically created along with `registration.php` and `etc/module.xml` files.
- If `di.xml` and `webapi.xml` files are not present in the module, they are created.
- If `di.xml` and `webapi.xml` files already exist, the additions are made at the end of the files.
- By default, the APIs exposed by this repository are available only to `Admin` users. Modify the resources in `webapi.xml` to suit to your preference.

### Process is aborted if:

1. The given table doesn't exist in the database.
2. The given class already exists in the module.

### Installation:

- Download or clone this repository to any folder on your machine.
- Copy the `app` folder from the downloaded / cloned contents into your `Magento 2` project's root directory.
- File system will ask your permission to merge the `app` folder with existing `app` folder. Allow it.
