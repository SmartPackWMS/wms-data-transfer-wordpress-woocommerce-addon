# wms-data-transfer-wordpress-plugin-woocommerce

## Webhook rules
Hooks Smartpack WMS need endpoints to update different data for product, stock and order there will be some REST API endpoints for that. remeber to add a webhook key in the settings area else its have disabled security and all can sending webhook to your store. 

**Stock changed webhook**

{domain-path}/wp-json/smartpack-wms/v1/stock-changed

**Order changed webhook**

{domain-path}/wp-json/smartpack-wms/v1/order-changed


## Crontab
You need to be enable to use cronjob to sync data between SmartPack WMS and Woocommerce. We are using CLI tools inside the cron so we are sure its react in the same way when you execute your code.

``` bash
* * * * * /path/to/woocommerce wp smartpack:product:sync
* * * * * /path/to/woocommerce wp smartpack:order:sync
```

```
wp cron event run --due-now --allow-root
wp cron event delete wms_cron_product_hook --allow-root
```


## Module Featuers
**WMS**

- Product
  - CLI: Full sync of products from shop to WMS integration
- Order
  - CLI: Sync all with shipment status
- Stock
  - Hook: update product stock on hook signals
  - CLI: Full stock sync to be ajour from WMS into Woo

## Woocommerce order process
Custom orders will not be automatically transfered to the WMS integration.

![](/assets/images/woocommerce-order-process-diagram.webp)