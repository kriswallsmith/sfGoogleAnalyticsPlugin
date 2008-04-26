<?php

require dirname(__FILE__).'/../bootstrap/unit.php';
require $sf_symfony_lib_dir.'/filter/sfFilter.class.php';
require $ga_lib_dir.'/filter/sfGoogleAnalyticsFilter.class.php';

$t = new lime_test(1, new lime_output_color);

$t->ok(class_exists('sfGoogleAnalyticsFilter'), 'filter class parses ok');
