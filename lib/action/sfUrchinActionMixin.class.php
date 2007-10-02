<?php

/**
 * Action mixin methods for the sfUrchinPlugin.
 * 
 * @package     sfUrchinPlugin
 * @subpackage  action
 * @author      Kris Wallsmith <kris [dot] wallsmith [at] gmail [dot] com>
 * @version     SVN: $Id$
 */
class sfUrchinActionMixin
{
  /**
   * Set a custom parameter for Google Analytics initialization.
   * 
   * @author  Kris Wallsmith
   * 
   * @param   sfComponent $action
   * @param   string $utParam
   */
  public static function setUrchinParam($action, $utParam)
  {
    $moduleName = $action->getModuleName();
    $actionName = $action->getActionName();
    
    $actionConfig = sfConfig::get('mod_'.$moduleName.'_'.$actionName.'_urchin', array());
    $actionConfig['ut_param'] = $utParam;
    
    sfConfig::set('mod_'.$moduleName.'_'.$actionName.'_urchin', $actionConfig);
  }
  
  /**
   * Add a Google Analytics initialization variable.
   * 
   * @author  Kris Wallsmith
   * 
   * @param   sfComponent $action
   * @param   string $name
   * @param   string $value
   */
  public static function addUrchinVar($action, $name, $value)
  {
    $moduleName = $action->getModuleName();
    $actionName = $action->getActionName();
    
    $actionConfig = sfConfig::get('mod_'.$moduleName.'_'.$actionName.'_urchin', array());
    if (!isset($actionConfig['vars']))
    {
      $actionConfig['vars'] = array();
    }
    $actionConfig['vars'][$name] = $value;
    
    sfConfig::set('mod_'.$moduleName.'_'.$actionName.'_urchin', $actionConfig);
  }
  
}
