StatusLib
=========

This is a library designed to demonstrate an [Apigility](http://apigility.org/) "Code-Connected"
REST API, and has been written in parallel with the [Apigility documentation](https://github.com/zfcampus/apigility-documentation).

It uses the following components:

- [rhumsaa/uuid](https://github.com/ramsey/uuid), a library for generating and validating UUIDs.
- [zfcampus/zf-configuration](https://github.com/zfcampus/zf-configuration), used for providing PHP
  files as one possible backend for reading/writing status messages.
- [zendframework/zend-config](https://framework.zend.com/) for the actual configuration writer used
  by the `zf-configuration` module.
- [zendframework/zend-db](https://framework.zend.com/), used for providing a database table as a
  backend for reading/writing status messages.
- [zendframework/zend-stdlib](https://framework.zend.com/), specifically the Hydrator subcomponent,
  for casting data from arrays to objects, and for the `ArrayUtils` class, which provides advanced
  array merging capabilities.
- [zendframework/zend-paginator](https://framework.zend.com/) for providing pagination.

It is written as a Zend Framework 2 module, but could potentially be dropped into other
applications; use the `StatusLib\*Factory` classes to see how dependencies might be injected.

Installation
------------

Use [Composer](https://getcomposer.org/) to install the library in your application:

```console
$ composer require zfcampus/statuslib-example:dev-master
```

If you are using this as part of a Zend Framework 2 or Apigility application, you will also need to
enable the module in your `config/application.config.php` file:

```php
return array(
    /* ... */
    'modules' => array(
        /* ... */
        'StatusLib',
    ),
    /* ... */
);
```

Configuration
-------------

When used as a Zend Framework 2 module, you may define the following configuration values in order
to tell the library which adapter to use, and what options to pass to that adapter.

```php
array(
    'statuslib' => array(
        'db' => 'Name of service providing DB adapter',
        'table' => 'Name of database table within db to use',
        'array_mapper_path' => 'path to PHP file returning an array for use with ArrayMapper',
    ),
    'service_manager' => array(
        'aliases' => array(
            // Set to either 'StatusLib\ArrayMapper' or 'StatusLib\TableGatewayMapper'
            'StatusLib\Mapper' => 'StatusLib\ArrayMapper',
        ),
    ),
)
```

For purposes of the Apigility examples, we suggest the following:

- Create a PHP file in your application's `data/` directory named `statuslib.php` that returns an
  array:

  ```php
  <?php
  return array();
  ```

- Edit your application's `config/autoload/local.php` file to set the `array_mapper_path`
  configuration value to `data/statuslib.php`:

  ```php
  <?php
  return array(
      /* ... */
      'statuslib' => array(
        'array_mapper_path' => 'data/statuslib.php',
      ),
  );
  ```

The above will provide the minimum necessary requirements for experimenting with the library in
order to test an API.

Using a database
----------------

The file `data/statuslib.sqlite.sql` contains a [SQLite](https://www.sqlite.org/) schema. You can
create a SQLite database using:

```console
$ sqlite3 statuslib.db < path/to/data/statuslib.sqlite.sql
```

The schema can be either used directly by other databases, or easily modified to work with other
databases.


StatusLib in a New ZF2 Project
------------------------------

1. Create a new ZF2 project from scratch, we'll use `my-project` as our project folder:

  ```console
  $ composer create-project -sdev --repository-url="https://packages.zendframework.com" zendframework/skeleton-application my-project
  ```

2. Install the StatusLib module:

  ```console
  $ composer require zfcampus/zf-configuration:dev-master
  ```

3. Build a DataSource

    - Option A: Array data source:

    First, copy the sample array to the `data` directory of thet application:

    ```console
    $ cp vendor/zfcampus/statuslib-example/data/sample-data/array-data.php data/status.data.php
    ```

    Then, configure this datasource by setting up a `local.php` configuration file:

    ```console
    $ cp config/autoload/local.php.dist config/autoload/local.php
    ```

    Next, add the StatusLib specific configuration for an array based data source:

    ```php
    'statuslib' => array(
        'array_mapper_path' => 'data/status.data.php',
    ),
    'service_manager' => array(
        'aliases' => array(
            'StatusLib\Mapper' => 'StatusLib\ArrayMapper',
        ),
    ),
    ```

    - Option B: Sqlite data source:

    First, create a sqlite3 database, and fill it with the sample data:

    ```console
    $ sqlite3 status.db < vendor/zfcampus/statuslib-example/data/statuslib.sqlite.sql
    $ sqlite3 status.db < vendor/zfcampus/statuslib-example/data/sample-data/db-sqlite-insert.sql
    ```

    Then, configure this datasource by setting up a `local.php` configuration file:

    ```console
    $ cp config/autoload/local.php.dist config/autoload/local.php
    ```

    Next, add the StatusLib specific configuration for a sqlite database based data source:

    ```php
    'db' => array(
        'adapters' => array(
            'MyDb' => array(
                'driver' => 'pdo_sqlite',
                'database' => __DIR__ . '/../../data/statuslib.db'
            )
        )
    ),
    'statuslib' => array(
        'db' => 'MyDb',
        'table' => 'status',
    ),
    'service_manager' => array(
        'aliases' => array(
            'StatusLib\Mapper' => 'StatusLib\TableGatewayMapper',
        ),
        'abstract_factories' => array(
            'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterAbstractServiceFactory',
        )
    ),
    ```

4. Alter the stock controller and view to prove the data source is working:

    -  Alter the index view `module/Application/view/application/index/index.phtml`, replacing it with:

    ```php
    <?php foreach ($this->statuses as $status): ?>
        <?php echo $status->message . ' by ' . $status->user; ?><br>
    <?php endforeach; ?>
    ```

    - Alter the `module/Application/src/Application/Controller/IndexController.php`'s `indexAction` method:

    ```php
    $statusMapper = $this->serviceLocator->get('StatusLib\Mapper');
    $statuses = $statusMapper->fetchAll();
    return new ViewModel(array('statuses' => $statuses));
    ```
