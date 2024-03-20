# Pronko_Elavon Module

## Overview
The Pronko_Elavon module provides integration between Elavon Payment and Magento 2.

## Installation

There are two ways of installing Pronko_Elavon module: Magento Component Manager or Composer.

### Component Manager

Here are steps required to install Elavon Payment extension via Component Manager:

* From your Magento® Admin access System -> Web Setup Wizard page.
* Enter Marketplace authentication keys. Please read about authentication keys generation.
* Navigate to Component Manager page.
* On the Component Manager page click “Sync” button to update your new purchased extensions.
* Click Install in the Action column for Realex Payments component.
* Follow Web Setup Wizard instructions.

Once module is prepared via composer package manager Pronko_Elavon module should be enabled.
```bash
$ bin/magento module:enable Pronko_Elavon  --clear-static-content
```

### Composer

In case you received a ZIP archive with Elavon Payment module for Magento 2 this section will show you how to install module via Composer package manager. 

Make sure you use right package with name which usually looks like _pronko-module_elavon-1.0.0.zip_, 
where 1.0.0 is a module version. Please note that module version might change.

Place pronko-module_elavon-1.0.0.zip package into the Magento 2 root directory. 
Usually absolute path is /var/www/magento/. 
Please consult with system administrator of the hosting provider you use for Magento 2 website.
Open composer.json file located in Magento 2 root directory and add new repository and require directives as follow:

```

“repositories”: [
    {
        “type”: “artifact”,
        “url”: “./”
    }
],

“require”: [
    “pronko/module-elavon”: “1.0.0”
]
```

From the command line execute the following command:

```bash
$ composer update
```

### Install Module
Enable Pronko_Elavon using the following command:

```bash
$ bin/magento module:enable Pronko_Elavon
```

Run `setup:upgrade` command:
```bash
$ bin/magento setup:upgrade
```

Now Elavon Payment is ready to be configured in the Magento Admin.
Navigate to _Magento Admin -> Stores -> Configuration -> Sales -> Payment Methods_ section.
Please read online documentation for the reference: https://www.pronkoconsulting.com/docs/elavon-payment-magento-2

## Tests

Pronko_Elavon module comes with Unit Tests (see `Test/Unit` directory).
In order to run tests use the following command:
```bash
$ bin/magento dev:tests:run unit
```

## About

### Requirements

Please refer to the composer.json file included into the root directory of the pronko/module-elavon package.

### License

Pronko_Elavon is licensed under the Custom license - see the `LICENSE` file for details