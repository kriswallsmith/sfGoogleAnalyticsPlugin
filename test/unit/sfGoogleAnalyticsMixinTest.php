<?php

require dirname(__FILE__).'/../bootstrap/unit.php';
require $ga_lib_dir.'/mixin/sfGoogleAnalyticsMixin.class.php';

$t = new lime_test(1, new lime_output_color);

$t->ok(class_exists('sfGoogleAnalyticsMixin'), 'action mixin parses ok');
