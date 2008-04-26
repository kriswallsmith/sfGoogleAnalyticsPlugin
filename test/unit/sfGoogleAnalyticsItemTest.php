<?php

require dirname(__FILE__).'/../bootstrap/unit.php';
require $ga_lib_dir.'/transaction/sfGoogleAnalyticsItem.class.php';

$t = new lime_test(9, new lime_output_color);

$t->diag('sfGoogleAnalyticsItem');

$item = new sfGoogleAnalyticsItem;

$t->isa_ok($item, 'sfGoogleAnalyticsItem', 'item instantiated ok');
$t->is_deeply($item->getValues(), array_fill(0, 6, null), 'getValues empty');

$item->setOrderId('orderid');
$item->setSku('mysku');
$item->setProductName('a product');
$item->setCategory('socks');
$item->setUnitPrice(4.99);
$item->setQuantity(5);

$t->is($item->getOrderId(), 'orderid', 'order id ok');
$t->is($item->getSku(), 'mysku', 'sku ok');
$t->is($item->getProductName(), 'a product', 'product name ok');
$t->is($item->getCategory(), 'socks', 'category ok');
$t->is($item->getUnitPrice(), 4.99, 'unit price ok');
$t->is($item->getQuantity(), 5, 'quantity ok');

$t->is_deeply($item->getValues(), array('orderid', 'mysku', 'a product', 'socks', 4.99, 5), 'getValues ok');
