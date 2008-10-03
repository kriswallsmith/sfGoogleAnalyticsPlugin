<?php

/**
 * Utility methods for the sfGoogleAnalyticsPlugin.
 * 
 * @package     sfGoogleAnalyticsPlugin
 * @subpackage  util
 * @author      Kris Wallsmith <kris.wallsmith@symfony-project.com>
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
    sfLoader::loadHelpers(array('Partial'));
    
    $usrc = sfContext::getInstance()->getRequest()->isSecure() ? 
      sfConfig::get('app_google_analytics_usrc_ssl', 'https://ssl.google-analytics.com/urchin.js') : 
      sfConfig::get('app_google_analytics_usrc', 'http://www.google-analytics.com/urchin.js');
    
    // capture and prepare all javascript variable to be declared before the
    // initial call to urchinTracker() - confirm we have a value for _uacct.
    $varsConfig = self::getFinalConfigValue('vars', true);
    if (!isset($varsConfig['uacct']) && !isset($varsConfig['_uacct']))
    {
      // backwards compatibility
      $varsConfig['uacct'] = sfConfig::get('app_google_analytics_uacct');
      if (!$varsConfig['uacct'])
      {
        throw new sfGoogleAnalyticsException('Please add your Google Analytics account number to your app.yml.');
      }
    }
    $vars = array();
    foreach ($varsConfig as $key => $value)
    {
      if ($key{0} != '_')
      {
        $key = '_'.$key;
      }
      $vars[] = $key.'='.self::escape($value);
    }
    
    // capture and prepare the parameter for the initial call to urchinTracker()
    $utParam = self::getFinalConfigValue('ut_param');
    if ($utParam)
    {
      $utParam = self::escape($utParam);
    }
    
    // capture and prepare any custom tracking variables that have been 
    // configured for this response.
    $customConfig = self::getFinalConfigValue('custom', true);
    $custom = array();
    foreach ($customConfig as $value)
    {
      $custom[] = sprintf('__utmSetVar(%s);', self::escape($value));
    }
    
    // build the html block for insertion
    $html   = array('');
    $html[] = sprintf('<script type="text/javascript" src="%s"></script>', $usrc);
    $html[] = '<script type="text/javascript">';
    $html[] = get_slot('google_analytics_top');
    $html[] = join("\n", $vars);
    $html[] = sprintf('urchinTracker(%s);', $utParam);
    $html[] = join("\n", $custom);
    $html[] = get_slot('google_analytics_bottom');
    $html[] = '</script>';
    
    $html = array_unique($html);
    $html = join("\n", array_slice($html, 1));
    
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
   * Escape a string value for Javascript.
   * 
   * This method will use the JSON extension if it is available.
   * 
   * @author  Kris Wallsmith
   * @param   string $value
   * @return  string
   */
  protected static function escape($value)
  {
    if (function_exists('json_encode'))
    {
      $escaped = json_encode($value);
    }
    else
    {
      sfLoader::loadHelpers(array('Escaping'));
      $escaped = '"'.esc_js($value).'"';
    }
    
    return $escaped;
  }
  
  /**
   * Get a configuration value.
   * 
   * Checks application, module and action-level configuration.
   * 
   * @author  Kris Wallsmith
   * @param   string $key
   * @param   bool $merge - treat value as array and merge configs
   * @return  mixed
   */
  protected static function getFinalConfigValue($key, $merge = false)
  {
    $module = sfContext::getInstance()->getModuleName();
    
    $actionConfig = self::getActionConfig();
    $actionConfig = isset($actionConfig[$key]) ? $actionConfig[$key] : null;
    
    $moduleConfig = sfConfig::get('mod_'.$module.'_google_analytics_'.$key);
    $appConfig = sfConfig::get('app_google_analytics_'.$key);
    
    if ($merge)
    {
      $value = array_merge((array) $appConfig, (array) $moduleConfig, (array) $actionConfig);
    }
    else
    {
      $value = $actionConfig ? $actionConfig : ($moduleConfig ? $moduleConfig : $appConfig);
    }
    
    return $value;
  }
  
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
