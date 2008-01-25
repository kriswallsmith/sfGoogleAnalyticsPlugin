<?php

/**
 * Renders tracking code on every page.
 * 
 * To activate, add the following code to your application's filters.yml file,
 * just below the web_debug filter.
 * 
 * <code>
 *  rendering: ~
 *  web_debug: ~
 *  
 *  # sfGoogleAnalyticsPlugin filter
 *  google_analytics:
 *    class: sfGoogleAnalyticsFilter
 *  
 *  # etc ...
 * </code>
 * 
 * @package     sfGoogleAnalyticsPlugin
 * @subpackage  filter
 * @author      Kris Wallsmith <kris [dot] wallsmith [at] gmail [dot] com>
 * @version     SVN: $Id$
 */
class sfGoogleAnalyticsFilter extends sfFilter
{
  /**
   * Insert tracking code for applicable web requests.
   * 
   * @author  Kris Wallsmith
   * 
   * @param   sfFilterChain $filterChain
   */
  public function execute($filterChain)
  {
    $filterChain->execute();
    
    if ($this->isTrackable())
    {
      $insertion    = sfConfig::get('app_google_analytics_insertion', 'bottom');
      $insertMethod = 'insertTrackingCode'.$insertion;
      
      if (method_exists($this, $insertMethod))
      {
        if (sfConfig::get('sf_logging_enabled'))
        {
          $this->getContext()->getLogger()->info('{sfGoogleAnalyticsFilter} Inserting tracking code in "'.$insertion.'" position.');
        }
        
        $trackingCode = $this->generateTrackingCode();
        call_user_func(array($this, $insertMethod), "\n".$trackingCode);
      }
      else
      {
        throw new sfGoogleAnalyticsException('Unrecognized insertion.');
      }
    }
    elseif (sfConfig::get('sf_logging_enabled'))
    {
      $this->getContext()->getLogger()->info('{sfGoogleAnalyticsFilter} Tracking code not inserted.');
    }
  }
  
  /**
   * Test whether tracking code should be inserted for this request.
   * 
   * @author  Kris Wallsmith
   * 
   * @return  bool
   */
  protected function isTrackable()
  {
    $context    = $this->getContext();
    $request    = $context->getRequest();
    $response   = $context->getResponse();
    $controller = $context->getController();
    
    // don't add analytics:
    // * if google analytics is not enabled
    // * for XHR requests
    // * if not HTML
    // * if 304
    // * if not rendering to the client
    // * if HTTP headers only
    if (!sfConfig::get('app_google_analytics_enabled') ||
        $request->isXmlHttpRequest() ||
        strpos($response->getContentType(), 'html') === false ||
        $response->getStatusCode() == 304 ||
        $controller->getRenderMode() != sfView::RENDER_CLIENT ||
        $response->isHeaderOnly())
    {
      return false;
    }
    else
    {
      return true;
    }
  }
  
  /**
   * Insert supplied tracking code at the top of the body tag.
   * 
   * @author  Kris Wallsmith
   * 
   * @param   string $trackingCode
   */
  protected function insertTrackingCodeTop($trackingCode)
  {
    $response = $this->getContext()->getResponse();
    
    $oldContent = $response->getContent();
    $newContent = preg_replace('/\<body[^\>]*\>/i', "$0".$trackingCode, $oldContent, 1);
    
    if ($oldContent == $newContent)
    {
      $newContent .= $trackingCode;
    }
    
    $response->setContent($newContent);
  }
  
  /**
   * Insert supplied tracking code at the bottom of the body tag.
   * 
   * @author  Kris Wallsmith
   * 
   * @param   string $trackingCode
   */
  protected function insertTrackingCodeBottom($trackingCode)
  {
    $response = $this->getContext()->getResponse();
    
    $oldContent = $response->getContent();
    $newContent = str_ireplace('</body>', $trackingCode.'</body>', $oldContent);
    
    if ($oldContent == $newContent)
    {
      $newContent .= $trackingCode;
    }
    
    $response->setContent($newContent);
  }
  
  /**
   * Get tracking code for insertion.
   *
   * @author  Kris Wallsmith
   * 
   * @return  string
   */
  protected function generateTrackingCode()
  {
    return sfGoogleAnalyticsToolkit::getHtml();
  }
}
