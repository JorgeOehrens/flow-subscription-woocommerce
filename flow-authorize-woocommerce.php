<?php
class cwoa_AuthorizeNet_AIM extends WC_Payment_Gateway {

  function __construct() {
    // global ID
    
    $this->id = "cwoa_authorizenet_aim";
    // Show Title
    $this->method_title = __( "Flow suscripción", 'cwoa-authorizenet-aim' );
    // Show Description
    $this->method_description = __( "Flow suscripción Payment Gateway Plug-in for WooCommerce", 'cwoa-authorizenet-aim' );
    // vertical tab title
    $this->title = __( "Flow suscripción", 'cwoa-authorizenet-aim' );
    $this->icon = null;
    $this->has_fields = true;
    // support default form with credit card
    $this->supports = array( '' );

    // setting defines
    $this->init_form_fields();
    // load time variable setting
    $this->init_settings();
    
    // Turn these settings into variables we can use
    foreach ( $this->settings as $setting_key => $value ) {
      $this->$setting_key = $value;
    }
    
    // further check of SSL if you want
    
    // Save settings
    if ( is_admin() ) {
      add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
    }    
  } // Here is the  End __construct()
  // administration fields for specific Gateway
  public function init_form_fields() {
    $this->form_fields = array(
      'enabled' => array(
        'title'    => __( 'Activado / Desactivado', 'cwoa-authorizenet-aim' ),
        'label'    => __( 'Activa el pago con suscripción', 'cwoa-authorizenet-aim' ),
        'type'    => 'checkbox',
        'default'  => 'no',
      ),
      'title' => array(
        'title'    => __( 'Titulo', 'cwoa-authorizenet-aim' ),
        'type'    => 'text',
        'desc_tip'  => __( 'Payment title of checkout process.', 'cwoa-authorizenet-aim' ),
        'default'  => __( 'Credit card', 'cwoa-authorizenet-aim' ),
      ),
      'description' => array(
        'title'    => __( 'Descripción', 'cwoa-authorizenet-aim' ),
        'type'    => 'textarea',
        'desc_tip'  => __( 'Payment title of checkout process.', 'cwoa-authorizenet-aim' ),
        'default'  => __( 'Successfully payment through credit card.', 'cwoa-authorizenet-aim' ),
        'css'    => 'max-width:450px;'
      ),
      'api_login' => array(
        'title'    => __( 'Flow API Login', 'cwoa-authorizenet-aim' ),
        'type'    => 'text',
        'desc_tip'  => __( 'This is the API Login provided by Authorize.net when you signed up for an account.', 'cwoa-authorizenet-aim' ),
      ),
      'trans_key' => array(
        'title'    => __( 'Flow Api Key', 'cwoa-authorizenet-aim' ),
        'type'    => 'password',
        'desc_tip'  => __( 'This is the Transaction Key provided by Authorize.net when you signed up for an account.', 'cwoa-authorizenet-aim' ),
      ),
      'environment' => array(
        'title'    => __( 'Modo test', 'cwoa-authorizenet-aim' ),
        'label'    => __( 'Enable Test Mode', 'cwoa-authorizenet-aim' ),
        'type'    => 'checkbox',
        'description' => __( 'This is the test mode of gateway.', 'cwoa-authorizenet-aim' ),
        'default'  => 'no',
      )
    );    
  }
  
  // Response handled for payment gateway
  public function process_payment( $order_id ) {
    global $woocommerce;
    $customer_order = new WC_Order( $order_id );
    $first_name = $customer_order->billing_first_name;
    $last_name = $customer_order->billing_last_name;
    $complete_name = $first_name . ' ' . $last_name;
    $email = $customer_order->billing_email;



    $externalIdOrder= $customer_order->id;
    $externalId ='#' . $first_name . ' ' . $last_name . ' ' . $email . ' ' . $externalIdOrder;

    //Product information

    // $producto_id = $customer_order->get_items();
    // $producto_id = $producto_id[0]['product_id'];

    // $producto = wc_get_product($producto_id);
    // $producto = $producto->get_title();






    //POST create user
    $secretKey = "47ccf1f6240b920d483cc19876c2907146690367";

    $params = array( 
      "apiKey" => "1F729C5B-7226-4127-AD5E-7E1C52AL6C56",
      "name" => $complete_name,
      "email" => $email,
      "externalId" => $externalId,

      
    ); 
    $keys = array_keys($params);
    sort($keys);
    $toSign = "";
    foreach($keys as $key) {
      $toSign .= $key . $params[$key];
    };
    $signature = hash_hmac('sha256', $toSign , $secretKey);


    $urlEnviroment = 'https://sandbox.flow.cl/api';
    // Agrega a la url el servicio a consumir
    $url = $urlEnviroment . '/customer/create';
    // agrega la firma a los parámetros
    $params["s"] = $signature;
    //Codifica los parámetros en formato URL y los agrega a la URL
    $url = $url . "?" . http_build_query($params);

    try {
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
      curl_setopt($ch, CURLOPT_POST, TRUE);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
      $response = curl_exec($ch);
      if($response === false) {
        $error = curl_error($ch);
        throw new Exception($error, 1);
      } 
      $info = curl_getinfo($ch);
      if(!in_array($info['http_code'], array('200', '400', '401'))) {
        throw new Exception('Unexpected error occurred. HTTP_CODE: '.$info['http_code'] , $info['http_code']);
      }

      $response = json_decode($response, true);

      $customerId= $response['customerId'];
      echo $response;
    } catch (Exception $e) {
      echo 'Error: ' . $e->getCode() . ' - ' . $e->getMessage();
    }





    // GetPlansID


    $paramsPlans = array( 
      "apiKey" => "1F729C5B-7226-4127-AD5E-7E1C52AL6C56",
      "planId" => "plan 1",

    );
    $keys_plans = array_keys($paramsPlans);
    sort($keys_plans);
    $toSign = "";
    foreach($keys_plans as $key) {
      $toSign .= $key . $paramsPlans[$key];
    };
    $signature_plans = hash_hmac('sha256', $toSign , $secretKey);


    // Agrega a la url el servicio a consumir
    $urlPlans= $urlEnviroment . '/plans/get';

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
  
      echo $response;
      } catch (Exception $e) {
        echo 'Error: ' . $e->getCode() . ' - ' . $e->getMessage();
    }

    //POST Create Subscription

    // $paramsSub = array (
    //   "apiKey" => "1F729C5B-7226-4127-AD5E-7E1C52AL6C56",
    //   "planId" => "plan 1",
    //   "customerId" => $customerId,

    // );

    // $keys_sub = array_keys($paramsSub);
    // sort($keys_sub);
    // $toSign = "";
    // foreach($keys_sub as $key) {
    //   $toSign .= $key . $paramsSub[$key];
    // };
    // $signature_sub = hash_hmac('sha256', $toSign , $secretKey);

    // // Agrega a la url el servicio a consumir
    // $urlSub= $urlEnviroment . '/subscription/create';

    // // agrega la firma a los parámetros
    // $paramsSub["s"] = $signature_sub;
    // //Codifica los parámetros en formato URL y los agrega a la URL
    // $urlSub = $urlSub . "?" . http_build_query($paramsSub);

    // try {
    //   $ch = curl_init();
    //   curl_setopt($ch, CURLOPT_URL, $urlSub);
    //   curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    //   curl_setopt($ch, CURLOPT_POST, TRUE);
    //   curl_setopt($ch, CURLOPT_POSTFIELDS, $paramsSub);
    //   $response = curl_exec($ch);
    //   if($response === false) {
    //     $error = curl_error($ch);
    //     throw new Exception($error, 1);
    //   } 
    //   $info = curl_getinfo($ch);
    //   if(!in_array($info['http_code'], array('200', '400', '401'))) {
    //     throw new Exception('Unexpected error occurred. HTTP_CODE: '.$info['http_code'] , $info['http_code']);
    //   }
    //   echo $response;
    // } catch (Exception $e) {
    //   echo 'Error: ' . $e->getCode() . ' - ' . $e->getMessage();
    // }


    

    //POST Create Payment
    // $url_return = $urlEnviroment . "?" . $secretKey;

    $url_return = $this -> get_return_url( $customer_order);



    $paramsPayment = array (
      "apiKey" => "1F729C5B-7226-4127-AD5E-7E1C52AL6C56",
      "customerId" => $customerId,
      "url_return" => $url_return,

    );

    $keys_payment = array_keys($paramsPayment);
    sort($keys_payment);
    $toSign = "";
    foreach($keys_payment as $key) {
      $toSign .= $key . $paramsPayment[$key];
    };
    $signature_payment = hash_hmac('sha256', $toSign , $secretKey);

    // Agrega a la url el servicio a consumir
    $urlPayment= $urlEnviroment . '/customer/register';

    // agrega la firma a los parámetros
    $paramsPayment["s"] = $signature_payment;
    //Codifica los parámetros en formato URL y los agrega a la URL
    $urlPayment = $urlPayment . "?" . http_build_query($paramsPayment);

    try {
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $urlPayment);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
      curl_setopt($ch, CURLOPT_POST, TRUE);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $paramsPayment);
      $response = curl_exec($ch);
      if($response === false) {
        $error = curl_error($ch);
        throw new Exception($error, 1);
      } 
      $info = curl_getinfo($ch);
      if(!in_array($info['http_code'], array('200', '400', '401'))) {
        throw new Exception('Unexpected error occurred. HTTP_CODE: '.$info['http_code'] , $info['http_code']);
      }
      $response = json_decode($response, true);
      $url_register= $response['url'];
      $token = $response['token'];

      $redirect = $url_register . "?token=" . $token;
      $customer_order->payment_complete();
      $woocommerce->cart->empty_cart();

      //return url to redirect to flow payment page 
      return array(
        'result' => 'success',
        'redirect' => $redirect
      );

      exit();
      echo $response;
    } catch (Exception $e) {
      echo 'Error: ' . $e->getCode() . ' - ' . $e->getMessage();
    }

    // Redirect to Flow
    // cors




    // // this is important part for empty cart
    // Redirect to thank you page



    
  
    
  }
  


  
  

  
}



?>