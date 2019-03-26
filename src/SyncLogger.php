<?php
/**
 * Copyright (c) 2019.  Klaas Eikelboom (klaas.eikelboom@civicoop.org)
 */

namespace Drupal\emn_civisync;

class SyncLogger {

  private static $instance = null;
  private $messages = [];

  public function log($message){
    $this->messages[]=$message;
  }

  public function clear(){
    $this->messages = [];
  }

  public function messages(){
    return $this->messages;
  }

  public static function getInstance()
  {
    if (self::$instance == null)
    {
      self::$instance = new SyncLogger();
    }

    return self::$instance;
  }

}