<?php

/**
 * Static utility methods.
 * 
 * @package     sfGoogleAnalyticsPlugin
 * @subpackage  util
 * @author      Kris Wallsmith <kris.wallsmith@symfony-project.com>
 * @version     SVN: $Id$
 */
class sfGoogleAnalyticsToolkit
{
  /**
   * Log a message.
   * 
   * @param   mixed   $subject
   * @param   string  $message
   * @param   string  $priority
   */
  static public function logMessage($subject, $message, $priority = 'info')
  {
    if (class_exists('ProjectConfiguration', false))
    {
      ProjectConfiguration::getActive()->getEventDispatcher()->notify(new sfEvent($subject, 'application.log', array($message, 'priority' => constant('sfLogger::'.strtoupper($priority)))));
    }
    else
    {
      $message = sprintf('{%s} %s', is_object($subject) ? get_class($subject) : $subject, $message);
      sfContext::getInstance()->getLogger()->log($message, constant('SF_LOG_'.strtoupper($priority)));
    }
  }
}
