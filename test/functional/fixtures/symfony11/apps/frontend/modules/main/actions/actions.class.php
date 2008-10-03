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
  * @param sfRequest $request A request object
  */
  public function executeIndex($request)
  {
    $this->forward('default', 'module');
  }
}
