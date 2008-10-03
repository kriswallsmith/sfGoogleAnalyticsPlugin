<?php

/**
 * main actions.
 *
 * @package    sfGoogleAnalyticsProject
 * @subpackage main
 * @author     Your name here
 * @version    SVN: $Id$
 */
class mainActions extends sfActions
{
  /**
   * Executes index action
   *
   */
  public function executeIndex()
  {
    $this->forward('default', 'module');
  }
}
