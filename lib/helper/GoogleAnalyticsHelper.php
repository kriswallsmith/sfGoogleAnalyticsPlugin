<?php

/**
 * A collection of helper functions for attaching analytics tracking code to
 * links. Used for outbound links and downloads. Functions all produce normal
 * links when analytics is turned off in the configuration.
 * 
 * @package     sfGoogleAnalyticsPlugin
 * @subpackage  helper
 * @author      Kris Wallsmith <kris [dot] wallsmith [at] gmail [dot] com>
 * @version     SVN: $Id$
 */

use_helper('Url', 'Javascript');

/**
 * Build a link that includes a call to the Javascript urchinTracker function.
 * 
 * Usually used when linking off your site or to any file on your site that
 * does not include tracking code (i.e. PDF documents, images, etc.)
 * 
 * @param   string $name        - name of the link
 * @param   string $internalUri - module/action or @rule
 * @param   string $urchinUri   - custom path for urchinTracker
 * @param   array  $options     - additional HTML parameters
 * 
 * @return  string
 */
function google_analytics_link_to($name, $internalUri, $urchinUri = null, $options = array())
{
  if (sfConfig::get('app_google_analytics_enabled'))
  {
    if (!$urchinUri)
    {
      $urchinUri = url_for($internalUri);
    }
    
    $newOnclick = 'urchinTracker(\''.$urchinUri.'\');';
    
    $options = _parse_attributes($options);
    $options['onclick'] = isset($options['onclick']) ? ($newOnclick.$options['onclick']) : $newOnclick;
  }

  return link_to($name, $internalUri, $options);
}

/**
 * Build a link to a Javascript call, including a call to urchinTracker.
 * 
 * @param   string $name      - name of the link
 * @param   string $function  - Javascript code
 * @param   string $urchinUri - custom path for urchinTracker
 * @param   array  $options   - additional HTML parameters
 * 
 * @return  string
 */
function google_analytics_link_to_function($name, $function, $urchinUri, $options = array())
{
  $link = link_to_function($name, $function, $options);
  
  if (sfConfig::get('app_google_analytics_enabled')) 
  {
    $link = str_replace('onclick="', 'onclick="urchinTracker(\''.$urchinUri.'\');', $link);
  }
  
  return $link;
}

/**
 * Add a custom variable to the tracking code.
 * 
 * @author  Kris Wallsmith
 * @param   string $var
 */
function google_analytics_custom_var($var)
{
  sfGoogleAnalyticsToolkit::addCustomVar($var);
}
