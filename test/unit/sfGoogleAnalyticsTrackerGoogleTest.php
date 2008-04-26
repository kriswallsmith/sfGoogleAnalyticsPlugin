<?php

require dirname(__FILE__).'/../bootstrap/unit.php';

require $ga_lib_dir.'/tracker/sfGoogleAnalyticsTracker.class.php';
require $ga_lib_dir.'/tracker/sfGoogleAnalyticsTrackerGoogle.class.php';

require $sf_symfony_lib_dir.'/util/sfParameterHolder.class.php';
require $sf_symfony_lib_dir.'/util/sfToolkit.class.php';

$t = new lime_test(10, new lime_output_color);

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

$t->diag('tracker name');
$tracker = new sfGoogleAnalyticsTrackerGoogle(new sfContext);

$t->is($tracker->getTrackerVar(), 'pageTracker', 'default tracker var name ok');
$tracker->setTrackerVar('myTracker');
$t->is($tracker->getTrackerVar(), 'myTracker', 'tracker var name ok');

$tracker->configure(array('tracker_var' => 'ga'));
$t->is($tracker->getTrackerVar(), 'ga', 'by configure ok');

$t->diag('page view');
$t->is($tracker->forgePageViewFunction('/my/page'), 'ga._trackPageview("\\/my\\/page");', 'page view ok');
$t->is($tracker->forgePageViewFunction('@homepage', 'is_route=on'), 'ga._trackPageview("\\/");', 'route page view ok');
$t->is($tracker->forgePageViewFunction('/my/event', 'is_event=on'), 'ga._trackEvent("\\/my\\/event");', 'event ok');
$t->is($tracker->forgePageViewFunction('@homepage', 'is_event=on is_route=on'), 'ga._trackEvent("\\/");', 'route event ok');

$t->diag('linker');
$t->is($tracker->forgeLinkerFunction('http://example.com/foo.html'), 'ga._link("http:\\/\\/example.com\\/foo.html");', 'linker ok');
$t->is($tracker->forgePostLinkerFunction(), 'ga._linkByPost(this);', 'post linker ok');
$t->is($tracker->forgePostLinkerFunction('document.getElementById("aform")'), 'ga._linkByPost(document.getElementById("aform"));', 'custom form post linker ok');
