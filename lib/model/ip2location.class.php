<?php

/**
 * ip2location
 * 
 * @package    m14tIp2Location
 * @subpackage ip2location
 * @author     Matt Farmer <work@mattfarmer.net>
 * @version    
 */
class ip2location {
  private $data = array();
  
  function __construct($ip) {
    //-- If ip = 127.0.0.1, then use the server's IP
    if ( '127.0.0.1' == $ip ) {
      $ip = $this->getIp();
    }
    
    $this->ip = $ip;
    
    $this->fetchInfo($ip);
  }
  
  public function __set($name, $value) {
      $this->data[$name] = $value;
  }

  public function __get($name) {
      if (array_key_exists($name, $this->data)) {
          return $this->data[$name];
      }
      return null;
  }
  
  private function fetchInfo($ip, $format = "json") {
    $api_key = sfConfig::get('app_ipinfodb_api_key');
    if ( !$api_key ) {
      throw new Exception("Missing sfConfig::get('app_ipinfodb_api_key') variable!");
    }
    $url = "http://api.ipinfodb.com/v2/ip_query.php?key=". $api_key ."&ip=". $ip ."&output=". $format ."&timezone=true";
    
    $session = curl_init($url);
    curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($session);
    
    switch ( $format ){
      case 'json':
        $phpObj =  json_decode($response);
        foreach ( $phpObj as $k=>$v ) {
          $this->$k = $v;
        }
        return;
        break;
      default:
        return $response;
      break;
    } 
  }
  
  private function getIp() {
    $url = "http://jsonip.appspot.com/";
    $session = curl_init($url);
    curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($session);
    $phpObj =  json_decode($response);
    return $phpObj->ip;
  }
}