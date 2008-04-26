<?php

$_test_dir = realpath(dirname(__FILE__).'/..');
$_root_dir = realpath(file_exists($_test_dir.'/../symfony') ? ($_test_dir.'/..') : ($_test_dir.'/../../..'));

if (false !== strpos(file_get_contents($_root_dir.'/symfony'), 'ProjectConfiguration'))
{
  // symfony 1.1 bootstrap
  require_once $_root_dir.'/config/ProjectConfiguration.class.php';
  $configuration = new ProjectConfiguration($_root_dir);
  $sf_symfony_lib_dir = $configuration->getSymfonyLibDir();
}
else
{
  // symfony 1.0 bootstrap
  define('SF_ROOT_DIR', $_root_dir);
  include SF_ROOT_DIR.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php';
}

require_once $sf_symfony_lib_dir.'/vendor/lime/lime.php';
$ga_lib_dir = $_root_dir.'/plugins/sfGoogleAnalyticsPlugin/lib';
