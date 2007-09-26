<?php

/**
 * Renders tracking code at the bottom of every page.
 * 
 * To activate, add the following code to your application's filters.yml file,
 * just below the rendering filter.
 * 
 * <code>
 *  rendering: ~
 *  
 *  # sfUrchinPlugin filter
 *  urchin:
 *    class: sfUrchinFilter
 *  
 *  web_debug: ~
 *  # etc ...
 * </code>
 * 
 * @package     sfUrchinPlugin
 * @subpackage  filter
 * @author      Kris Wallsmith <kris [dot] wallsmith [at] gmail [dot] com>
 * @version     SVN: $Id$
 */
class sfUrchinFilter extends sfFilter
{
  public function execute($filterChain)
  {
    $filterChain->execute();
    
    $context    = $this->getContext();
    $request    = $context->getRequest();
    $response   = $context->getResponse();
    $controller = $context->getController();
    
    // don't add analytics:
    // * if urchin is not enabled
    // * for XHR requests
    // * if 304
    // * if not rendering to the client
    // * if HTTP headers only
    if (!sfConfig::get('app_urchin_enabled') ||
        $request->isXmlHttpRequest() ||
        strpos($response->getContentType(), 'html') === false ||
        $response->getStatusCode() == 304 ||
        $controller->getRenderMode() != sfView::RENDER_CLIENT ||
        $response->isHeaderOnly())
    {
      return;
    }
    
    $content = $response->getContent();
    $newContent = str_ireplace('</body>', sfUrchinToolkit::getHtml().'</body>', $content);
    if ($content == $newContent)
    {
      $newContent .= $urchinTracker;
    }
    
    $response->setContent($newContent);
  }
}
