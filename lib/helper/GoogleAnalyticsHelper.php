<?php

/**
 * Helpers for sfGoogleAnalyticsPlugin.
 * 
 * @package     sfGoogleAnalyticsPlugin
 * @subpackage  helper
 * @author      Kris Wallsmith <kris [dot] wallsmith [at] gmail [dot] com>
 * @version     SVN: $Id$
 */

sfLoader::loadHelpers(array('Tag', 'Url'));

/**
 * Build a link that tracks a page view.
 * 
 * Options can include:
 * 
 *  * track_as:   an internal URI other than the link's href
 *  * is_route:   whether to send the URI through sfRouting
 *  * is_event:   track as an event rather than a page view
 *  * use_linker: use this if you're linking to another domain that tracks on
 *                the same website profile
 * 
 * @param   string $name
 * @param   string $internalUri
 * @param   array $options
 * 
 * @return  string
 */
function google_analytics_link_to($name = null, $internalUri = null, $options = array())
{
  $tracker = sfContext::getInstance()->getRequest()->getTracker();
  
  $options = _parse_attributes($options);
  $trackerOptions = $tracker->extractViewOptions($options);
  
  if ($tracker->isEnabled())
  {
    $trackAs = isset($trackerOptions['track_as']) ? $trackerOptions['track_as'] : $internalUri;
    
    if (isset($trackerOptions['use_linker']) && $trackerOptions['use_linker'])
    {
      $onclick = google_analytics_linker_function($trackAs, $trackerOptions);
    }
    else
    {
      $onclick = $tracker->forgePageViewFunction($trackAs, $trackerOptions);
    }
    
    $options['onclick'] = isset($options['onclick']) ? ($onclick.$options['onclick']) : $onclick;
    
    if (isset($trackerOptions['use_linker']) && $trackerOptions['use_linker'])
    {
      $options['onclick'] .= 'return false';
    }
  }
  
  return link_to($name, $internalUri, $options);
}

/**
 * Build a Javascript link that tracks a page view.
 * 
 * Options can include:
 * 
 *  * track_as: an internal URI (required)
 *  * is_route: whether to send the URI through sfRouting
 *  * is_event: track as an event rather than a page view (for those trackers 
 *              that support this option)
 * 
 * @throws  sfViewException if "track_as" option is absent
 * 
 * @param   string $name
 * @param   string $internalUri
 * @param   array $options
 * 
 * @return  string
 */
function google_analytics_link_to_function($name, $function, $options = array())
{
  sfLoader::loadHelpers(array('Javascript'));
  
  $tracker = sfContext::getInstance()->getRequest()->getTracker();
  
  $options = _parse_attributes($options);
  $trackerOptions = $tracker->extractViewOptions($options);
  
  $link = link_to_function($name, $function, $options);
  $link = _add_onclick_tracking($tracker, $link, $trackerOptions);
  
  return $link;
}

/**
 * Build a Javascript link that tracks a page view.
 * 
 * Options (2nd parameter) can include:
 * 
 *  * track_as: an internal URI (required)
 *  * is_route: whether to send the URI through sfRouting
 *  * is_event: track as an event rather than a page view (for those trackers 
 *              that support this option)
 * 
 * @throws  sfViewException if "track_as" option is absent
 * 
 * @param   string $name
 * @param   array $options
 * @param   array $html_options
 * 
 * @return  string
 */
function google_analytics_link_to_remote($name, $options = array(), $html_options = array())
{
  sfLoader::loadHelpers(array('Javascript'));
  
  $tracker = sfContext::getInstance()->getRequest()->getTracker();
  
  $options = _parse_attributes($options);
  $trackerOptions = $tracker->extractViewOptions($options);
  
  $link = link_to_remote($name, $options, $html_options);
  $link = _add_onclick_tracking($tracker, $link, $trackerOptions);
  
  return $link;
}

/**
 * Build a call to the Javascript linker function.
 * 
 * @param   string $url
 * 
 * @return  string
 */
function google_analytics_linker_function($url)
{
  $tracker = sfContext::getInstance()->getRequest()->getTracker();
  
  _check_linker_settings($tracker);
  
  $linker = null;
  if ($tracker->isEnabled())
  {
    $linker = $tracker->forgeLinkerFunction($url);
  }
  
  return $linker;
}

/**
 * Build a call to the Javascript POST linker function.
 * 
 * @param   string $formElement
 * 
 * @return  string
 */
function google_analytics_post_linker_function($formElement = 'this')
{
  $tracker = sfContext::getInstance()->getRequest()->getTracker();
  
  _check_linker_settings($tracker);
  
  $linker = null;
  if ($tracker->isEnabled())
  {
    $linker = $tracker->forgePostLinkerFunction($formElement);
  }
  
  return $linker;
}

/**
 * Inserts a page view into the supplied link's onclick attribute.
 * 
 * @throws  sfViewException if "track_as" option is absent
 * 
 * @param   sfGoogleAnalyticsTracker $tracker
 * @param   string $link
 * @param   array $options
 * 
 * @return  string
 */
function _add_onclick_tracking(sfGoogleAnalyticsTracker $tracker, $link, $options = array())
{
  if (!isset($options['track_as']))
  {
    throw new sfViewException(sprintf('{%s} The "track_as" parameter is required.', basename(__FILE__)));
  }
  
  $tracker = sfContext::getInstance()->getRequest()->getTracker();
  if ($tracker->isEnabled())
  {
    $onclick = $tracker->forgePageViewFunction($options['track_as'], $options);
    $onclick = escape_once($onclick);
    
    $link = str_replace('onclick="', 'onclick="'.$onclick.' ', $link);
  }
  
  return $link;
}

/**
 * Confirm the tracker is configured correctly to support a linker.
 * 
 * @param   sfGoogleAnalyticsTracker $tracker
 */
function _check_linker_settings(sfGoogleAnalyticsTracker $tracker)
{
  if ($tracker->getDomainName() !== 'none' || $tracker->getLinkerPolicy() !== true)
  {
    // a friendly note...
    sfContext::getInstance()->getLogger()->notice('{%s} If tracking multiple domain names on one profile, the app.yml "domain_name" setting should be "off" and the "linker_policy" setting should be "on".', basename(__FILE__));
  }
}
