<?php

/**
 * Static utility methods.
 * 
 * @package     sfGoogleAnalyticsPlugin
 * @subpackage  util
 * @author      Kris Wallsmith <kris [dot] wallsmith [at] gmail [dot] com>
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
  static public function logMessage($subject, $message, $priority = null)
  {
    $priority = strtoupper($priority);
    
    if (class_exists('ProjectConfiguration'))
    {
      ProjectConfiguration::getActive()->getEventDispatcher()->notify(new sfEvent($subject, 'application.log', array($message, 'priority' => $priority ? constant('sfLogger::'.$priority) : sfLogger::INFO)));
    }
    else
    {
      $message = sprintf('{%s} %s', is_object($subject) ? get_class($subject) : $subject, $message);
      sfContext::getInstance()->getLogger()->log($message, constant('SF_LOG_'.$priority));
    }
  }
}
