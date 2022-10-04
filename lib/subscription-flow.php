<?php

    /*
    Plugin name: Subscription Flow
    Plugin URI: https://www.subscriptionflow.com
    Description: Subscription Flow is a plugin that allows you to create a subscription flow for your website.
    Author: Jorge Oehrens
    Version: 1.0
    Author URI: https://www.subscriptionflow.com
    */


add_action('admin_menu', 'subscription_flow_menu');

function subscription_flow_menu() {
    add_menu_page('Suscripción Flow', 'Suscripción Flow', 'manage_options', 'subscription-flow', 'subscription_flow_options', 'dashicons-admin-generic', 6);
}

function subscription_flow_options() {
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }
    echo '<div class="wrap">';
    echo '<h2>Subscripciones flow</h2>';
    echo '<p>Revisa todas las suscipciones realizadas por tus usuarios.</p>';
    echo '</div>';
    //Tables susbcriptions
    echo '<div class="wrap">';
    echo '<h2>Suscripciones</h2>';
    echo '<table class="wp-list-table widefat fixed striped posts">';
    echo '<thead>';
    echo '<tr>';
    echo '<th scope="col" id="title" class="manage-column column-title column-primary sortable desc"><a href="#"><span>Nombre</span><span class="sorting-indicator"></span></a></th>';
    echo '<th scope="col" id="author" class="manage-column column-author">Membresía</th>';
    echo '<th scope="col" id="author" class="manage-column column-author">Valor</th>';

    echo '<th scope="col" id="categories" class="manage-column column-categories">Fecha de ultimo pago</th>';
    echo '<th scope="col" id="categories" class="manage-column column-categories">Fecha de renovación</th>';

    echo '</tr>';
    echo '</thead>';
    echo '<tbody id="the-list">';
    echo '<tr id="post-1" class="iedit author-self level-0 post-1 type-post status-publish format-standard hentry category-uncategorized">';
    echo '<td class="title column-title has-row-actions column-primary page-title" data-colname="Title">';
    echo '<strong><a class="row-title" href="http://flow.cl" aria-label=“Nombre usuario” (Edit)">“Nombre usuario”</a></strong>';
    echo '<div class="row-actions">';
    echo '<span class="view"><a href="http://flow.cl" rel="bookmark" aria-label="View “Hello world!”">Revisar</a></span>';
    echo '</div>';
    echo '<button type="button" class="toggle-row"><span class="screen-reader-text">Show more details</span></button>';
    echo '</td>';
    echo '<td class="author column-author" data-colname="Author">admin</td>';
    echo '<td class="categories column-categories" data-colname="Categories">$</td>';
    echo '<td class="categories column-categories" data-colname="Date">Fecha</td>';
    echo '<td class="categories column-categories" data-colname="Date">Fecha</td>';

    echo '</tr>';
    echo '</tbody>';
    echo '<tfoot>';
    echo '<tr>';
    echo '<th scope="col" class="manage-column column-title column-primary sortable desc"><a href="#"><span>Nombre</span><span class="sorting-indicator"></span></a></th>';
    echo '<th scope="col" class="manage-column column-author">Membresía</th>';
    echo '<th scope="col" id="author" class="manage-column column-author">Valor</th>';

    echo '<th scope="col" class="manage-column column-categories">Fecha de ultimo pago</th>';
    echo '<th scope="col" id="categories" class="manage-column column-categories">Fecha de renovación</th>';

    echo '</tr>';
    echo '</tfoot>';
    echo '</table>';
    echo '</div>';
    

}

add_action('admin_menu', 'subscription_flow_menu');

$params = array( 
    "apiKey" => "1F729C5B-7226-4127-AD5E-7E1C52AL6C56",
    "token" => "47ccf1f6240b920d483cc19876c2907146690367"
  ); 
$keys = array_keys($params);
sort($keys);


$secretKey="47ccf1f6240b920d483cc19876c2907146690367";
$toSign = "";
foreach($keys as $key) {
  $toSign .= $key . $params[$key];
};
$signature = hash_hmac('sha256', $toSign , $secretKey);
$url = 'https://sandbox.flow.cl/api';
// Agrega a la url el servicio a consumir
$url = $url . '/customer/list';
// agrega la firma a los parámetros
$params["s"] = $signature;
//Codifica los parámetros en formato URL y los agrega a la URL
$url = $url . "?" . http_build_query($params);
// Realiza la petición

echo $signature;
try {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
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
?>


<!-- 

$fisrt_name = $customer_order->billing_first_name;
$last_name = $customer_order->billing_last_name;
$complete_name = $fisrt_name . ' ' . $last_name;

$email = $customer_order->billing_email;

$externalId= $customer_order->id;

$producto_id = $customer_order->get_items();
$producto_id = $producto_id[0]['product_id'];

$producto = wc_get_product($producto_id);
$producto_name = $producto->get_title();
$producto_price = $producto->get_price();
$producto_price = $producto_price * 100;
  -->