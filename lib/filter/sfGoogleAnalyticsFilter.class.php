<?php

/**
 * Add tracking code to the response.
 * 
 * @package     sfGoogleAnalyticsPlugin
 * @subpackage  filter
 * @author      Kris Wallsmith <kris [dot] wallsmith [at] gmail [dot] com>
 * @version     SVN: $Id$
 */
class sfGoogleAnalyticsFilter extends sfFilter
{
  /**
   * Insert tracking code for applicable web requests.
   * 
   * @param   sfFilterChain $filterChain
   */
  public function execute($filterChain)
  {
    $prefix   = 'app_sf_google_analytics_plugin_';
    $user     = $this->context->getUser();
    $request  = $this->context->getRequest();
    $response = $this->context->getResponse();
    
    if ($this->isFirstCall())
    {
      $classes = array_merge(array(
        'urchin' => 'sfGoogleAnalyticsTrackerUrchin',
        'google' => 'sfGoogleAnalyticsTrackerGoogle'), sfConfig::get($prefix.'classes', array()));
      $class = $classes[sfConfig::get($prefix.'tracker', 'urchin')];
      
      $tracker = new $class($this->context);
      
      // pull callables from session storage
      $callables = $user->getAttribute('callables', array(), 'sf_google_analytics_plugin');
      foreach ($callables as $callable)
      {
        list($method, $arguments) = $callable;
        call_user_func_array(array($tracker, $method), $arguments);
      }
      
      $request->setTracker($tracker);
    }
    
    $filterChain->execute();
    $tracker = $request->getTracker();
    
    // apply module- and action-level configuration
    $module = $this->context->getModuleName();
    $action = $this->context->getActionName();
    
    $moduleParams = sfConfig::get('mod_'.$module.'_sf_google_analytics_plugin_params', array());
    $tracker->configure($moduleParams);
    
    $actionConfig = sfConfig::get('mod_'.$module.'_'.$action.'_sf_google_analytics_plugin', array());
    if (isset($actionConfig['params']))
    {
      $tracker->configure($actionConfig['params']);
    }
    
    // insert tracking code
    if ($this->isTrackable() && $tracker->isEnabled())
    {
      if (sfConfig::get('sf_logging_enabled'))
      {
        $this->log('Inserting tracking code.');
      }
      
      $tracker->insert($response);
    }
    elseif (sfConfig::get('sf_logging_enabled'))
    {
      $this->log('Tracking code not inserted.');
    }
    
    $user->getAttributeHolder()->removeNamespace('sf_google_analytics_plugin');
    $tracker->shutdown($user);
  }
  
  /**
   * Test whether the response is trackable.
   * 
   * @return  bool
   */
  protected function isTrackable()
  {
    $request    = $this->context->getRequest();
    $response   = $this->context->getResponse();
    $controller = $this->context->getController();
    
    // don't add analytics:
    // * for XHR requests
    // * if not HTML
    // * if 304
    // * if not rendering to the client
    // * if HTTP headers only
    if ($request->isXmlHttpRequest() ||
        strpos($response->getContentType(), 'html') === false ||
        $response->getStatusCode() == 304 ||
        $controller->getRenderMode() != sfView::RENDER_CLIENT ||
        $response->isHeaderOnly())
    {
      return false;
    }
    else
    {
      return true;
    }
  }
  
  /**
   * Log a message.
   * 
   * @param   string $message
   */
  protected function log($message)
  {
    if (defined('SYMFONY_VERSION') && strpos(SYMFONY_VERSION, '1.1') === 0)
    {
      $this->context->getEventDispatcher()->notify(new sfEvent(null, 'application.log', array($message)));
    }
    else
    {
      $this->getContext()->getLogger()->info(sprintf('{%s} %s', __CLASS__, $message));
    }
  }
  
}
