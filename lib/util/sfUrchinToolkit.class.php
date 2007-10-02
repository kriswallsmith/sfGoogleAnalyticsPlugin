<?php

/**
 * Utility methods for the sfUrchinPlugin.
 * 
 * @package     sfUrchinPlugin
 * @subpackage  util
 * @author      Kris Wallsmith <kris [dot] wallsmith [at] gmail [dot] com>
 * @version     SVN: $Id$
 */
class sfUrchinToolkit
{
  /**
   * Get HTML for insert at the bottom of a document.
   * 
   * @author  Kris Wallsmith
   * @throws  sfUrchinException
   * 
   * @return  string
   */
  public static function getHtml()
  {
    sfLoader::loadHelpers(array('Escaping'));
    
    $context = sfContext::getInstance();
    $request = $context->getRequest();
    $module  = $context->getModuleName();
    $action  = $context->getActionName();
    
    $actionConfig = sfConfig::get('mod_'.$module.'_'.$action.'_urchin', array());
    
    $usrc = $request->isSecure() ? 
      sfConfig::get('app_urchin_usrc_ssl', 'https://ssl.google-analytics.com/urchin.js') : 
      sfConfig::get('app_urchin_usrc', 'http://www.google-analytics.com/urchin.js');
    
    // initial parameter
    $utParam = '';
    if (isset($actionConfig['ut_param']))
    {
      $utParam = $actionConfig['ut_param'];
    }
    else
    {
      $utParam = sfConfig::get('app_urchin_ut_param');
      $utParam = sfConfig::get('mod_'.$module.'_urchin_ut_param', $utParam);
    }
    
    // initialization variables
    $vars = sfConfig::get('app_urchin_vars', array());
    $vars = array_merge($vars, sfConfig::get('mod_'.$module.'_urchin_vars', array()));
    if (isset($actionConfig['vars']) && is_array($actionConfig['vars']))
    {
      $vars = array_merge($vars, $actionConfig['vars']);
    }
    
    // account number is required
    if (!isset($vars['uacct']) && !isset($vars['_uacct']))
    {
      // backwards compatibility
      $vars['uacct'] = sfConfig::get('app_urchin_uacct');
      if (!$vars['uacct'])
      {
        throw new sfUrchinException('Please add your Urchin account number to your app.yml.');
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
    
    $html = <<<EOD
<script src="%s" type="text/javascript"></script>
<script type="text/javascript">
%s
urchinTracker(%s);
</script>
EOD;
    $html = sprintf($html, $usrc, $jsVars, $utParam);
    
    return $html;
  }
  
}
