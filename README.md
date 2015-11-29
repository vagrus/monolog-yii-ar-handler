# Monolog Yii ActiveRecord Handler

These handler make it easy to send logs to Yii AR model.

## Installation

Install the latest version with

```bash
$ composer require vagrus/monolog-yii-ar-handler
```

## Basic Usage

```php
<?php

use Monolog\Logger;
use Vagrus\Monolog\Handler\YiiArHandler;

$mappingSettings = array(
    '*' => 'modelProperty', // required
);

// create a log channel
$log = new Logger('name');
$log->pushHandler(new YiiArHandler('modelName', $mappingSettings, Logger::WARNING));

// add records to the log
$log->warning('Foo');
```

## Extended Usage

```php
<?php

use Monolog\Logger;
use Vagrus\Monolog\Handler\YiiArHandler;

$mappingSettings = array(
    '*' => 'property1', // required
    'contextVar1' => 'property2',
    'contextVar2' => 'property3',
);

// create a log channel
$log = new Logger('name');
$log->pushHandler(new YiiArHandler('modelName', $mappingSettings, Logger::WARNING));

// add records to the log
// 'Foo' will be written to model's property1, 'some context value' to property2, etc. 
$context = array(
    'contextVar1' => 'some context value',
    'contextVar2' => 'other context value',
);
$log->warning('Foo', $context);
```
