<?php

/**
 * Google Analytics ga.js tracker.
 * 
 * @package     sfGoogleAnalyticsPlugin
 * @subpackage  tracker
 * @author      Tobias Sjösten <tobias.sjosten@gmail.com>
 * @version     SVN: $Id$
 */
class sfGoogleAnalyticsTrackerAsynchronous extends sfGoogleAnalyticsTracker
{
  protected $trackerVar = '_gaq';

  public function configure($params)
  {
    parent::configure($params);

    $params = array_merge(array('tracker_var' => null), $params);

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
    $html[] = sprintf('var %s = %s || [];', $tracker, $tracker);

    $html[] = sprintf(
      '%s.push(["_setAccount", %s]);',
      $tracker,
      $this->escape($this->getProfileId())
    );

    if ($domainName = $this->getDomainName())
    {
      $html[] = sprintf(
        '%s.push(["_setDomainName", "%s"]);',
        $tracker,
        $domainName
      );
    }

    if ($this->getLinkerPolicy())
    {
      $html[] = sprintf('%s.push(["_setAllowLinker", true]);', $tracker);
    }

    foreach ($this->getOrganicReferers() as $i => $referer)
    {
      list($name, $param) = $referer;
      
      $html[] = sprintf(
        '%s.push(["_addOrganic", %s, %s]);',
        $tracker,
        $this->escape($name),
        $this->escape($param)
      );
    }

    if ($cookiePath = $this->getCookiePath())
    {
      $html[] = sprintf(
        '%s.push(["_setCookiePath", %s]);',
        $tracker,
        $this->escape($cookiePath)
      );
    }

    // data collection
    if (!$this->getClientInfoPolicy())
    {
      $html[] = sprintf('%s.push(["_setClientInfo", false]);', $tracker);
    }
    if (!$this->getHashPolicy())
    {
      $html[] = sprintf('%s.push(["_setAllowHash", false]);', $tracker);
    }
    if (!$this->getDetectFlashPolicy())
    {
      $html[] = sprintf('%s.push(["_setDetectFlash", false]);', $tracker);
    }
    if (!$this->getDetectTitlePolicy())
    {
      $html[] = sprintf('%s.push(["_setDetectTitle", false]);', $tracker);
    }

    if ($timeout = $this->getSessionTimeout())
    {
      $html[] = sprintf(
        '%s.push(["_setSessionTimeout", %d]);',
        $tracker,
        $timeout
      );
    }

    if ($timeout = $this->getCookieTimeout())
    {
      $html[] = sprintf(
        '%s.push(["_setCookieTimeout", %d]);',
        $tracker,
        $timeout
      );
    }

    // campaign parameters
    if ($nameKey = $this->getCampaignNameKey())
    {
      $html[] = sprintf(
        '%s.push(["_setCampNameKey", "%s"]);',
        $tracker,
        $nameKey
      );
    }
    if ($mediumKey = $this->getCampaignMediumKey())
    {
      $html[] = sprintf(
        '%s.push(["_setCampMediumKey", "%s"]);',
        $tracker,
        $mediumKey
      );
    }
    if ($sourceKey = $this->getCampaignSourceKey())
    {
      $html[] = sprintf(
        '%s.push(["_setCampSourceKey", "%s"]);',
        $tracker,
        $sourceKey
      );
    }
    if ($termKey = $this->getCampaignTermKey())
    {
      $html[] = sprintf(
        '%s.push(["_setCampTermKey", "%s"]);',
        $tracker,
        $termKey
      );
    }
    if ($contentKey = $this->getCampaignContentKey())
    {
      $html[] = sprintf(
        '%s.push(["_setCampContentKey", "%s"]);',
        $tracker,
        $contentKey
      );
    }
    if ($idKey = $this->getCampaignIdKey())
    {
      $html[] = sprintf(
        '%s.push(["_setCampIdKey", "%s"]);',
        $tracker,
        $idKey
      );
    }
    if ($noOverrideKey = $this->getCampaignNoOverrideKey())
    {
      $html[] = sprintf(
        '%s.push(["_setCampNOKey", "%s"]);',
        $tracker,
        $noOverrideKey
      );
    }

    if ($this->getAnchorPolicy())
    {
      $html[] = sprintf('%s.push(["_setAllowAnchor", true]);', $tracker);
    }

    foreach ($this->getIgnoredOrganics() as $keyword)
    {
      $html[] = sprintf(
        '%s.push(["_addIgnoredOrganic", "%s"]);',
        $tracker,
        $keyword
      );
    }
    foreach ($this->getIgnoredReferers() as $referer)
    {
      $html[] = sprintf(
        '%s.push(["_addIgnoredRef", "%s"]);',
        $tracker,
        $referer
      );
    }

    if ($rate = $this->getSampleRate())
    {
      $html[] = sprintf('%s.push(["_setSampleRate", %d]);', $tracker, $rate);
    }

    if ($this->getLocalRemoteServerPolicy())
    {
      $html[] = sprintf('%s.push(["_setLocalRemoteServerMode"]);', $tracker);
    }

    if ($before = $this->getBeforeTrackerJS())
    {
      $html[] = $before;
    }

    if ($pageName = $this->getPageName())
    {
      $html[] = sprintf(
        '%s.push(["_trackPageview", %s]);',
        $tracker,
        $this->escape($pageName)
      );
    }
    else
    {
      $html[] = sprintf('%s.push(["_trackPageview"]);', $tracker);
    }

    if ($this->getTrackPageLoadTime())
    {
      $html[] = sprintf('%s.push(["_trackPageLoadTime"]);', $tracker);
    }

    foreach ($this->getVars() as $var)
    {
      $html[] = sprintf(
        '%s.push(["_setVar", %s]);',
        $tracker,
        $this->escape($var)
      );
    }

    foreach ($this->getCustomVars() as $slot => $var)
    {
      if ($var[2]) { // with scope
        $html[] = sprintf(
          '%s.push(["_setCustomVar", %d, %s, %s, %d]);',
          $tracker,
          $slot,
          $this->escape($var[0]), // name
          $this->escape($var[1]), // value
          $var[2] // scope
        );
      }
      else {
        $html[] = sprintf(
          '%s.push(["_setCustomVar", %d, %s, %s]);',
          $tracker,
          $slot,
          $this->escape($var[0]), // name
          $this->escape($var[1]) // value
        );
      }
    }

    if ($transaction = $this->getTransaction())
    {
      $values = array_map(array($this, "escape"), $transaction->getValues());
      $html[] = sprintf(
        '%s.push(["_addTrans", %s]);',
        $tracker,
        join(",", $values)
      );

      foreach ($transaction->getItems() as $item)
      {
        $values = array_map(array($this, "escape"), $item->getValues());
        $html[] = sprintf(
          '%s.push(["_addItem", %s]);',
          $tracker,
          join(',', $values)
        );
      }

      $html[] = sprintf('%s.push(["_trackTrans"]);', $tracker);
    }

    if ($after = $this->getAfterTrackerJS())
    {
      $html[] = $after;
    }

    $html[] = "(function() {";
    $html[] = 'var ga = document.createElement("script"); ga.type = "text/javascript"; ga.async = true;';
    $html[] = 'ga.src = ("https:" == document.location.protocol ? "https://ssl" : "http://www") + ".google-analytics.com/ga.js";';
    $html[] = 'var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(ga, s);';
    $html[] = "})();";

    $html[] = '//]]>';
    $html[] = '</script>';

    $html = implode("\n", $html);
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
      $func = "%s.push(['_trackEvent', %s]);";
    }
    else
    {
      $func = "%s.push(['_trackPageview', %s]);";
    }

    return sprintf($func, $this->getTrackerVar(), $this->escape($path));
  }

  /**
   * @see sfGoogleAnalyticsTracker
   */
  public function forgeLinkerFunction($url, $options = array())
  {
    return sprintf("%s.push(['_link', %s]);", $this->getTrackerVar(), $this->escape($url));
  }

  /**
   * @see sfGoogleAnalyticsTracker
   */
  public function forgePostLinkerFunction($formElement = 'this')
  {
    return sprintf("%s.push(['_linkByPost', '%s']);", $this->getTrackerVar(), $formElement);
  }
}
