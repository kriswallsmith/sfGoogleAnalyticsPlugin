<?php

/**
 * User mixin methods for the sfGoogleAnalyticsPlugin.
 * 
 * @package     sfGoogleAnalyticsPlugin
 * @subpackage  user
 * @author      Kris Wallsmith <kris.wallsmith@symfony-project.com>
 * @version     SVN: $Id$
 */
class sfGoogleAnalyticsUserMixin
{
  /**
   * Convenience method for adding custom tracking variables to flash storage.
   * 
   * For example, you can track site usage by gender by adding either a "male"
   * or "female" variable to analytics upon signin. You would only want to 
   * use sfFlash storage for this purpose if the action that calls this method
   * subsequently redirects, as most signin actions do.
   * 
   * <code>
   * // overload the sfGuardSecurityUser::signIn() method
   * public function signIn($user, $remember = false, $con = null)
   * {
   *   $retval = parent::signIn($user, $remember, $con);
   * 
   *   $this->addGoogleAnalyticsCustomVarToFlash('gender/'.$this->getGuardUser()->getProfile()->getGender());
   * 
   *   return $retval;
   * }
   * </code>
   * 
   * @author  Kris Wallsmith
   * @param   sfUser $user
   * @param   string $var
   * @param   bool $persist
   * @see     sfGoogleAnalyticsActionMixin::addGoogleAnalyticsCustomVarToFlash()
   * @todo    support updated symfony 1.1 flash architecture
   */
  public static function addGoogleAnalyticsCustomVarToFlash(sfUser $user, $var, $persist = true)
  {
    $user->
      getContext()->
      getActionStack()->
      getLastEntry()->
      getActionInstance()->
      addGoogleAnalyticsCustomVarToFlash($var, $persist);
  }
  
}
