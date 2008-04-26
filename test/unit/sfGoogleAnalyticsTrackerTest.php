<?php

require dirname(__FILE__).'/../bootstrap/unit.php';

require $ga_lib_dir.'/tracker/sfGoogleAnalyticsTracker.class.php';
require $ga_lib_dir.'/transaction/sfGoogleAnalyticsTransaction.class.php';

require $sf_symfony_lib_dir.'/util/sfParameterHolder.class.php';
require $sf_symfony_lib_dir.'/util/sfInflector.class.php';
require $sf_symfony_lib_dir.'/util/sfToolkit.class.php';

$t = new lime_test(99, new lime_output_color);

class sfContext
{
  public function getController()
  {
    return new sfController;
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

class sfUser
{
  protected $attributeHolder = null;
  
  public function getAttributeHolder()
  {
    if (is_null($this->attributeHolder))
    {
      $this->attributeHolder = new sfParameterHolder;
    }
    
    return $this->attributeHolder;
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

class sfGoogleAnalyticsTrackerTest extends sfGoogleAnalyticsTracker
{
  public function forgePageViewFunction($path = null, $options = array())
  {
  }
  
  public function forgeLinkerFunction($url)
  {
  }
  
  public function forgePostLinkerFunction($formElement = 'this')
  {
  }
  
  public function insert(sfResponse $response)
  {
  }
}

$t->diag('sfGoogleAnalyticsTracker');

$tracker = new sfGoogleAnalyticsTrackerTest(new sfContext);

$t->isa_ok($tracker, 'sfGoogleAnalyticsTrackerTest', 'tracker instantiated ok');
$t->isa_ok($tracker->getContext(), 'sfContext', 'context stored ok');
$t->ok($tracker->getParameterHolder() instanceof sfParameterHolder, 'parameter holder stored ok');

$t->diag('setters/getters');

// a test configuration
$params = array(
  'enabled'                     => true,
  'insertion'                   => sfGoogleAnalyticsTrackerTest::POSITION_BOTTOM,
  'profile_id'                  => 'XX-XXXXX-X',
  'page_name'                   => '/my/page.name',
  'domain_name'                 => 'example.com',
  'linker_policy'               => true,
  'organic_referers'            => array(array('mysearch.com', 'q'), array('anothersearch.com', 'kw')),
  'vars'                        => array('tool', 'girl'),
  'cookie_path'                 => '/my/cookie/path',
  'client_info_policy'          => false,
  'hash_policy'                 => false,
  'detect_flash_policy'         => false,
  'detect_title_policy'         => false,
  'session_timeout'             => 1200,
  'cookie_timeout'              => 12000,
  'campaign_keys'               => array(
    'name'        => 'cnk', 
    'source'      => 'csk', 
    'medium'      => 'cmk', 
    'term'        => 'ctt', 
    'content'     => 'cck', 
    'id'          => 'cik', 
    'no_override' => 'cnok',
  ),
  'anchor_policy'               => true,
  'ignored_organics'            => array('ignored_kw', 'ignored_kw2'),
  'ignored_referers'            => array('ignored_ref', 'ignored_ref2'),
  'sample_rate'                 => 50,
  'local_remote_server_policy'  => true,
);

$t->ok(is_null($tracker->getBeforeTrackerJS()), 'before js null');
$tracker->setBeforeTrackerJS('alert("before");');
$t->is($tracker->getBeforeTrackerJS(), 'alert("before");', 'before js ok');

$t->ok(is_null($tracker->getAfterTrackerJS()), 'after js null');
$tracker->setAfterTrackerJS('alert("after");');
$t->is($tracker->getAfterTrackerJS(), 'alert("after");', 'after js ok');

$t->ok($tracker->isEnabled() === false, 'enabled false');
$tracker->setEnabled($params['enabled']);
$t->ok($tracker->isEnabled() === $params['enabled'], 'enabled ok');

$t->ok(is_null($tracker->getInsertion()), 'insertion null');
$tracker->setInsertion($params['insertion']);
$t->is($tracker->getInsertion(), $params['insertion'], 'insertion ok');

$t->ok(is_null($tracker->getProfileId()), 'profile id null');
$tracker->setProfileId($params['profile_id']);
$t->is($tracker->getProfileId(), $params['profile_id'], 'profile id ok');

$t->ok(is_null($tracker->getPageName()), 'page name null');
$tracker->setPageName($params['page_name']);
$t->is($tracker->getPageName(), $params['page_name'], 'page name ok');

$t->ok(is_null($tracker->getDomainName()), 'domain name null');
$tracker->setDomainName($params['domain_name']);
$t->is($tracker->getDomainName(), $params['domain_name'], 'domain name ok');

$t->ok($tracker->getLinkerPolicy() === false, 'linker policy false');
$tracker->setLinkerPolicy($params['linker_policy']);
$t->ok($tracker->getLinkerPolicy() === $params['linker_policy'], 'linker policy ok');

$t->is_deeply($tracker->getOrganicReferers(), array(), 'organic referers empty');
foreach ($params['organic_referers'] as $referer)
{
  $tracker->addOrganicReferer($referer[0], $referer[1]);
}
$t->is_deeply($tracker->getOrganicReferers(), $params['organic_referers'], 'organic referers ok');

$t->is_deeply($tracker->getVars(), array(), 'vars empty');
foreach ($params['vars'] as $var)
{
  $tracker->setVar($var);
}
$t->is_deeply($tracker->getVars(), $params['vars'], 'vars ok');

$t->ok(is_null($tracker->getCookiePath()), 'cookie path null');
$tracker->setCookiePath($params['cookie_path']);
$t->is($tracker->getCookiePath(), $params['cookie_path'], 'cookie path ok');

$t->ok($tracker->getClientInfoPolicy() === true, 'client info policy true');
$tracker->setClientInfoPolicy($params['client_info_policy']);
$t->ok($tracker->getClientInfoPolicy() === $params['client_info_policy'], 'client info policy ok');

$t->ok($tracker->getHashPolicy(), 'hash policy true');
$tracker->setHashPolicy($params['hash_policy']);
$t->ok($tracker->getHashPolicy() === $params['hash_policy'], 'hash policy ok');

$t->ok($tracker->getDetectFlashPolicy(), 'flash detect policy true');
$tracker->setDetectFlashPolicy($params['detect_flash_policy']);
$t->ok($tracker->getDetectFlashPolicy() === $params['detect_flash_policy'], 'flash detect policy ok');

$t->ok($tracker->getDetectTitlePolicy(), 'title detect policy true');
$tracker->setDetectTitlePolicy($params['detect_title_policy']);
$t->ok($tracker->getDetectTitlePolicy() === $params['detect_title_policy'], 'title detect policy ok');

$t->ok(is_null($tracker->getSessionTimeout()), 'session timeout null');
$tracker->setSessionTimeout($params['session_timeout']);
$t->is($tracker->getSessionTimeout(), $params['session_timeout'], 'session timeout ok');

$t->ok(is_null($tracker->getCookieTimeout()), 'cookie timeout null');
$tracker->setCookieTimeout($params['cookie_timeout']);
$t->is($tracker->getCookieTimeout(), $params['cookie_timeout'], 'cookie timeout ok');

$t->ok(is_null($tracker->getCampaignNameKey()), 'cmpgn name key null');
$tracker->setCampaignNameKey($params['campaign_keys']['name']);
$t->is($tracker->getCampaignNameKey(), $params['campaign_keys']['name'], 'cmpgn name key ok');

$t->ok(is_null($tracker->getCampaignSourceKey()), 'cmpgn source key null');
$tracker->setCampaignSourceKey($params['campaign_keys']['source']);
$t->is($tracker->getCampaignSourceKey(), $params['campaign_keys']['source'], 'cmpgn source key ok');

$t->ok(is_null($tracker->getCampaignMediumKey()), 'cmpgn medium key null');
$tracker->setCampaignMediumKey($params['campaign_keys']['medium']);
$t->is($tracker->getCampaignMediumKey(), $params['campaign_keys']['medium'], 'cmpgn medium key ok');

$t->ok(is_null($tracker->getCampaignTermKey()), 'cmpgn term key null');
$tracker->setCampaignTermKey($params['campaign_keys']['term']);
$t->is($tracker->getCampaignTermKey(), $params['campaign_keys']['term'], 'cmpgn term key ok');

$t->ok(is_null($tracker->getCampaignContentKey()), 'cmpgn content key null');
$tracker->setCampaignContentKey($params['campaign_keys']['content']);
$t->is($tracker->getCampaignContentKey(), $params['campaign_keys']['content'], 'cmpgn content key ok');

$t->ok(is_null($tracker->getCampaignIdKey()), 'cmpgn id key null');
$tracker->setCampaignIdKey($params['campaign_keys']['id']);
$t->is($tracker->getCampaignIdKey(), $params['campaign_keys']['id'], 'cmpgn id key ok');

$t->ok(is_null($tracker->getCampaignNoOverrideKey()), 'cmpgn no override key null');
$tracker->setCampaignNoOverrideKey($params['campaign_keys']['no_override']);
$t->is($tracker->getCampaignNoOverrideKey(), $params['campaign_keys']['no_override'], 'cmpgn no override key ok');

$t->ok($tracker->getAnchorPolicy() === false, 'anchor policy false');
$tracker->setAnchorPolicy($params['anchor_policy']);
$t->ok($tracker->getAnchorPolicy() === $params['anchor_policy'], 'anchor policy ok');

$t->is_deeply($tracker->getIgnoredOrganics(), array(), 'ignored organics empty');
foreach ($params['ignored_organics'] as $ignore)
{
  $tracker->addIgnoredOrganic($ignore);
}
$t->is_deeply($tracker->getIgnoredOrganics(), $params['ignored_organics'], 'ignored organics ok');

$t->is_deeply($tracker->getIgnoredReferers(), array(), 'ignored referers empty');
foreach ($params['ignored_referers'] as $ignore)
{
  $tracker->addIgnoredReferer($ignore);
}
$t->is_deeply($tracker->getIgnoredReferers(), $params['ignored_referers'], 'ignored referers ok');

$t->ok(is_null($tracker->getSampleRate()), 'sample rate null');
$tracker->setSampleRate($params['sample_rate']);
$t->is($tracker->getSampleRate(), $params['sample_rate'], 'sample rate ok');

$t->ok($tracker->getLocalRemoteServerPolicy() === false, 'local/remote policy false');
$tracker->setLocalRemoteServerPolicy($params['local_remote_server_policy']);
$t->ok($tracker->getLocalRemoteServerPolicy() === $params['local_remote_server_policy'], 'local/remote policy policy ok');

$t->diag('configure');

$tracker = new sfGoogleAnalyticsTrackerTest(new sfContext);
$tracker->configure($params);

$t->ok($tracker->isEnabled() === $params['enabled'], 'enabled ok');
$t->is($tracker->getInsertion(), $params['insertion'], 'insertion ok');
$t->is($tracker->getProfileId(), $params['profile_id'], 'profile id ok');
$t->is($tracker->getPageName(), $params['page_name'], 'page name ok');
$t->is($tracker->getDomainName(), $params['domain_name'], 'domain name ok');
$t->ok($tracker->getLinkerPolicy() === $params['linker_policy'], 'linker policy ok');
$t->is_deeply($tracker->getOrganicReferers(), $params['organic_referers'], 'organic referers ok');
$t->is_deeply($tracker->getVars(), $params['vars'], 'vars ok');
$t->is($tracker->getCookiePath(), $params['cookie_path'], 'cookie path ok');
$t->ok($tracker->getClientInfoPolicy() === $params['client_info_policy'], 'client info policy ok');
$t->ok($tracker->getHashPolicy() === $params['hash_policy'], 'hash policy ok');
$t->ok($tracker->getDetectFlashPolicy() === $params['detect_flash_policy'], 'flash detect policy ok');
$t->ok($tracker->getDetectTitlePolicy() === $params['detect_title_policy'], 'title detect policy ok');
$t->is($tracker->getSessionTimeout(), $params['session_timeout'], 'session timeout ok');
$t->is($tracker->getCookieTimeout(), $params['cookie_timeout'], 'cookie timeout ok');
$t->is($tracker->getCampaignNameKey(), $params['campaign_keys']['name'], 'cmpgn name key ok');
$t->is($tracker->getCampaignSourceKey(), $params['campaign_keys']['source'], 'cmpgn source key ok');
$t->is($tracker->getCampaignMediumKey(), $params['campaign_keys']['medium'], 'cmpgn medium key ok');
$t->is($tracker->getCampaignTermKey(), $params['campaign_keys']['term'], 'cmpgn term key ok');
$t->is($tracker->getCampaignContentKey(), $params['campaign_keys']['content'], 'cmpgn content key ok');
$t->is($tracker->getCampaignIdKey(), $params['campaign_keys']['id'], 'cmpgn id key ok');
$t->is($tracker->getCampaignNoOverrideKey(), $params['campaign_keys']['no_override'], 'cmpgn no override key ok');
$t->ok($tracker->getAnchorPolicy() === $params['anchor_policy'], 'anchor policy ok');
$t->is_deeply($tracker->getIgnoredOrganics(), $params['ignored_organics'], 'ignored organics ok');
$t->is_deeply($tracker->getIgnoredReferers(), $params['ignored_referers'], 'ignored referers ok');
$t->is($tracker->getSampleRate(), $params['sample_rate'], 'sample rate ok');
$t->ok($tracker->getLocalRemoteServerPolicy() === $params['local_remote_server_policy'], 'local/remote policy policy ok');

$t->diag('shutdown');

$tracker = new sfGoogleAnalyticsTrackerTest(new sfContext);

$user = new sfUser;
$t->isa_ok($user, 'sfUser', 'fake user class instantiated');

$tracker->shutdown($user);
$t->is_deeply($user->getAttributeHolder()->get('callables', null, 'sf_google_analytics_plugin'), array(), 'shutdown empty');

$tracker->setBeforeTrackerJS('alert("before");', 'use_flash=on');
$tracker->setAfterTrackerJS('alert("after");', 'use_flash=on');
$tracker->setInsertion(sfGoogleAnalyticsTrackerTest::POSITION_BOTTOM, 'use_flash=on');
$tracker->setPageName('/delayed/page.name', 'use_flash=on');
$tracker->setVar('boy', 'use_flash=on');

$t->ok(is_null($tracker->getBeforeTrackerJS()), 'before js null');
$t->ok(is_null($tracker->getAfterTrackerJS()), 'after js null');
$t->ok(is_null($tracker->getInsertion()), 'flash insertion null');
$t->ok(is_null($tracker->getPageName()), 'flash page name null');
$t->is_deeply($tracker->getVars(), array(), 'flash vars empty');

$tracker->shutdown($user);

$calls = $user->getAttributeHolder()->get('callables', array(), 'sf_google_analytics_plugin');
$t->is_deeply($calls, array(
  array(
    'setBeforeTrackerJS',
    array('alert("before");', array()),
  ), array(
    'setAfterTrackerJS',
    array('alert("after");', array()),
  ), array(
    'setInsertion', 
    array(sfGoogleAnalyticsTrackerTest::POSITION_BOTTOM, array()),
  ), array(
    'setPageName', 
    array('/delayed/page.name', array()),
  ), array(
    'setVar', 
    array('boy', array())),
  ), 'user callables attribute ok');

// mutator options
$t->diag('mutator options');

$tracker = new sfGoogleAnalyticsTrackerTest(new sfContext);
$tracker->setPageName('@homepage', 'is_route=on');
$t->is($tracker->getPageName(), '/', 'routing option ok');

// view options
$t->diag('view options');

$options = array(
  'my_other_option' => 'xyz',
  'track_as'        => '@homepage',
  'is_route'        => true,
  'is_event'        => true,
  'use_linker'      => true,
);
$t->is_deeply($tracker->extractViewOptions($options), array(
  'track_as'        => '@homepage',
  'is_route'        => true,
  'is_event'        => true,
  'use_linker'      => true,
), 'view options ok');
$t->is_deeply($options, array('my_other_option' => 'xyz'), 'other options ok');
