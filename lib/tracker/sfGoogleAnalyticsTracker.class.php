<?php

/**
 * Houses the core API for manipulating the Google Analytics tracking code.
 * 
 * @package     sfGoogleAnalyticsPlugin
 * @subpackage  tracker
 * @author      Kris Wallsmith <kris [dot] wallsmith [at] gmail [dot] com>
 * @version     SVN: $Id$
 */
abstract class sfGoogleAnalyticsTracker
{
  const
    POSITION_TOP              = 'top',
    POSITION_BOTTOM           = 'bottom';
  
  protected
    $context                  = null,
    $parameterHolder          = null,
    $beforeTrackerJS          = null,
    $afterTrackerJS           = null,
    
    $enabled                  = false,
    $profileId                = null,
    $insertion                = null,
    $domainName               = null,
    $pageName                 = null,
    $linkerPolicy             = false,
    $localRemoteServerPolicy  = false,
    $anchorPolicy             = false,
    $clientInfoPolicy         = true,
    $hashPolicy               = true,
    $detectFlashPolicy        = true,
    $detectTitlePolicy        = true,
    $organicReferers          = array(),
    $ignoredOrganics          = array(),
    $ignoredReferers          = array(),
    $campaignNameKey          = null,
    $campaignSourceKey        = null,
    $campaignMediumKey        = null,
    $campaignTermKey          = null,
    $campaignContentKey       = null,
    $campaignIdKey            = null,
    $campaignNoOverrideKey    = null,
    $sampleRate               = null,
    $sessionTimeout           = null,
    $cookieTimeout            = null,
    $cookiePath               = null,
    $vars                     = array(),
    $transaction              = null;
  
  public function __construct($context, $parameters = array())
  {
    $this->initialize($context, $parameters);
  }
  
  public function initialize($context, $parameters = array())
  {
    $this->context = $context;
    
    $this->parameterHolder = class_exists('sfNamespacedParameterHolder') ? new sfNamespacedParameterHolder : new sfParameterHolder;
    $this->parameterHolder->add($parameters);
    
    // apply configuration from app.yml
    $prefix = 'app_sf_google_analytics_plugin_';
    
    $params = sfConfig::get($prefix.'params', array());
    $params['enabled']    = sfConfig::get($prefix.'enabled');
    $params['profile_id'] = sfConfig::get($prefix.'profile_id');
    $params['insertion']  = sfConfig::get($prefix.'insertion');
    
    $this->configure($params);
    
    return true;
  }
  
  /**
   * Apply non-null configuration values.
   * 
   * @param   array $params
   */
  public function configure($params)
  {
    $params = array_merge(array(
      'enabled'                     => null,
      'insertion'                   => null,
      'profile_id'                  => null,
      'page_name'                   => null,
      'domain_name'                 => null,
      'linker_policy'               => null,
      'organic_referers'            => null,
      'vars'                        => null,
      'cookie_path'                 => null,
      'client_info_policy'          => null,
      'hash_policy'                 => null,
      'detect_flash_policy'         => null,
      'detect_title_policy'         => null,
      'session_timeout'             => null,
      'cookie_timeout'              => null,
      'campaign_keys'               => null,
      'anchor_policy'               => null,
      'ignored_organics'            => null,
      'ignored_referers'            => null,
      'sample_rate'                 => null,
      'local_remote_server_policy'  => null), $params);
    
    if (!is_null($params['enabled']))
    {
      $this->setEnabled($params['enabled']);
    }
    
    if (!is_null($params['profile_id']))
    {
      $this->setProfileId($params['profile_id']);
    }
    
    if (!is_null($params['page_name']))
    {
      $this->setPageName($params['page_name']);
    }
    
    if (!is_null($params['insertion']))
    {
      $this->setInsertion($params['insertion']);
    }
    
    if (!is_null($params['domain_name']))
    {
      $this->setDomainName($params['domain_name']);
    }
    
    if (!is_null($params['linker_policy']))
    {
      $this->setLinkerPolicy($params['linker_policy']);
    }
    
    if (!is_null($params['local_remote_server_policy']))
    {
      $this->setLocalRemoteServerPolicy($params['local_remote_server_policy']);
    }
    
    if (!is_null($params['anchor_policy']))
    {
      $this->setAnchorPolicy($params['anchor_policy']);
    }
    
    if (!is_null($params['client_info_policy']))
    {
      $this->setClientInfoPolicy($params['client_info_policy']);
    }
    
    if (!is_null($params['hash_policy']))
    {
      $this->setHashPolicy($params['hash_policy']);
    }
    
    if (!is_null($params['detect_flash_policy']))
    {
      $this->setDetectFlashPolicy($params['detect_flash_policy']);
    }
    
    if (!is_null($params['detect_title_policy']))
    {
      $this->setDetectTitlePolicy($params['detect_title_policy']);
    }
    
    if (!is_null($params['organic_referers']))
    {
      foreach ($params['organic_referers'] as $referer)
      {
        is_int(key($referer)) ?
          $this->addOrganicReferer($referer[0], $referer[1]) :
          $this->addOrganicReferer($referer['name'], $referer['param']);
      }
    }
    
    if (!is_null($params['ignored_organics']))
    {
      foreach ($params['ignored_organics'] as $keyword)
      {
        $this->addIgnoredOrganic($keyword);
      }
    }
    
    if (!is_null($params['ignored_referers']))
    {
      foreach ($params['ignored_referers'] as $referer)
      {
        $this->addIgnoredReferer($referer);
      }
    }
    
    if (!is_null($params['campaign_keys']))
    {
      foreach ($params['campaign_keys'] as $key => $value)
      {
        $method = 'setCampaign'.sfInflector::camelize($key).'Key';
        $this->$method($value);
      }
    }
    
    if (!is_null($params['sample_rate']))
    {
      $this->setSampleRate($params['sample_rate']);
    }
    
    if (!is_null($params['session_timeout']))
    {
      $this->setSessionTimeout($params['session_timeout']);
    }
    
    if (!is_null($params['cookie_timeout']))
    {
      $this->setCookieTimeout($params['cookie_timeout']);
    }
    
    if (!is_null($params['cookie_path']))
    {
      $this->setCookiePath($params['cookie_path']);
    }
    
    if (!is_null($params['vars']))
    {
      foreach ($params['vars'] as $var)
      {
        $this->setVar($var);
      }
    }
  }
  
  public function getContext()
  {
    return $this->context;
  }
  
  public function getParameterHolder()
  {
    return $this->parameterHolder;
  }
  
  public function getParameter($name, $default = null, $ns = null)
  {
    return $this->parameterHolder->get($name, $default, $ns);
  }
  
  public function hasParameter($name, $ns = null)
  {
    return $this->parameterHolder->has($name, $ns);
  }
  
  public function setParameter($name, $value, $ns = null)
  {
    return $this->parameterHolder->set($name, $value, $ns);
  }
  
  /**
   * Add JS to include immediately before the tracker function is called.
   * 
   * @param   string $js
   * @param   array $options
   */
  public function setBeforeTrackerJS($js, $options = array())
  {
    if ($this->prepare($js, $options))
    {
      $this->beforeTrackerJS = $js;
    }
  }
  
  public function getBeforeTrackerJS()
  {
    return $this->beforeTrackerJS;
  }
  
  /**
   * Add JS to include at the bottom of the tracker code.
   * 
   * @param   string $js
   * @param   array $options
   */
  public function setAfterTrackerJS($js, $options = array())
  {
    if ($this->prepare($js, $options))
    {
      $this->afterTrackerJS = $js;
    }
  }
  
  public function getAfterTrackerJS()
  {
    return $this->afterTrackerJS;
  }
  
  /**
   * Toggle tracker's enabled state.
   * 
   * @param   bool $enabled
   */
  public function setEnabled($enabled)
  {
    $this->enabled = (bool) $enabled;
  }
  
  public function isEnabled()
  {
    return $this->enabled;
  }
  
  /**
   * Set the profile ID to use for this tracker.
   * 
   * @param   string $profileId
   */
  public function setProfileId($profileId)
  {
    $this->profileId = $profileId;
  }
  
  public function getProfileId()
  {
    return $this->profileId;
  }
  
  /**
   * Set where the tracking code should be inserted into the response.
   * 
   * @param   string $insertion
   * @param   array $options
   */
  public function setInsertion($insertion, $options = array())
  {
    if ($this->prepare($insertion, $options))
    {
      $this->insertion = $insertion;
    }
  }
  
  public function getInsertion()
  {
    return $this->insertion;
  }
  
  /**
   * Define a page other than what's in the address bar.
   * 
   * @param   string $pageName
   * @param   array $options
   */
  public function setPageName($pageName, $options = array())
  {
    if ($this->prepare($pageName, $options))
    {
      $this->pageName = $pageName;
    }
  }
  
  public function getPageName()
  {
    return $this->pageName;
  }
  
  /**
   * Set the domain to track this website as.
   * 
   * @param   string $domainName
   */
  public function setDomainName($domainName)
  {
    if ($domainName === false)
    {
      $domainName = 'none';
    }
    
    $this->domainName = $domainName;
  }
  
  public function getDomainName()
  {
    return $this->domainName;
  }
  
  /**
   * Define a linker policy.
   * 
   * @param   bool $enabled
   */
  public function setLinkerPolicy($enabled)
  {
    $this->linkerPolicy = (bool) $enabled;
  }
  
  public function getLinkerPolicy()
  {
    return $this->linkerPolicy;
  }
  
  /**
   * Set a transaction to track in the response.
   * 
   * @param   sfGoogleAnalyticsTransaction $transaction
   * @param   array $options
   */
  public function setTransaction(sfGoogleAnalyticsTransaction $transaction, $options = array())
  {
    if ($this->prepare($transaction, $options))
    {
      $this->transaction = $transaction;
    }
  }
  
  public function getTransaction()
  {
    return $this->transaction;
  }
  
  /**
   * Add an organic referer.
   * 
   * @param   string $name
   * @param   string $param
   */
  public function addOrganicReferer($name, $param)
  {
    $this->organicReferers[] = array($name, $param);
  }
  
  public function getOrganicReferers()
  {
    return $this->organicReferers;
  }
  
  /**
   * Add a custom tracking variable to this cookie.
   * 
   * @param   string $var
   * @param   array $options
   */
  public function setVar($var, $options = array())
  {
    if ($this->prepare($var, $options))
    {
      $this->vars[] = $var;
    }
  }
  
  public function getVars()
  {
    return $this->vars;
  }
  
  /**
   * Set a path to limit the tracking cookie to.
   * 
   * @param   string $path
   * @param   array $options
   */
  public function setCookiePath($path, $options = array())
  {
    if ($this->prepare($path, $options))
    {
      $this->cookiePath = $path;
    }
  }
  
  public function getCookiePath()
  {
    return $this->cookiePath;
  }
  
  /**
   * Define a client info detection policy.
   * 
   * @param   bool $enabled
   */
  public function setClientInfoPolicy($enabled)
  {
    $this->clientInfoPolicy = (bool) $enabled;
  }
  
  public function getClientInfoPolicy()
  {
    return $this->clientInfoPolicy;
  }
  
  /**
   * Define a hash policy.
   * 
   * @param   bool $enabled
   */
  public function setHashPolicy($enabled)
  {
    $this->hashPolicy = (bool) $enabled;
  }
  
  public function getHashPolicy()
  {
    return $this->hashPolicy;
  }
  
  /**
   * Define a flash detection policy.
   * 
   * @param   bool $enabled
   */
  public function setDetectFlashPolicy($enabled)
  {
    $this->detectFlashPolicy = (bool) $enabled;
  }
  
  public function getDetectFlashPolicy()
  {
    return $this->detectFlashPolicy;
  }
  
  /**
   * Define a title detection policy.
   * 
   * @param   bool $enabled
   */
  public function setDetectTitlePolicy($enabled)
  {
    $this->detectTitlePolicy = $enabled;
  }
  
  public function getDetectTitlePolicy()
  {
    return $this->detectTitlePolicy;
  }
  
  /**
   * Set a session timeout.
   * 
   * @param   int $seconds
   */
  public function setSessionTimeout($seconds)
  {
    $this->sessionTimeout = (int) $seconds;
  }
  
  public function getSessionTimeout()
  {
    return $this->sessionTimeout;
  }
  
  /**
   * Set a cookie timeout.
   * 
   * @param   int $seconds
   */
  public function setCookieTimeout($seconds)
  {
    $this->cookieTimeout = (int) $seconds;
  }
  
  public function getCookieTimeout()
  {
    return $this->cookieTimeout;
  }
  
  /**
   * Set a campaign name parameter key.
   * 
   * @param   string $key
   */
  public function setCampaignNameKey($key)
  {
    $this->campaignNameKey = $key;
  }
  
  public function getCampaignNameKey()
  {
    return $this->campaignNameKey;
  }
  
  /**
   * Set a campaign source parameter key.
   * 
   * @param   string $key
   */
  public function setCampaignSourceKey($key)
  {
    $this->campaignSourceKey = $key;
  }
  
  public function getCampaignSourceKey()
  {
    return $this->campaignSourceKey;
  }
  
  /**
   * Set a campaign medium parameter key.
   * 
   * @param   string $key
   */
  public function setCampaignMediumKey($key)
  {
    $this->campaignMediumKey = $key;
  }
  
  public function getCampaignMediumKey()
  {
    return $this->campaignMediumKey;
  }
  
  /**
   * Set a campaign term parameter key.
   * 
   * @param   string $key
   */
  public function setCampaignTermKey($key)
  {
    $this->campaignTermKey = $key;
  }
  
  public function getCampaignTermKey()
  {
    return $this->campaignTermKey;
  }
  
  /**
   * Set a campaign content parameter key.
   * 
   * @param   string $key
   */
  public function setCampaignContentKey($key)
  {
    $this->campaignContentKey = $key;
  }
  
  public function getCampaignContentKey()
  {
    return $this->campaignContentKey;
  }
  
  /**
   * Set a campaign ID parameter key.
   * 
   * @param   string $key
   */
  public function setCampaignIdKey($key)
  {
    $this->campaignIdKey = $key;
  }
  
  public function getCampaignIdKey()
  {
    return $this->campaignIdKey;
  }
  
  /**
   * Set a campaign no override parameter key.
   * 
   * @param   string $key
   */
  public function setCampaignNoOverrideKey($key)
  {
    $this->campaignNoOverrideKey = $key;
  }
  
  public function getCampaignNoOverrideKey()
  {
    return $this->campaignNoOverrideKey;
  }
  
  /**
   * Define an anchor policy.
   * 
   * @param   bool $enabled
   */
  public function setAnchorPolicy($enabled)
  {
    $this->anchorPolicy = (bool) $enabled;
  }
  
  public function getAnchorPolicy()
  {
    return $this->anchorPolicy;
  }
  
  /**
   * Add an ignored orgnic keyword.
   * 
   * @param   string $keyword
   */
  public function addIgnoredOrganic($keyword)
  {
    $this->ignoredOrganics[] = $keyword;
  }
  
  public function getIgnoredOrganics()
  {
    return $this->ignoredOrganics;
  }
  
  /**
   * Add an ignored referer.
   * 
   * @param   string $referer
   */
  public function addIgnoredReferer($referer)
  {
    $this->ignoredReferers[] = $referer;
  }
  
  public function getIgnoredReferers()
  {
    return $this->ignoredReferers;
  }
  
  /**
   * Set a sample rate.
   * 
   * @param   int $rate
   */
  public function setSampleRate($rate)
  {
    $this->sampleRate = (int) $rate;
  }
  
  public function getSampleRate()
  {
    return $this->sampleRate;
  }
  
  /**
   * Define a local/remove server policy.
   * 
   * @param   bool $enabled
   */
  public function setLocalRemoteServerPolicy($enabled)
  {
    $this->localRemoteServerPolicy = (bool) $enabled;
  }
  
  public function getLocalRemoteServerPolicy()
  {
    return $this->localRemoteServerPolicy;
  }
  
  /**
   * Extract options used by tracker's helper functions.
   * 
   * View options include:
   * 
   *  * track_as
   *  * is_route
   *  * is_event
   *  * use_linker
   * 
   * @param   array $options
   * 
   * @return  array
   */
  public function extractViewOptions(& $options)
  {
    $viewOptions = array();
    
    foreach (array('track_as', 'is_route', 'is_event', 'use_linker') as $option)
    {
      if (isset($options[$option]))
      {
        $viewOptions[$option] = $options[$option];
        unset($options[$option]);
      }
    }
    
    return $viewOptions;
  }
  
  /**
   * Forge a call to the Javascript page view function.
   * 
   * @param   string $path
   * @param   array $options
   * 
   * @return  string
   */
  abstract public function forgePageViewFunction($path = null, $options = array());
  
  /**
   * Forge a call to the Javascript linker function.
   * 
   * @param   string $path
   * 
   * @return  string
   */
  abstract public function forgeLinkerFunction($url);
  
  /**
   * Forge a call to the Javascript POST linker function.
   * 
   * @param   string $formElement
   * 
   * @return  string
   */
  abstract public function forgePostLinkerFunction($formElement = 'this');
  
  /**
   * Insert tracking code into a response.
   * 
   * @param   sfResponse $response
   */
  abstract public function insert(sfResponse $response);
  
  /**
   * Insert content into a response.
   * 
   * @param   sfResponse $response
   * @param   string $content
   * @param   string $position
   */
  protected function doInsert(sfResponse $response, $content, $position = null)
  {
    if ($position == null)
    {
      $position = self::POSITION_BOTTOM;
    }
    
    // check for overload
    $method = 'doInsert'.$position;
    
    if (method_exists($this, $method))
    {
      call_user_func(array($this, $method), $response, $content);
    }
    else
    {
      $old = $response->getContent();
      
      switch ($position)
      {
        case self::POSITION_TOP:
        $new = preg_replace('/<body[^>]*>/i', "$0\n".$content."\n", $old, 1);
        break;
        
        case self::POSITION_BOTTOM:
        $new = str_ireplace('</body>', "\n".$content."\n</body>", $old);
        break;
      }
      
      if ($old == $new)
      {
        $new .= $content;
      }
      
      $response->setContent($new);
    }
  }
  
  /**
   * Apply common options to a value.
   * 
   * @param   mixed $value
   * @param   mixed $options
   * 
   * @return  bool  whether to continue execution
   */
  protected function prepare(& $value, & $options = array())
  {
    if (is_string($options))
    {
      $options = sfToolkit::stringToArray($options);
    }
    
    if (isset($options['use_flash']) && $options['use_flash'])
    {
      unset($options['use_flash']);
      
      $trace = debug_backtrace();
      
      $caller = $trace[1];
      $this->plant($caller['function'], array($value, $options));
      
      return false;
    }
    else
    {
      if (is_string($value) && isset($options['is_route']) && $options['is_route'])
      {
        $value = $this->context->getController()->genUrl($value);
        unset($options['is_route']);
      }
      
      return true;
    }
  }
  
  /**
   * Plant a callable to be executed against the next request's tracker.
   * 
   * @param   string $method
   * @param   array $arguments
   */
  protected function plant($method, $arguments = array())
  {
    if (sfConfig::get('sf_logging_enabled'))
    {
      $this->context->getLogger()->info(sprintf('{%s} Storing call to %s method for next response.', __CLASS__, $method));
    }
    
    $callables = $this->parameterHolder->getAll('flash', array());
    $callables[] = array($method, $arguments);
    
    $this->parameterHolder->removeNamespace('flash');
    $this->parameterHolder->add($callables, 'flash');
  }
  
  /**
   * Escape the provided value for Javascript evaluation.
   * 
   * @param   string $value
   * 
   * @return  string
   */
  protected function escape($value)
  {
    if (function_exists('json_encode'))
    {
      $escaped = json_encode($value);
    }
    else
    {
      sfLoader::loadHelpers(array('Escaping'));
      $escaped = '"'.esc_js($value).'"';
    }
    
    return $escaped;
  }
  
  /**
   * Update storage with callables for the next tracker.
   * 
   * @param   sfUser $user
   */
  public function shutdown($user)
  {
    if (sfConfig::get('sf_logging_enabled'))
    {
      $this->context->getLogger()->info(sprintf('{%s} Copying callables to session storage.', __CLASS__));
    }
    
    $user->getAttributeHolder()->set('callables', $this->parameterHolder->getAll('flash', array()), 'sf_google_analytics_plugin');
  }
  
}
