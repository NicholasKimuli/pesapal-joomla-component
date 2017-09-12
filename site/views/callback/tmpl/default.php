<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_donation
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
 
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
?>
    <h1>
        Callback
    </h1>

    <!-- Processing the callback -->
    <?php
 	include_once('OAuth.php');
    include_once('xmlhttprequest.php');

    // Getting from URL merchant reference ID and tracking ID from URL
    $merchantReference =  $_GET['pesapal_merchant_reference'];
    $trackingId = $_GET['pesapal_transaction_tracking_id'];

    $token = $params = NULL;
    $statusrequest = 'https://www.pesapal.com/api/querypaymentstatusbymerchantref';

    if(!empty($trackingId)){
        $statusrequest = 'https://www.pesapal.com/api/querypaymentdetails';
    }

    $signature_method = new OAuthSignatureMethod_HMAC_SHA1();
    // $dconfig = JComponentHelper::getParams('com_pesapal');
    // $consumer_key = $dconfig->get('consumer_key');
    $consumer_key = "5wLc9Gbr4ZtXiGRB5bSUkZae+udVEZwC";
    // $mode = $dconfig->get('app_mode');
    $mode = "demo";
    // $consumer_secret = $dconfig->get('consumer_secret');
    $consumer_secret = "ZAwnqjcIM/MWZOvoBk7tQJyq1zY=";

    $consumer = new OAuthConsumer($consumer_key,$consumer_secret);

    if($mode=="demo"){
        $statusrequest ="https://demo.pesapal.com/api/querypaymentstatusbymerchantref";
        if(!empty($trackingId)){
            $statusrequest ="https://demo.pesapal.com/api/querypaymentdetails";
        }
    }
        
    //get transaction status
    $request_status = OAuthRequest::from_consumer_and_token($consumer, $token,"GET", $statusrequest, $params);
    $request_status->set_parameter("pesapal_merchant_reference", $merchantReference);
    if(!empty($trackingId)) {
        $request_status->set_parameter("pesapal_transaction_tracking_id", $trackingId);
    }
    $request_status->sign_request($signature_method, $consumer, $token);

    $options = array(
            CURLOPT_RETURNTRANSFER => true,   // return web page
            CURLOPT_HEADER         => false,  // don't return headers
            CURLOPT_FOLLOWLOCATION => true,   // follow redirects
            CURLOPT_MAXREDIRS      => 10,     // stop after 10 redirects
            CURLOPT_ENCODING       => "",     // handle compressed
            CURLOPT_USERAGENT      => "test", // name of client
            CURLOPT_AUTOREFERER    => true,   // set referrer on redirect
            CURLOPT_CONNECTTIMEOUT => 120,    // time-out on connect
            CURLOPT_TIMEOUT        => 120,    // time-out on response
    );

    $ch = curl_init($request_status);
    curl_setopt_array($ch, $options);
    $content=curl_exec($ch);
    curl_close($ch);

    $values = array();
    $elements = preg_split("/=/",$content);
    $values[$elements[0]] = $elements[1];
    $res = explode( ',', $values['pesapal_response_data']);
    $array=array();
    if(count($res)==1){
        $array['status']=$res[0];
    } else{
        $array['tracking_id']=$res[0];
        $array['method']=$res[1];
        $array['status']=$res[2];
        $array['reference']=$res[3];
    }

    if ($array['status'] = "COMPLETED"):
        echo '<div class="alert alert-success">
        <strong>Success!</strong> Your donation was received.
      </div>';
        
        // Update order using reference
        // Create an object for the record we are going to update.
        $object = new stdClass();

        // Must be a valid primary key value.
        $object->reference = $array['reference'];
        $object->status = $array['status'];
        $object->tracking_id = $array['tracking_id'];
        $object->method = $array['method'];

        // Update their details in the users table using id as the primary key.
        $result = JFactory::getDbo()->updateObject('#__donations', $object, 'reference');

        // TODO: Send confirmation email

    elseif ($array['status'] = "PENDING"):
        echo '<div class="alert alert-warning">
        <strong>Pending!</strong> Your donation is pending.
      </div>';
        
        // Update order using reference
        // Create an object for the record we are going to update.
        $object = new stdClass();
        
        // Must be a valid primary key value.
        $object->reference = $array['reference'];
        $object->status = $array['status'];
        $object->tracking_id = $array['tracking_id'];
        $object->method = $array['method'];

        // Update their details in the users table using id as the primary key.
        $result = JFactory::getDbo()->updateObject('#__donations', $object, 'reference');

        // TODO: Send confirmation email

    elseif ($array['status'] = "FAILED"):
        echo '<div class="alert alert-danger">
        <strong>Failed!</strong> Your donation did not go through.
      </div>';
        
        // Update order using reference
        // Create an object for the record we are going to update.
        $object = new stdClass();

        // Must be a valid primary key value.
        $object->reference = $array['reference'];
        $object->status = $array['status'];
        $object->tracking_id = $array['tracking_id'];
        $object->method = $array['method'];

        // Update their details in the users table using id as the primary key.
        $result = JFactory::getDbo()->updateObject('#__donations', $object, 'reference');

        // TODO: Send confirmation email

    else:
        echo '<div class="alert alert-danger">
        <strong>An error occurred!</strong>
      </div>';

    endif; 
    ?>
