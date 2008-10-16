<?php

if (defined('SYMFONY_VERSION') && 0 !== strpos(SYMFONY_VERSION, '1.0'))
{
  // >= symfony 1.1
  $listener = array('sfGoogleAnalyticsListener', 'observe');
  
  $this->dispatcher->connect('request.method_not_found', $listener);
  $this->dispatcher->connect('response.method_not_found', $listener);
  $this->dispatcher->connect('component.method_not_found', $listener);
  $this->dispatcher->connect('user.method_not_found', $listener);
}
else
{
  // symfony 1.0
  $getter = array('sfGoogleAnalyticsMixin', 'getTracker');
  $setter = array('sfGoogleAnalyticsMixin', 'setTracker');
  
  sfMixer::register('sfRequest', $getter);
  sfMixer::register('sfRequest', $setter);
  
  sfMixer::register('sfResponse', $getter);
  sfMixer::register('sfResponse', $setter);
  
  sfMixer::register('sfComponent', $getter);
  sfMixer::register('sfComponent', $setter);
  
  sfMixer::register('sfUser', $getter);
  sfMixer::register('sfUser', $setter);
}
