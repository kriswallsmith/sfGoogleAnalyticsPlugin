<?php

/**
 * Google Analytics ga.js tracker.
 * 
 * @package     sfGoogleAnalyticsPlugin
 * @subpackage  tracker
 * @author      Kris Wallsmith <kris.wallsmith@symfony-project.com>
 * @version     SVN: $Id$
 */
class sfGoogleAnalyticsTrackerGoogle extends sfGoogleAnalyticsTracker
{
  protected
    $trackerVar = 'pageTracker';
  
  public function configure($params)
  {
    parent::configure($params);
    
    $params = array_merge(array(
      'tracker_var' => null), $params);
    
    if (!is_null($params['tracker_var']))
    {
      $this->setTrackerVar($params['tracker_var']);
    }
  }
  
  public function setTrackerVar($tracker)
  {
    $this->trackerVar = $tracker;
  }
  public function getTrackerVar()
  {
    return $this->trackerVar;
  }
  
  /**
   * @see sfGoogleAnalyticsTracker
   */
  public function insert(sfResponse $response)
  {
    $tracker = $this->getTrackerVar();
    
    $html = array();
    $html[] = '<script type="text/javascript">';
    $html[] = '//<![CDATA[';
    $html[] = 'var gaJsHost=(("https:"==document.location.protocol)?"https://ssl.":"http://www.");';
    $html[] = 'document.write(unescape("%3Cscript src=\'"+gaJsHost+"google-analytics.com/ga.js\' type=\'text/javascript\'%3E%3C/script%3E"));';
    $html[] = '//]]>';
    $html[] = '</script>';
    $html[] = '<script type="text/javascript">';
    $html[] = '//<![CDATA[';
    
    $html[] = sprintf('var %s=_gat._getTracker(%s);', $tracker, $this->escape($this->getProfileId()));
    $html[] = sprintf('%s._initData();', $tracker);
    
    if ($domainName = $this->getDomainName())
    {
      $html[] = sprintf('%s._setDomainName(%s);', $tracker, $this->escape($domainName));
    }
    
    if ($this->getLinkerPolicy())
    {
      $html[] = sprintf('%s._setAllowLinker(true);', $tracker);
    }
    
    foreach ($this->getOrganicReferers() as $i => $referer)
    {
      list($name, $param) = $referer;
      
      $html[] = sprintf('%s._addOrganic(%s, %s);', $tracker, $this->escape($name), $this->escape($param));
    }
    
    if ($cookiePath = $this->getCookiePath())
    {
      $html[] = sprintf('%s._setCookiePath(%s);', $tracker, $this->escape($cookiePath));
    }
    
    // data collection
    if (!$this->getClientInfoPolicy())
    {
      $html[] = sprintf('%s._setClientInfo(false);', $tracker);
    }
    if (!$this->getHashPolicy())
    {
      $html[] = sprintf('%s._setAllowHash(false);', $tracker);
    }
    if (!$this->getDetectFlashPolicy())
    {
      $html[] = sprintf('%s._setDetectFlash(false);', $tracker);
    }
    if (!$this->getDetectTitlePolicy())
    {
      $html[] = sprintf('%s._setDetectTitle(false);', $tracker);
    }
    
    if ($timeout = $this->getSessionTimeout())
    {
      $html[] = sprintf('%s._setSessionTimeout(%d);', $tracker, $timeout);
    }
    
    if ($timeout = $this->getCookieTimeout())
    {
      $html[] = sprintf('%s._setCookieTimeout(%d);', $tracker, $timeout);
    }
    
    // campaign parameters
    if ($nameKey = $this->getCampaignNameKey())
    {
      $html[] = sprintf('%s._setCampNameKey(%s);', $tracker, $nameKey);
    }
    if ($mediumKey = $this->getCampaignMediumKey())
    {
      $html[] = sprintf('%s._setCampMediumKey(%s);', $tracker, $mediumKey);
    }
    if ($sourceKey = $this->getCampaignSourceKey())
    {
      $html[] = sprintf('%s._setCampSourceKey(%s);', $tracker, $sourceKey);
    }
    if ($termKey = $this->getCampaignTermKey())
    {
      $html[] = sprintf('%s._setCampTermKey(%s);', $tracker, $termKey);
    }
    if ($contentKey = $this->getCampaignContentKey())
    {
      $html[] = sprintf('%s._setCampContentKey(%s);', $tracker, $contentKey);
    }
    if ($idKey = $this->getCampaignIdKey())
    {
      $html[] = sprintf('%s._setCampIdKey(%s);', $tracker, $idKey);
    }
    if ($noOverrideKey = $this->getCampaignNoOverrideKey())
    {
      $html[] = sprintf('%s._setCampNOKey(%s);', $tracker, $noOverrideKey);
    }

    if ($this->getAnchorPolicy())
    {
      $html[] = sprintf('%s._setAllowAnchor(true);', $tracker);
    }

    foreach ($this->getIgnoredOrganics() as $keyword)
    {
      $html[] = sprintf('%s._addIgnoredOrganic(%s);', $tracker, $keyword);
    }
    foreach ($this->getIgnoredReferers() as $referer)
    {
      $html[] = sprintf('%s._addIgnoredRef(%s);', $tracker, $referer);
    }
    
    if ($rate = $this->getSampleRate())
    {
      $html[] = sprintf('%s._setSampleRate(%d);', $tracker, $rate);
    }
    
    if ($this->getLocalRemoteServerPolicy())
    {
      $html[] = sprintf('%s._setLocalRemoteServerMode();', $tracker);
    }
    
    if ($before = $this->getBeforeTrackerJS())
    {
      $html[] = $before;
    }
    
    if ($pageName = $this->getPageName())
    {
      $html[] = sprintf('%s._trackPageview(%s);', $tracker, $this->escape($pageName));
    }
    else
    {
      $html[] = sprintf('%s._trackPageview();', $tracker);
    }

    if ($this->getTrackPageLoadTime())
    {
      $html[] = sprintf('%s._trackPageLoadTime();', $tracker);
    }

    foreach ($this->getVars() as $var)
    {
      $html[] = sprintf('%s._setVar(%s);', $tracker, $this->escape($var));
    }

    foreach ($this->getCustomVars() as $slot => $var)
    {
      if ($var[2]) { // with scope option
        $html[] = sprintf('%s._setCustomVar(%d, %s, %s, %d);', $tracker, $slot, $this->escape($var[0]), $this->escape($var[1]), $var[2]);
      }
      else {
        $html[] = sprintf('%s._setCustomVar(%d, %s, %s);', $tracker, $slot, $this->escape($var[0]), $this->escape($var[1]));
      }
    }


    if ($transaction = $this->getTransaction())
    {
      $values = array_map(array($this, 'escape'), $transaction->getValues());
      $html[] = sprintf('%s._addTrans(%s);', $tracker, join(',', $values));
      
      foreach ($transaction->getItems() as $item)
      {
        $values = array_map(array($this, 'escape'), $item->getValues());
        $html[] = sprintf('%s._addItem(%s);', $tracker, join(',', $values));
      }
      
      $html[] = sprintf('%s._trackTrans();', $tracker);
    }
    
    if ($after = $this->getAfterTrackerJS())
    {
      $html[] = $after;
    }
    
    $html[] = '//]]>';
    $html[] = '</script>';
    
    $html = join("\n", $html);
    $this->doInsert($response, $html, $this->insertion);
  }
  
  /**
   * @see sfGoogleAnalyticsTracker
   */
  public function forgePageViewFunction($path = null, $options = array())
  {
    $this->prepare($path, $options);
    
    if (isset($options['is_event']) && $options['is_event'])
    {
      $func = '%s._trackEvent(%s);';
    }
    else
    {
      $func = '%s._trackPageview(%s);';
    }
    
    return sprintf($func, $this->getTrackerVar(), $this->escape($path));
  }
  
  /**
   * @see sfGoogleAnalyticsTracker
   */
  public function forgeLinkerFunction($url, $options = array())
  {
    return sprintf('%s._link(%s);', $this->getTrackerVar(), $this->escape($url));
  }
  
  /**
   * @see sfGoogleAnalyticsTracker
   */
  public function forgePostLinkerFunction($formElement = 'this')
  {
    return sprintf('%s._linkByPost(%s);', $this->getTrackerVar(), $formElement);
  }
  
}
