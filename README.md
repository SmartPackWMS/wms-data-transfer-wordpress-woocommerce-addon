# wordpress-woocommerce-plugin

## Webhook rules
Hooks Smartpack WMS need endpoints to update different data for product, stock and order there will be some REST API endpoints for that. remeber to add a webhook key in the settings area else its have disabled security and all can sending webhook to your store. 

**Stock changed webhook**

/wp-json/smartpack-wms/v1/stock-changed

## Module Featuers
**WMS**

- Product
  - [in-process] CLI: Full sync of products from shop to WMS integration
  - [coming] Cron: sync product changes from shop to WMS integration
- Order
  - [coming] CLI: Sync all with shipment status
- Stock
  - Hook: update product stock on hook signals