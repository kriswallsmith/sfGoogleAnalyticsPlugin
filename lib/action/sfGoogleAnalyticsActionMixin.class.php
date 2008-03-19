<?php

/**
 * Action mixin methods for the sfGoogleAnalyticsPlugin.
 * 
 * @package     sfGoogleAnalyticsPlugin
 * @subpackage  action
 * @author      Kris Wallsmith <kris [dot] wallsmith [at] gmail [dot] com>
 * @version     SVN: $Id$
 */
class sfGoogleAnalyticsActionMixin
{
  /**
   * Set a custom parameter for Google Analytics initialization.
   * 
   * @author  Kris Wallsmith
   * @param   sfComponent $action
   * @param   string $utParam
   */
  public static function setGoogleAnalyticsParam(sfComponent $action, $utParam)
  {
    sfGoogleAnalyticsToolkit::setParam($utParam);
  }
  
  /**
   * Add a Google Analytics initialization variable.
   * 
   * @author  Kris Wallsmith
   * @param   sfComponent $action
   * @param   string $name
   * @param   string $value
   */
  public static function addGoogleAnalyticsVar(sfComponent $action, $name, $value)
  {
    sfGoogleAnalyticsToolkit::addVar($name, $value);
  }
  
  /**
   * Add a custom variable to Google Analytics.
   * 
   * @author  Kris Wallsmith
   * @param   sfComponent $action
   * @param   string $var
   */
  public static function addGoogleAnalyticsCustomVar(sfComponent $action, $var)
  {
    sfGoogleAnalyticsToolkit::addCustomVar($var);
  }
  
  /**
   * Add a custom variable that will render on the next request.
   * 
   * @author  Kris Wallsmith
   * @param   sfComponent $action
   * @param   string $var
   * @param   bool $persist
   * @see     comment block for sfGoogleAnalyticsUserMixin::addGoogleAnalyticsCustomVarToFlash()
   * @todo    support updated symfony 1.1 flash architecture
   */
  public static function addGoogleAnalyticsCustomVarToFlash(sfComponent $action, $var, $persist = true)
  {
    $vars = $action->getFlash('google_analytics_custom_vars', array());
    $vars[] = $var;
    $action->setFlash('google_analytics_custom_vars', $vars, $persist);
  }
  
}
