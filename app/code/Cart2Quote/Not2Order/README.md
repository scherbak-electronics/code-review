# Not2Order for Magento 2 #

Not2Order for Magento 2 is the next version of the popular Not2Order module for Magento. Like it’s predecessor it allows you to hide the order button or / and the product price.

With Not2Order you can disable the Order functionality and/or hide prices for all or individual products per store view. Simply manage the orderability and visibility of your prices globally, per individual product, storeview or complete website. This extension gives you full control whether your products should show prices and "Add to Cart" buttons, and which products have their prices and/or "Add to Cart" buttons hidden.

The purpose of the Not2Order module is to hide the order button or price in certain circumstances. This allows users to use the Magento platform as a product catalog or make it possible to only show prices or order products in certain circumstances.

## Project details ##

***Latest Stable Release:*** Version 2.0.8 - Stable ( 2019-12-27 )


## Installation ##

***Requirements: ***

We require a Magento 2 installation for Not2Order to work.

Magento 2.1, 2.2 and 2.3 Open Source or Commerce

***Step 1 - Copy the Not2Order files***

Unpack the Not2Order files and copy the Cart2Quote directory to your /app/code directory.

***Step 2 - Enable the Not2Order Module***

To enable Not2Order you need to open your terminal and navigate to your Magento root directory. From your root directory execute:

```
php bin/magento module:enable -c
```

***Step 3 - Execute the database scripts***

To make sure all setup scripts are executed run the following command in your terminal from your Magento root directory:

```
php bin/magento setup:upgrade
```

***Step 4 - Refresh your cache***

Last but not least, you need to refresh your cache. You can do this in the Magento admin panel but since we are already working from the command line the following command is a lot quicker.

```
php bin/magento cache:clean
```
