<?php

/**
 * Utility methods for the sfGoogleAnalyticsPlugin.
 * 
 * @package     sfGoogleAnalyticsPlugin
 * @subpackage  util
 * @author      Kris Wallsmith <kris [dot] wallsmith [at] gmail [dot] com>
 * @version     SVN: $Id$
 */
class sfGoogleAnalyticsToolkit
{
  /**
   * Get HTML for insertion at the bottom of a document.
   * 
   * @author  Kris Wallsmith
   * @throws  sfGoogleAnalyticsException
   * @return  string
   */
  public static function getHtml()
  {
    sfLoader::loadHelpers(array('Escaping', 'Asset'));
    
    $context = sfContext::getInstance();
    $request = $context->getRequest();
    $module  = $context->getModuleName();
    $action  = $context->getActionName();
    
    $actionConfig = sfConfig::get('mod_'.$module.'_'.$action.'_google_analytics', array());
    
    $usrc = $request->isSecure() ? 
      sfConfig::get('app_google_analytics_usrc_ssl', 'https://ssl.google-analytics.com/urchin.js') : 
      sfConfig::get('app_google_analytics_usrc', 'http://www.google-analytics.com/urchin.js');
    
    // initial parameter
    $utParam = '';
    if (isset($actionConfig['ut_param']))
    {
      $utParam = $actionConfig['ut_param'];
    }
    else
    {
      $utParam = sfConfig::get('app_google_analytics_ut_param');
      $utParam = sfConfig::get('mod_'.$module.'_google_analytics_ut_param', $utParam);
    }
    
    // initialization variables
    $vars = sfConfig::get('app_google_analytics_vars', array());
    $vars = array_merge($vars, sfConfig::get('mod_'.$module.'_google_analytics_vars', array()));
    if (isset($actionConfig['vars']) && is_array($actionConfig['vars']))
    {
      $vars = array_merge($vars, $actionConfig['vars']);
    }
    
    // account number is required
    if (!isset($vars['uacct']) && !isset($vars['_uacct']))
    {
      // backwards compatibility
      $vars['uacct'] = sfConfig::get('app_google_analytics_uacct');
      if (!$vars['uacct'])
      {
        throw new sfGoogleAnalyticsException('Please add your Google Analytics account number to your app.yml.');
      }
    }
    
    // prep the initial parameter
    if ($utParam)
    {
      $utParam = sprintf('"%s"', esc_js_no_entities($utParam));
      $utParam = str_replace('\\/', '/', $utParam);
    }
    
    // build initialization variables
    $jsVars = array();
    foreach ($vars as $key => $value)
    {
      if ($key{0} != '_')
      {
        $key = '_'.$key;
      }
      $jsVars[] = sprintf("%s=\"%s\";", $key, esc_js_no_entities($value));
    }
    $jsVars = join("\n", $jsVars);
    
    // custom variables
    $custom = sfConfig::get('app_google_analytics_custom', array());
    $custom = array_merge($custom, sfConfig::get('mod_'.$module.'_google_analytics_custom', array()));
    if (isset($actionConfig['custom']) && is_array($actionConfig['custom']))
    {
      $custom = array_merge($custom, $actionConfig['custom']);
    }
    
    $jsCustom = array();
    foreach ($custom as $value)
    {
      $jsCustom[] = sprintf('__utmSetVar("%s");', esc_js_no_entities($value));
    }
    $jsCustom = join("\n", $jsCustom);
    
    $html  = javascript_include_tag($usrc);
    $html .= javascript_tag(sprintf("%s\nurchinTracker(%s);\n%s", $jsVars, $utParam, $jsCustom));
    
    return $html;
  }
  
  /**
   * Add an initialization variable to Google Analytics.
   * 
   * @author  Kris Wallsmith
   * @param   string $name
   * @param   string $value
   */
  public static function addVar($name, $value)
  {
    $config = self::getActionConfig();
    if (!isset($config['vars']))
    {
      $config['vars'] = array();
    }
    $config['vars'][$name] = $value;
    
    self::setActionConfig($config);
  }
  
  /**
   * Set a custom parameter for Google Analytics initialization.
   * 
   * @author  Kris Wallsmith
   * @param   string $utParam
   */
  public static function setParam($utParam)
  {
    $config = self::getActionConfig();
    $config['ut_param'] = $utParam;
    
    self::setActionConfig($config);
  }
  
  /**
   * Add a custom variable to Google Analytics.
   * 
   * @author  Kris Wallsmith
   * @param   string $var
   */
  public static function addCustomVar($var)
  {
    $config = self::getActionConfig();
    if (!isset($config['custom']))
    {
      $config['custom'] = array();
    }
    $config['custom'][] = $var;
    
    self::setActionConfig($config);
  }
  
  /**
   * Add multiple custom variables to Google Analytics.
   * 
   * @author  Kris Wallsmith
   * @param   array $vars
   */
  public static function addCustomVars($vars)
  {
    foreach ($vars as $var)
    {
      self::addCustomVar($var);
    }
  }
  
  //------------------------------------------------------------------------//
  // INTERNAL UTILITIES
  //------------------------------------------------------------------------//
  
  /**
   * Get the Google Analytics configuration for the current action.
   * 
   * @author  Kris Wallsmith
   * @return  array
   */
  protected static function getActionConfig()
  {
    return sfConfig::get(self::getActionConfigKey(), array());
  }
  
  /**
   * Set the Google Analytics configuration for the current action.
   * 
   * @author  Kris Wallsmith
   * @param   array $config
   */
  protected static function setActionConfig($config)
  {
    sfConfig::set(self::getActionConfigKey(), $config);
  }
  
  /**
   * Get the string used for the current action's Google Analytics config.
   * 
   * @author  Kris Wallsmith
   * @return  string
   */
  protected static function getActionConfigKey()
  {
    $context = sfContext::getInstance();
    $module = $context->getModuleName();
    $action = $context->getActionName();
    
    return 'mod_'.$module.'_'.$action.'_google_analytics';
  }
  
}
