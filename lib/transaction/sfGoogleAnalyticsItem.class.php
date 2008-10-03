<?php

/**
 * A transaction item.
 * 
 * @package     sfGoogleAnalyticsPlugin
 * @subpackage  transaction
 * @author      Kris Wallsmith <kris.wallsmith@symfony-project.com>
 * @version     SVN: $Id$
 */
class sfGoogleAnalyticsItem
{
  protected
    $orderId      = null,
    $sku          = null,
    $productName  = null,
    $category     = null,
    $unitPrice    = null,
    $quantity     = null;
  
  public function getValues()
  {
    $values = array(
      $this->getOrderId(),
      $this->getSku(),
      $this->getProductName(),
      $this->getCategory(),
      $this->getUnitPrice(),
      $this->getQuantity(),
    );
    
    return $values;
  }
  
  public function setOrderId($orderId)
  {
    $this->orderId = $orderId;
  }
  
  public function getOrderId()
  {
    return $this->orderId;
  }
  
  public function setSku($sku)
  {
    $this->sku = $sku;
  }
  
  public function getSku()
  {
    return $this->sku;
  }
  
  public function setProductName($name)
  {
    $this->productName = $name;
  }
  
  public function getProductName()
  {
    return $this->productName;
  }
  
  public function setCategory($category)
  {
    $this->category = $category;
  }
  
  public function getCategory()
  {
    return $this->category;
  }
  
  public function setUnitPrice($price)
  {
    $this->unitPrice = $price;
  }
  
  public function getUnitPrice()
  {
    return $this->unitPrice;
  }
  
  public function setQuantity($quantity)
  {
    $this->quantity = $quantity;
  }
  
  public function getQuantity()
  {
    return $this->quantity;
  }
  
}
