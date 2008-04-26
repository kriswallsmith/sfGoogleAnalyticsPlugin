<?php

require dirname(__FILE__).'/../bootstrap/unit.php';

require $ga_lib_dir.'/tracker/sfGoogleAnalyticsTracker.class.php';
require $ga_lib_dir.'/tracker/sfGoogleAnalyticsTrackerUrchin.class.php';

require $sf_symfony_lib_dir.'/util/sfParameterHolder.class.php';
require $sf_symfony_lib_dir.'/util/sfToolkit.class.php';

class sfContext
{
  public function getController()
  {
    return new sfController;
  }
}

class sfController
{
  public function genUrl($rule = array(), $absolute = false)
  {
    if ($rule == '@homepage')
    {
      return '/';
    }
  }
}

class sfConfig
{
  public static function get($key, $default = null)
  {
    return $default;
  }
  
  public static function set()
  {
    
  }
  
  public static function add()
  {
    
  }
  
}

class sfLoader
{
  public static function loadHelpers($helpers)
  {
    global $sf_symfony_lib_dir;
    
    foreach ($helpers as $helper)
    {
      require_once $sf_symfony_lib_dir.'/helper/'.$helper.'Helper.php';
    }
  }
}

$t = new lime_test(6, new lime_output_color);

$tracker = new sfGoogleAnalyticsTrackerUrchin(new sfContext);

$t->diag('page view');
$t->is($tracker->forgePageViewFunction(), 'urchinTracker();', 'null page view ok');
$t->is($tracker->forgePageViewFunction('/testing/123'), 'urchinTracker("\\/testing\\/123");', 'string page view ok');
$t->is($tracker->forgePageViewFunction('@homepage', 'is_route=on'), 'urchinTracker("\\/");', 'route page view ok');

$t->diag('linker');
$t->is($tracker->forgeLinkerFunction('http://example.com/link/to/me'), '__utmLinker("http:\\/\\/example.com\\/link\\/to\\/me");', 'linker ok');
$t->is($tracker->forgePostLinkerFunction(), '__utmLinkPost(this);', 'post linker ok');
$t->is($tracker->forgePostLinkerFunction('document.getElementById("my_form")'), '__utmLinkPost(document.getElementById("my_form"));', 'custom form linker ok');
