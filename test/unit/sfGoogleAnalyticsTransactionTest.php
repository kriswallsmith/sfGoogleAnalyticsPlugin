<?php

require dirname(__FILE__).'/../bootstrap/unit.php';

require $ga_lib_dir.'/transaction/sfGoogleAnalyticsTransaction.class.php';
require $ga_lib_dir.'/transaction/sfGoogleAnalyticsItem.class.php';

$t = new lime_test(12, new lime_output_color);

$t->diag('sfGoogleAnalyticsTransaction');

$trans = new sfGoogleAnalyticsTransaction;

$t->isa_ok($trans, 'sfGoogleAnalyticsTransaction', 'transaction instantiated ok');
$t->is_deeply($trans->getValues(), array_fill(0, 8, null), 'getValues empty');

$trans->setOrderId('orderid');
$trans->setStoreName('storename');
$trans->setTotal(100);
$trans->setTax(1.25);
$trans->setShipping(4.99);
$trans->setCity('new york');
$trans->setState('ny');
$trans->setCountry('usa');

$item1 = new sfGoogleAnalyticsItem;
$item2 = new sfGoogleAnalyticsItem;
$trans->addItem($item1);
$trans->addItem($item2);

$t->is($trans->getOrderId(), 'orderid', 'order id ok');
$t->is($trans->getStoreName(), 'storename', 'store name ok');
$t->is($trans->getTotal(), 100, 'total ok');
$t->is($trans->getTax(), 1.25, 'tax ok');
$t->is($trans->getShipping(), 4.99, 'shipping ok');
$t->is($trans->getCity(), 'new york', 'city ok');
$t->is($trans->getState(), 'ny', 'state ok');
$t->is($trans->getCountry(), 'usa', 'country ok');
$t->is_deeply($trans->getItems(), array($item1, $item2), 'items ok');

$t->is_deeply($trans->getValues(), array('orderid', 'storename', 100, 1.25, 4.99, 'new york', 'ny', 'usa'), 'getValues ok');
