# wordpress-woocommerce-plugin

## Webhook rules
Hooks Smartpack WMS need endpoints to update different data for product, stock and order there will be some REST API endpoints for that. remeber to add a webhook key in the settings area else its have disabled security and all can sending webhook to your store. 

**Stock changed webhook**

/wp-json/smartpack-wms/v1/stock-changed


## Crontab
You need to be enable to use cronjob to sync data between SmartPack WMS and Woocommerce. We are using CLI tools inside the cron so we are sure its react in the same way when you execute your code.

``` bash
* * * * * /path/to/woocommerce wp smartpack:product:sync
* * * * * /path/to/woocommerce wp smartpack:order:sync
* * * * * /path/to/woocommerce wp smartpack:stock:sync
```

## Module Featuers
**WMS**

- Product
  - CLI: Full sync of products from shop to WMS integration
- Order
  - CLI: Sync all with shipment status
- Stock
  - Hook: update product stock on hook signals
  - CLI: Sync stock to be ajour from WMS