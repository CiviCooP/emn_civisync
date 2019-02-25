<?php
/**
 * Copyright (c) 2019.  Klaas Eikelboom (klaas.eikelboom@civicoop.org)
 */

namespace Drupal\emn_civisync;

/**
 * Class CiviCRMService.
 */
class CiviCRMService {

  /**
   * @var
   */
  var $config;
  var $entityQuery;

  /**
   * Constructs a new CiviCRMService object.
   */
  public function __construct($factory) {
    $this->config = $factory->get('emn_civisync.civisyncconfig');
    $this->entityQuery = $factory->get('entity.query');
  }

  public function rest($action){
    $url= $this->config->get('url');
    $key = $this->config->get('key');
    $api_key = $this->config->get('api_key');
    $service_url = "{$url}/sites/all/modules/civicrm/extern/rest.php?json=1&key={$key}&api_key={$api_key}&entity=EmnMember&action=$action";
    $curl = curl_init($service_url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST,true);
    $result = curl_exec($curl);
    if(curl_errno($curl)){
      return ['is_error' => 1,
        'code' => curl_errno($curl),
        'error_message' => curl_strerror(curl_errno($curl)),
      ];
    }
    return json_decode($result,true);
  }

  public function check(){
    $result = $this->rest('ping');
    if($result['is_error']){
      return 'ERROR: '.$result['error_message'];
    } else {
      return 'OK';
    }
  }

}
