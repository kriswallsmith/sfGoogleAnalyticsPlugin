<?php

/**
 * Google Analytics urchin.js tracker.
 * 
 * @package     sfGoogleAnalyticsPlugin
 * @subpackage  tracker
 * @author      Kris Wallsmith <kris.wallsmith@symfony-project.com>
 * @version     SVN: $Id$
 */
class sfGoogleAnalyticsTrackerUrchin extends sfGoogleAnalyticsTracker
{
  /**
   * @see sfGoogleAnalyticsTracker
   */
  public function insert(sfResponse $response)
  {
    $html = array();
    $html[] = $this->context->getRequest()->isSecure() ?
      '<script src="https://ssl.google-analytics.com/urchin.js" type="text/javascript"></script>' :
      '<script src="http://www.google-analytics.com/urchin.js" type="text/javascript"></script>';
    $html[] = '<script type="text/javascript">';
    $html[] = '//<![CDATA[';
    $html[] = sprintf('_uacct=%s;', $this->escape($this->getProfileId()));
    if ($domainName = $this->getDomainName())
    {
      $html[] = sprintf('_udn=%s;', $this->escape($domainName));
    }
    
    if ($this->getLinkerPolicy())
    {
      $html[] = '_ulink=1;';
    }
    
    foreach ($this->getOrganicReferers() as $i => $referer)
    {
      list($name, $param) = $referer;
      
      $html[] = sprintf('_uOsr[%d]=%s;', $i+20, $this->escape($name));
      $html[] = sprintf('_uOkw[%d]=%s;', $i+20, $this->escape($param));
    }
    
    if ($cookiePath = $this->getCookiePath())
    {
      $html[] = sprintf('_utcp=%s;', $this->escape($cookiePath));
    }
    
    // data collection
    if (!$this->getClientInfoPolicy())
    {
      $html[] = '_ufsc=0;';
    }
    if (!$this->getHashPolicy())
    {
      $html[] = '_uhash=0;';
    }
    if (!$this->getDetectFlashPolicy())
    {
      $html[] = '_uflash=0;';
    }
    if (!$this->getDetectTitlePolicy())
    {
      $html[] = '_utitle=0;';
    }
    
    if ($timeout = $this->getSessionTimeout())
    {
      $html[] = sprintf('_utimeout=%d;', $timeout);
    }
    
    if ($timeout = $this->getCookieTimeout())
    {
      $html[] = sprintf('_ucto=%d;', $timeout);
    }
    
    // campaign parameters
    if ($nameKey = $this->getCampaignNameKey())
    {
      $html[] = sprintf('_uccn=%s;', $this->escape($nameKey));
    }
    if ($mediumKey = $this->getCampaignMediumKey())
    {
      $html[] = sprintf('_ucmd=%s;', $this->escape($mediumKey));
    }
    if ($sourceKey = $this->getCampaignSourceKey())
    {
      $html[] = sprintf('_ucsr=%s;', $this->escape($sourceKey));
    }
    if ($termKey = $this->getCampaignTermKey())
    {
      $html[] = sprintf('_uctr=%s;', $this->escape($termKey));
    }
    if ($contentKey = $this->getCampaignContentKey())
    {
      $html[] = sprintf('_ucct=%s;', $this->escape($contentKey));
    }
    if ($idKey = $this->getCampaignIdKey())
    {
      $html[] = sprintf('_ucid=%s;', $this->escape($idKey));
    }
    if ($noOverrideKey = $this->getCampaignNoOverrideKey())
    {
      $html[] = sprintf('_ucno=%s;', $this->escape($noOverrideKey));
    }
    
    if ($this->getAnchorPolicy())
    {
      $html[] = '_uanchor=1;';
    }
    
    foreach ($this->getIgnoredOrganics() as $i => $keyword)
    {
      $html[] = sprintf('_uOno[%d]=%s;', $i, $this->escape($keyword));
    }
    foreach ($this->getIgnoredReferers() as $i => $referer)
    {
      $html[] = sprintf('_uRno[%d]=%s;', $i, $this->escape($referer));
    }
    
    if ($rate = $this->getSampleRate())
    {
      $html[] = sprintf('_usample=%d;', $rate);
    }
    
    if ($this->getLocalRemoteServerPolicy())
    {
      $html[] = '_userv=2;';
    }
    
    if ($before = $this->getBeforeTrackerJS())
    {
      $html[] = $before;
    }
    
    if ($pageName = $this->getPageName())
    {
      $html[] = sprintf('urchinTracker(%s);', $this->escape($pageName));
    }
    else
    {
      $html[] = 'urchinTracker();';
    }
    
    foreach ($this->getVars() as $var)
    {
      $html[] = sprintf('__utmSetVar(%s);', $this->escape($var));
    }

    // custom vars does not exist for urchin


    if ($after = $this->getAfterTrackerJS())
    {
      $html[] = $after;
    }
    
    $html[] = '//]]>';
    $html[] = '</script>';
    
    if ($transaction = $this->getTransaction())
    {
      $html[] = '<form name="utmform" id="utmform">';
      $html[] = '<textarea name="utmtrans" id="utmtrans" style="display:none">';
      $html[] = vsprintf('UTM:T|%s|%s|%s|%s|%s|%s|%s|%s', $transaction->getValues());
      foreach ($transaction->getItems() as $item)
      {
        $html[] = sprintf('UTM:I|%s|%s|%s|%s|%s|%s', $item->getValues());
      }
      $html[] = '</textarea>';
      $html[] = '</form>';
      $html[] = '<script type="text/javascript">';
      $html[] = '//<![CDATA[';
      $html[] = '__utmSetTrans();';
      $html[] = '//]]>';
      $html[] = '</script>';
    }
    
    $html = join("\n", $html);
    $this->doInsert($response, $html, $this->insertion);
  }
  
  /**
   * @see sfGoogleAnalyticsTracker
   */
  public function forgePageViewFunction($path = null, $options = array())
  {
    $this->prepare($path, $options);
    
    return sprintf('urchinTracker(%s);', is_null($path) ? null : $this->escape($path));
  }
  
  /**
   * @see sfGoogleAnalyticsTracker
   */
  public function forgeLinkerFunction($url)
  {
    return sprintf('__utmLinker(%s);', $this->escape($url));
  }
  
  /**
   * @see sfGoogleAnalyticsTracker
   */
  public function forgePostLinkerFunction($formElement = 'this')
  {
    return sprintf('__utmLinkPost(%s);', $formElement);
  }
  
}
