<?php

$app = 'frontend';
require dirname(__FILE__).'/../bootstrap/functional.php';

$context = sfContext::getInstance();
$request = $context->getRequest();
$request->setRelativeUrlRoot('');
sfConfig::set('sf_no_script_name', true);
sfLoader::loadHelpers(array('GoogleAnalytics'));

$t = new lime_test(19, new lime_output_color);

$urchin = new sfGoogleAnalyticsTrackerUrchin($context);
$google = new sfGoogleAnalyticsTrackerGoogle($context);

$t->diag('urchin: link_to');

$request->setTracker($urchin);
$t->is(google_analytics_link_to('my page', '/my/page', 'is_route=on'), '<a href="/my/page">my page</a>', 'disabled ok');
$t->is(google_analytics_link_to('home', '@homepage', 'is_route=on'), '<a href="/">home</a>', 'disabled route ok');

$urchin->setEnabled(true);
$t->is(google_analytics_link_to('my page', '/my/page'), '<a onclick="urchinTracker(&quot;\\/my\\/page&quot;);" href="/my/page">my page</a>', 'enabled ok');
$t->is(google_analytics_link_to('home', '@homepage', 'is_route=on'), '<a onclick="urchinTracker(&quot;\\/&quot;);" href="/">home</a>', 'enabled route ok');

$t->diag('urchin: link_to_function');

$urchin->setEnabled(false);
try
{
  google_analytics_link_to_function('foo', 'alert("bar")');
  $t->fail('track_as exception ok');
}
catch (sfViewException $e)
{
  $t->pass('track_as exception ok');
}
catch (Exception $e)
{
  $t->fail('track_as exception ok');
}
$t->is(google_analytics_link_to_function('foo', 'alert("bar")', 'track_as=/baz'), '<a href="#" onclick="alert(&quot;bar&quot;); return false;">foo</a>', 'disabled ok');
$t->is(google_analytics_link_to_function('foo', 'alert("bar")', 'track_as=/baz is_event=on'), '<a href="#" onclick="alert(&quot;bar&quot;); return false;">foo</a>', 'disabled event ok');

$urchin->setEnabled(true);
$t->is(google_analytics_link_to_function('foo', 'alert("bar")', 'track_as=/baz'), '<a href="#" onclick="urchinTracker(&quot;\\/baz&quot;); alert(&quot;bar&quot;); return false;">foo</a>', 'enabled ok');
$t->is(google_analytics_link_to_function('foo', 'alert("bar")', 'track_as=/baz is_event=on'), '<a href="#" onclick="urchinTracker(&quot;\\/baz&quot;); alert(&quot;bar&quot;); return false;">foo</a>', 'enabled event ok');

$t->diag('urchin: link_to_remote');

$urchin->setEnabled(false);
$t->is(google_analytics_link_to_remote('foo', 'url=@homepage track_as=/bar'), '<a href="#" onclick="new Ajax.Request(\'/\', {asynchronous:true, evalScripts:false}); return false;">foo</a>', 'disabled ok');
$t->is(google_analytics_link_to_remote('foo', 'url=@homepage track_as=@homepage is_route=on'), '<a href="#" onclick="new Ajax.Request(\'/\', {asynchronous:true, evalScripts:false}); return false;">foo</a>', 'disabled event ok');

$urchin->setEnabled(true);
$t->is(google_analytics_link_to_remote('foo', 'url=@homepage track_as=@homepage is_route=on'), '<a href="#" onclick="urchinTracker(&quot;\\/&quot;); new Ajax.Request(\'/\', {asynchronous:true, evalScripts:false}); return false;">foo</a>', 'enabled ok');
$t->is(google_analytics_link_to_remote('foo', 'url=@homepage track_as=/bar'), '<a href="#" onclick="urchinTracker(&quot;\\/bar&quot;); new Ajax.Request(\'/\', {asynchronous:true, evalScripts:false}); return false;">foo</a>', 'enabled event ok');

$t->diag('urchin: linker');

$urchin->setEnabled(false);
$t->ok(is_null(google_analytics_linker_function('http://www.google.com')), 'disabled ok');


$urchin->setEnabled(true);
$urchin->setDomainName(false);
$urchin->setLinkerPolicy(true);
$t->is(google_analytics_linker_function('http://www.google.com'), '__utmLinker("http:\\/\\/www.google.com");', 'enabled ok');

$t->diag('urchin: post linker');

$urchin->setEnabled(false);
$t->ok(is_null(google_analytics_post_linker_function()), 'disabled ok');
$t->ok(is_null(google_analytics_post_linker_function('document.getElementById("myform")')), 'custom form disabled ok');

$urchin->setEnabled(true);
$t->is(google_analytics_post_linker_function(), '__utmLinkPost(this);', 'enabled ok');
$t->is(google_analytics_post_linker_function('document.getElementById("myform")'), '__utmLinkPost(document.getElementById("myform"));', 'custom form enabled ok');
