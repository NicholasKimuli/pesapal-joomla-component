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

    <?php
        include_once(JPATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'OAuth.php');

        $dconfig = JComponentHelper::getParams('com_donation');
        $consumerKey = $dconfig->get('consumerkey');
        $consumerSecret = $dconfig->get('consumersecret');

        $token = $params = NULL;
        $iframelink ="https://demo.pesapal.com/api/PostPesapalDirectOrderV4";

        $callback_url = JURI::root() . 'index.php?option=com_donation&view=callback'; //redirect url, the page that will handle the response from pesapal.
        $signature_method = new OAuthSignatureMethod_HMAC_SHA1();
    ?>

    <?php if(isset($this->msg)): ?>
        <h1>Complete donation</h1>

        <!-- Complete donation process -->
        <?php

            $post_xml_complete = "<?xml version=\"1.0\" encoding=\"utf-8\"?><PesapalDirectOrderInfo xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" Currency=\"".$this->msg->currency."\" Amount=\"".$this->msg->amount."\" Description=\"".$this->msg->description."\" Type=\"MERCHANT\" Reference=\"".$this->msg->reference."\" FirstName=\"".$this->msg->fname."\" LastName=\"".$this->msg->lname."\" Email=\"".$this->msg->email."\"  xmlns=\"http://www.pesapal.com\" />";
            $post_xml_complete = htmlentities($post_xml_complete);

            // Construct OAuth Request url
            $consumer = new OAuthConsumer($consumerKey, $consumerSecret);
            //post transaction to pesapal
            $iframe_src_complete = OAuthRequest::from_consumer_and_token($consumer, $token, "GET", $iframelink, $params);
            $iframe_src_complete->set_parameter("oauth_callback", $callback_url);
            $iframe_src_complete->set_parameter("pesapal_request_data", $post_xml_complete);
            $iframe_src_complete->sign_request($signature_method, $consumer, $token);
            
            echo '<iframe src="'. $iframe_src_complete . '" width="100%" height="720px" scrolling="no" frameBorder="0"><p>Unable to load the payment page</p></iframe>';

        ?>

    
    <?php else: ?>
        <h1>Donate</h1>

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

            $mode = "demo";
            $amount = $_POST['amount'];
            $amount = number_format($amount, 2, '.', '');//format amount to 2 decimal places
            $desc = $_POST['description'];
            $type = 'MERCHANT';//default value = MERCHANT
            $reference = uniqid();//unique order id of the transaction, generated by merchant
            $first_name =  $_POST['fname'];
            $last_name = $_POST['lname'];
            $phonenumber = $_POST['mobile'];//ONE of email or phonenumber is required
            $email =  $_POST['email'];
            $currency =  'KES';

            $post_xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?><PesapalDirectOrderInfo xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" Currency=\"".$currency."\" Amount=\"".$amount."\" Description=\"".$desc."\" Type=\"".$type."\" Reference=\"".$reference."\" FirstName=\"".$first_name."\" LastName=\"".$last_name."\" Email=\"".$email."\"  xmlns=\"http://www.pesapal.com\" />";
            $post_xml = htmlentities($post_xml);

            // Construct OAuth Request url
            $consumer = new OAuthConsumer($consumerKey, $consumerSecret);
            //post transaction to pesapal
            $iframe_src = OAuthRequest::from_consumer_and_token($consumer, $token, "GET", $iframelink, $params);
            $iframe_src->set_parameter("oauth_callback", $callback_url);
            $iframe_src->set_parameter("pesapal_request_data", $post_xml);
            $iframe_src->sign_request($signature_method, $consumer, $token);

            // Store donation details
            // Create and populate an object.
            $donation = new stdClass();
            $donation->fname = $first_name;
            $donation->lname = $last_name;
            $donation->mobile = $phonenumber;
            $donation->email = $email;
            $donation->description = $desc;
            $donation->amount = $amount;
            $donation->status = 'PLACED';
            $donation->currency = $currency;
            $donation->reference = $reference;
            $donation->donation_period = strtoupper($_POST['time']);
            
            // Insert the object into the user profile table.
            $result = JFactory::getDbo()->insertObject('#__donations', $donation);

            // Send email to user containing the link to payment
            // Using elastic mail
            $donation_url = JURI::root() . 'index.php?option=com_donation&view=donation&reference=' . $reference;
            
            $url = 'https://api.elasticemail.com/v2/email/send';

            $post = [
                'from' => 'nick@wizart.co.ke',
                'fromName' => 'Nicholas Kimuli',
                'apikey' => '394702b5-bea9-4db0-87f9-666b6554bab8',
                'subject' => 'Donation',
                'to' => $email,
                'bodyHtml' => '<h3>Donation placed</h3><br><p>Thank you for your interest in donating to our cause ' . $first_name . '. Your donation will be paid ' . strtolower($_POST['time']) . '.</p><br>Click <a href="' . $donation_url . '">here</a> to complete the donation.',
                'bodyText' => '',
                'isTransactional' => false
            ];
            
            $ch = curl_init();
            curl_setopt_array($ch, array(
                CURLOPT_URL => $url,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $post,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER => false,
                CURLOPT_SSL_VERIFYPEER => false
            ));
            
            $result=curl_exec ($ch);
            curl_close ($ch);
            
            echo '<iframe src="'. $iframe_src . '" width="100%" height="720px" scrolling="no" frameBorder="0"><p>Unable to load the payment page</p></iframe>';

        ?>

    <?php endif; ?>

<?php endif; ?>
