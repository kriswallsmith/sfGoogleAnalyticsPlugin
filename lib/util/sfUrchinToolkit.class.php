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
    if(sfConfig::get('app_urchin_enabled'))
    {
      $uacct = sfConfig::get('app_urchin_uacct');
      if(!$uacct)
      {
        $msg = 'Please add your Urchin account number to your app.yml (urchin_uacct)';
        
        throw new sfUrchinException($msg);
      }
      
      $usrc = sfConfig::get('app_urchin_usrc', 'http://www.google-analytics.com/urchin.js');
      
      $utParam = self::getUtParam();
      if($utParam)
      {
        $utParam = '"'.$utParam.'"';
      }
      
      $html = <<<EOD
<script src="$usrc" type="text/javascript"></script>
<script type="text/javascript">
_uacct = "$uacct";
urchinTracker($utParam);
</script>
EOD;
      
      return $html;
    }
  }
  
  /**
   * Set a custom ut_param for use in the first urchinTracker() call.
   * 
   * @author  Kris Wallsmith
   * 
   * @param   string $utParam
   */
  public static function setUtParam($utParam)
  {
    sfContext::getInstance()->getResponse()->setParameter('ut_param', $utParam);
  }
  
  /**
   * Get any custom ut_param set in the response object.
   * 
   * @author  Kris Wallsmith
   * 
   * @param   string $defaultValue
   * 
   * @return  string
   */
  public static function getUtParam($defaultValue = null)
  {
    return sfContext::getInstance()->getResponse()->getParameter('ut_param', $defaultValue);
  }
}
