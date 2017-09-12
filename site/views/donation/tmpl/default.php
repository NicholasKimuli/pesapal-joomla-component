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
    
    <p> Consumer Secret: <?php echo $consumerSecret; ?></p><br>
    <p> Consumer Key: <?php echo $consumerKey; ?></p>
    
    <!-- Creating form -->
    <?php if(!isset($_POST['submitButton'])): ?>
    <div style="width: 100%;">
        <form role="form" action="" method="post">
            <div class="form-group">
                <label for="fname">First Name</label>
                <input type="text" class="form-control" name="fname" id="fname" placeholder="Enter First Name" required>
            </div>
            <div class="form-group">
                <label for="lname">Last Name</label>
                <input type="text" class="form-control" id="lname" name="lname" placeholder="Enter Last Name" required>
            </div>
            <div class="form-group">
                <label for="email">Email address</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Enter email" required>
            </div>
            <div class="form-group">
                <label for="mobile">Mobile</label>
                <input type="text" class="form-control" id="mobile" name="mobile" placeholder="Mobile" required>
            </div>
            <div class="form-group">
                <label for="amount">Amount</label>
                <input type="number" class="form-control" id="amount" name="amount" placeholder="Enter Amount" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <input type="text" class="form-control" id="description" name="description" placeholder="Description" required>
            </div>
            <div class="form-group">
                <label for="time">Time Period</label>
                <select class="form-control" name="time" id="time">
                    <option value="oneoff">One-off</option>
                    <option value="monthly">Monthly</option>
                    <option value="annual">Annual</option>
                </select>
            </div>
            <button type="submit" name="submitButton" class="btn btn-success">Donate</button>
        </form>
    </div>

<?php else:
include_once('OAuth.php');

$token = $params = NULL;

// $dconfig = JComponentHelper::getParams('com_donation');

// $consumer_key = $dconfig->get('consumer_key');
// $consumer_secret = $dconfig->get('consumer_secret');
$consumer_key = "5wLc9Gbr4ZtXiGRB5bSUkZae+udVEZwC";
$consumer_secret = "ZAwnqjcIM/MWZOvoBk7tQJyq1zY=";
// $mode = $dconfig->get('app_mode');
$mode = "demo";
$signature_method = new OAuthSignatureMethod_HMAC_SHA1();
$iframelink = 'https://www.pesapal.com/api/PostPesapalDirectOrderV4';
$amount = $_POST['amount'];
$amount = number_format($amount, 2);
$desc = 'desc';
$type = 'MERCHANT';
$reference = uniqid();
$first_name =  $_POST['fname'];
$last_name = $_POST['lname'];
$mobile = @$_POST['mobile'];
$email =  $_POST['email'];
$description =  $_POST['description'];
$currency =  'KES';
$city =  'Nairobi';
$address =  'Yaya center, Kilimani';
$zip =  '90837';
$country =  'Kenya';

if($mode=="demo"){
    $iframelink ="https://demo.pesapal.com/api/PostPesapalDirectOrderV4";
}

$donor = array();

$callback_url =JURI::root().'index.php?option=com_donation&view=callback';

$post_xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?><PesapalDirectOrderInfo xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" Currency=\"".$currency."\" Amount=\"".$amount."\" Description=\"".$desc."\" Type=\"".$type."\" Reference=\"".$reference."\" FirstName=\"".$first_name."\" LastName=\"".$last_name."\" Email=\"".$email."\"  xmlns=\"http://www.pesapal.com\" />";
$post_xml = htmlentities($post_xml);

$consumer = new OAuthConsumer($consumer_key, $consumer_secret);

//post transaction to pesapal
$iframe_src = OAuthRequest::from_consumer_and_token($consumer, $token, "GET", $iframelink, $params);
$iframe_src->set_parameter("oauth_callback", $callback_url);
$iframe_src->set_parameter("pesapal_request_data", $post_xml);
$iframe_src->sign_request($signature_method, $consumer, $token);

//save data to db
// Get a db connection.
$db = JFactory::getDbo();

// Create a new query object.
$query = $db->getQuery(true);

$columns = [
    'fname',
    'lname',
    'mobile',
    'email',
    'description',
    'amount',
    'status',
    'currency',
    'city',
    'address',
    'zipcode',
    'country',
    'reference'
];

$values = array($db->quote($first_name),
    $db->quote($last_name),
    $db->quote($mobile),
    $db->quote($email),
    $db->quote($description),
    $db->quote($amount),
    $db->quote('PLACED'),
    $db->quote($currency),
    $db->quote($city),
    $db->quote($address),
    $db->quote($zip),
    $db->quote($country),
    $db->quote($reference),
);

$query
    ->insert($db->quoteName('#__donations'))
    ->columns($db->quoteName($columns))
    ->values(implode(',', $values));
$db->setQuery($query);
$db->execute();

?>

<?php echo '<iframe src="'. $iframe_src . '" width="100%" height="620px" id="iframe" scrolling="no" frameBorder="0">
                <p>Browser unable to load iFrame</p>
            </iframe>';

?>

<?php endif; ?>