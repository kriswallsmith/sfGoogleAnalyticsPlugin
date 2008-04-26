<?php

include dirname(__FILE__).'/../../bootstrap/functional.php';

// create a new test browser
$browser = new sfTestBrowser;
$browser->initialize();
$t = $browser->test();

$prefix = 'app_sf_google_analytics_plugin_';

$urchinVars = explode(',', 'dn,link,Osr,Okw,tcp,fsc,hash,flash,title,timeout,cto,ccn,cmd,csr,ctr,cct,cid,cno,anchor,Ono,Rno,sample,serv');
$googleSetters = explode(',', 'DomainName,AllowLinker,CookiePath,ClientInfo,AllowHash,DetectFlash,DetectTitle,SessionTimeout,CookieTimeout,CampNameKey,CampMediumKey,CampSourceKey,CampTermKey,CampContentKey,CampIdKey,CampNOKey,AllowAnchor,SampleRate,LocalRemoteServerMode');
$googleAdders = explode(',', 'Organic,IgnoredOrganic,IgnoredRef');

$params = array(
  'page_name'                   => '/virtual/page',
  'domain_name'                 => 'example.com',
  'linker_policy'               => true,
  'organic_referers'            => array(array('name' => 'example.com', 'param' => 'q')),
  'vars'                        => array('customer'),
  'cookie_path'                 => '/subdir',
  'client_info_policy'          => false,
  'hash_policy'                 => false,
  'detect_flash_policy'         => false,
  'detect_title_policy'         => false,
  'session_timeout'             => 60,
  'cookie_timeout'              => 600,
  'campaign_keys'               => array(
    'name'        => 'cnk',
    'medium'      => 'cmk',
    'source'      => 'csk',
    'term'        => 'ctk',
    'content'     => 'cck',
    'id'          => 'cik',
    'no_override' => 'cnok',
  ),
  'anchor_policy'               => true,
  'ignored_organics'            => array('keyword'),
  'ignored_referers'            => array('example.com'),
  'sample_rate'                 => 10,
  'local_remote_server_policy'  => true,
);

$browser->getAndCheck('main', 'index');
$t->unlike($browser->getResponse()->getContent(), '/google-analytics\.com/', 'disabled urchin ok');

sfConfig::set($prefix.'enabled', true);
$browser->
  getAndCheck('main', 'index')->
  responseContains('google-analytics.com')->
  responseContains('urchinTracker()');

$content = $browser->getResponse()->getContent();
$t->unlike($content, '/_u('.join('|', $urchinVars).')/', 'no params ok');
$t->unlike($content, '/__utmSetVar/', 'no vars ok');

sfConfig::set($prefix.'params', $params);
$browser->
  getAndCheck('main', 'index')->
  responseContains('google-analytics.com')->
  responseContains('urchinTracker("\/virtual\/page")');

$content = $browser->getResponse()->getContent();
foreach ($urchinVars as $var)
{
  $t->like($content, '/^_u'.$var.'(\[\d*\])?=[^;]+;$/m', 'u'.$var.' ok');
}

sfConfig::set($prefix.'tracker', 'google');
sfConfig::set($prefix.'params', array());
$browser->
  getAndCheck('main', 'index')->
  responseContains('google-analytics.com')->
  responseContains('pageTracker');

$content = $browser->getResponse()->getContent();
$t->unlike($content, '/set('.join('|', $googleSetters).')/', 'no setters ok');
$t->unlike($content, '/add('.join('|', $googleAdders).')/', 'no adders ok');

sfConfig::set($prefix.'params', $params);
$browser->
  getAndCheck('main', 'index')->
  responseContains('google-analytics.com')->
  responseContains('pageTracker');

$content = $browser->getResponse()->getContent();
foreach ($googleSetters as $setter)
{
  $t->like($content, '/set'.$setter.'\(/', 'set'.$setter.' ok');
}
foreach ($googleAdders as $adder)
{
  $t->like($content, '/add'.$adder.'\(/', 'add'.$adder.' ok');
}

sfConfig::set('mod_main_sf_google_analytics_plugin_params', array(
  'page_name' => '/module/page',
  'vars'      => array('module_var'),
));
$browser->
  getAndCheck('main', 'index')->
  responseContains('google-analytics.com')->
  responseContains('pageTracker')->
  responseContains('\/module\/page')->
  responseContains('module_var');
