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
        Donate
    </h1>

<?php
    $dconfig = JComponentHelper::getParams('com_donation');
    $consumerKey = $dconfig->get('consumerkey');
    $consumerSecret = $dconfig->get('consumersecret');
?>

<!-- Processing the callback -->
<?php
include_once(JPATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'OAuth.php');

$statusrequest = 'https://demo.pesapal.com/api/querypaymentdetails';
$mode = "demo";

// Getting from URL merchant reference ID and tracking ID from URL
// Parameters sent to you by PesaPal IPN
$pesapalNotification = $_GET['pesapal_notification_type'];
$pesapalTrackingId = $_GET['pesapal_transaction_tracking_id'];
$pesapal_merchant_reference =  $_GET['pesapal_merchant_reference'];

if($pesapalTrackingId != ''): //$pesapalNotification == "CHANGE" && 
    
    $token = $params = NULL;
    $consumer = new OAuthConsumer($consumerKey,$consumerSecret);
    $signature_method = new OAuthSignatureMethod_HMAC_SHA1();
        
    //get transaction status
    $request_status = OAuthRequest::from_consumer_and_token($consumer, $token, "GET", $statusrequest, $params);
    $request_status->set_parameter("pesapal_merchant_reference", $pesapal_merchant_reference);
    $request_status->set_parameter("pesapal_transaction_tracking_id", $pesapalTrackingId);
    $request_status->sign_request($signature_method, $consumer, $token);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $request_status);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    
    if(defined('CURL_PROXY_REQUIRED')) if (CURL_PROXY_REQUIRED == 'True')
    {
        $proxy_tunnel_flag = (defined('CURL_PROXY_TUNNEL_FLAG') && strtoupper(CURL_PROXY_TUNNEL_FLAG) == 'FALSE') ? false : true;
        curl_setopt ($ch, CURLOPT_HTTPPROXYTUNNEL, $proxy_tunnel_flag);
        curl_setopt ($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
        curl_setopt ($ch, CURLOPT_PROXY, CURL_PROXY_SERVER_DETAILS);
    }

    $response = curl_exec($ch);
    $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $raw_header  = substr($response, 0, $header_size - 4);
    $headerArray = explode("\r\n\r\n", $raw_header);
    $header      = $headerArray[count($headerArray) - 1];

    //transaction status
    $elements = preg_split("/=/",substr($response, $header_size));
    $status = $elements[1];
    curl_close ($ch);

    if ($status = "COMPLETED"):
        echo '<div class="alert alert-success">
        <strong>Success!</strong> Your donation was received. Thank you.
    </div>';
        
        // Update order using reference
        // Create an object for the record we are going to update.
        $object = new stdClass();

        // Must be a valid primary key value.
        $object->reference = $pesapal_merchant_reference;
        $object->status =  $status;
        $object->tracking_id = $pesapalTrackingId;

        // Update their details in the users table using id as the primary key.
        $result = JFactory::getDbo()->updateObject('#__donations', $object, 'reference');

        // TODO: Send confirmation email

    elseif ($status = "PENDING"):
        echo '<div class="alert alert-warning">
        <strong>Pending!</strong> Your donation is pending.
    </div>';
        
        // Update order using reference
        // Create an object for the record we are going to update.
        $object = new stdClass();
        
        // Must be a valid primary key value.
        $object->reference = $pesapal_merchant_reference;
        $object->status =  $status;
        $object->tracking_id = $pesapalTrackingId;

        // Update their details in the users table using id as the primary key.
        $result = JFactory::getDbo()->updateObject('#__donations', $object, 'reference');

        // TODO: Send email

    elseif ($status = "FAILED"):
        echo '<div class="alert alert-danger">
        <strong>Failed!</strong> Your donation did not go through.
    </div>';
        
        // Update order using reference
        // Create an object for the record we are going to update.
        $object = new stdClass();

        // Must be a valid primary key value.
        $object->reference = $pesapal_merchant_reference;
        $object->status =  $status;
        $object->tracking_id = $pesapalTrackingId;

        // Update their details in the users table using id as the primary key.
        $result = JFactory::getDbo()->updateObject('#__donations', $object, 'reference');

        // TODO: Send email

    else:
        echo '<div class="alert alert-danger">
        <strong>An error occurred!</strong>
    </div>';

    endif; 

else:
    echo '<div class="alert alert-danger"><strong>Error!</strong> Your donation was not received.</div>';
    
endif;

?>
