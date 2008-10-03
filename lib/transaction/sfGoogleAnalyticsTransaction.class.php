<?php

/**
 * A transaction to track.
 * 
 * @package     sfGoogleAnalyticsPlugin
 * @subpackage  transaction
 * @author      Kris Wallsmith <kris.wallsmith@symfony-project.com>
 * @version     SVN: $Id$
 */
class sfGoogleAnalyticsTransaction
{
  protected
    $orderId    = null,
    $storeName  = null,
    $total      = null,
    $tax        = null,
    $shipping   = null,
    $city       = null,
    $state      = null,
    $country    = null,
    $items      = array();
  
  public function getValues()
  {
    $values = array(
      $this->getOrderId(),
      $this->getStoreName(),
      $this->getTotal(),
      $this->getTax(),
      $this->getShipping(),
      $this->getCity(),
      $this->getState(),
      $this->getCountry(),
    );
    
    return $values;
  }
  
  public function addItem(sfGoogleAnalyticsItem $item)
  {
    $this->items[] = $item;
  }
  
  public function getItems()
  {
    return $this->items;
  }
  
  public function setOrderId($orderId)
  {
    $this->orderId = $orderId;
  }
  
  public function getOrderId()
  {
    return $this->orderId;
  }
  
  public function setStoreName($name)
  {
    $this->storeName = $name;
  }
  
  public function getStoreName()
  {
    return $this->storeName;
  }
  
  public function setTotal($total)
  {
    $this->total = $total;
  }
  
  public function getTotal()
  {
    return $this->total;
  }
  
  public function setTax($tax)
  {
    $this->tax = $tax;
  }
  
  public function getTax()
  {
    return $this->tax;
  }
  
  public function setShipping($shipping)
  {
    $this->shipping = $shipping;
  }
  
  public function getShipping()
  {
    return $this->shipping;
  }
  
  public function setCity($city)
  {
    $this->city = $city;
  }
  
  public function getCity()
  {
    return $this->city;
  }
  
  public function setState($state)
  {
    $this->state = $state;
  }
  
  public function getState()
  {
    return $this->state;
  }
  
  public function setCountry($country)
  {
    $this->country = $country;
  }
  
  public function getCountry()
  {
    return $this->country;
  }
  
}
