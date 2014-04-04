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
