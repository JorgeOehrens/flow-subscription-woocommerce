<?php

  /*
  Plugin name: Subscription Flow
  Plugin URI: https://www.subscriptionflow.com
  Description: Subscription Flow is a plugin that allows you to create a subscription flow for your website.
  Author: Jorge Oehrens
  Version: 1.0
  Author URI: https://www.subscriptionflow.com
  */
  add_action( 'plugins_loaded', 'cwoa_authorizenet_aim_init', 0 );
  function cwoa_authorizenet_aim_init() {
      //if condition use to do nothin while WooCommerce is not installed
    if ( ! class_exists( 'WC_Payment_Gateway' ) ) return;
    include_once( 'flow-authorize-woocommerce.php' );
    // class add it too WooCommerce
    add_filter( 'woocommerce_payment_gateways', 'cwoa_add_authorizenet_aim_gateway' );
    function cwoa_add_authorizenet_aim_gateway( $methods ) {
      $methods[] = 'cwoa_AuthorizeNet_AIM';
      return $methods;
    }
  }

  // Add custom action links
  add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'cwoa_authorizenet_aim_action_links' );
  function cwoa_authorizenet_aim_action_links( $links ) {
    $plugin_links = array(
      '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout' ) . '">' . __( 'Settings', 'cwoa-authorizenet-aim' ) . '</a>',
    );
    return array_merge( $plugin_links, $links );
  }

  add_action( 'woocommerce_thankyou', 'misha_poll_form', 4 );

  function misha_poll_form( $order_id ) {
    $urlEnviroment = 'https://sandbox.flow.cl/api';
    $token = $_POST["token"];

    //Print reponse
    $secretKey = "47ccf1f6240b920d483cc19876c2907146690367";

    $paramsPlans = array( 
      "apiKey" => "1F729C5B-7226-4127-AD5E-7E1C52AL6C56",
      "token" => $token,

    );
    $keys_plans = array_keys($paramsPlans);
    sort($keys_plans);
    $toSign = "";
    foreach($keys_plans as $key) {
      $toSign .= $key . $paramsPlans[$key];
    };
    $signature_plans = hash_hmac('sha256', $toSign , $secretKey);


    // Agrega a la url el servicio a consumir
    $urlPlans= $urlEnviroment . '/customer/getRegisterStatus';

    // agrega la firma a los parámetros
    $paramsPlans["s"] = $signature_plans;
    //Codifica los parámetros en formato URL y los agrega a la URL
    $urlPlans = $urlPlans . "?" . http_build_query($paramsPlans);



    try {
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $urlPlans);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
      $response = curl_exec($ch);
      if($response === false) {
        $error = curl_error($ch);
        throw new Exception($error, 1);
      } 
      $info = curl_getinfo($ch);
      $response = json_decode($response, true);

      $customerId= $response['customerId'];

      // echo $response;
      } catch (Exception $e) {
        echo 'Error: ' . $e->getCode() . ' - ' . $e->getMessage();
    }
    


    //Create Subscription
    $paramsSub = array (
      "apiKey" => "1F729C5B-7226-4127-AD5E-7E1C52AL6C56",
      "planId" => "plan 1",
      "customerId" => $customerId,

    );

    $keys_sub = array_keys($paramsSub);
    sort($keys_sub);
    $toSign = "";
    foreach($keys_sub as $key) {
      $toSign .= $key . $paramsSub[$key];
    };
    $signature_sub = hash_hmac('sha256', $toSign , $secretKey);

    // Agrega a la url el servicio a consumir
    $urlSub= $urlEnviroment . '/subscription/create';

    // agrega la firma a los parámetros
    $paramsSub["s"] = $signature_sub;
    //Codifica los parámetros en formato URL y los agrega a la URL
    $urlSub = $urlSub . "?" . http_build_query($paramsSub);

    try {
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $urlSub);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
      curl_setopt($ch, CURLOPT_POST, TRUE);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $paramsSub);
      $response = curl_exec($ch);
      if($response === false) {
        $error = curl_error($ch);
        throw new Exception($error, 1);
      } 
      $info = curl_getinfo($ch);
      if(!in_array($info['http_code'], array('200', '400', '401'))) {
        throw new Exception('Unexpected error occurred. HTTP_CODE: '.$info['http_code'] , $info['http_code']);
      }
      // echo $response;
    } catch (Exception $e) {
      echo 'Error: ' . $e->getCode() . ' - ' . $e->getMessage();
    }


    



  
  }
  
?>