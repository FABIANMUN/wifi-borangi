<?php
// Check if form is submitted
if(isset($_POST['submit'])) {
    // Set timezone
    date_default_timezone_set('Africa/Nairobi');

    // Access token credentials
    $consumerKey = 'Your_Consumer_Key';
    $consumerSecret = 'Your_Consumer_Secret';

    // M-PESA details
    $BusinessShortCode = 'Your_Business_ShortCode';
    $Passkey = 'Your_Passkey';

    // Callback URL
    $CallBackURL = 'https://arcane-retreat-18416-20a639b8efcc.herokuapp.com/callback_url.php';

    // Get phone number and amount from form
    $PartyA = $_POST['phone'];
    $Amount = $_POST['amount'];

    // Generate timestamp and password
    $Timestamp = date('YmdHis');
    $Password = base64_encode($BusinessShortCode.$Passkey.$Timestamp);

    // Get access token
    $access_token_url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
    $headers = ['Content-Type:application/json; charset=utf8'];
    $curl = curl_init($access_token_url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($curl, CURLOPT_HEADER, FALSE);
    curl_setopt($curl, CURLOPT_USERPWD, $consumerKey.':'.$consumerSecret);
    $result = curl_exec($curl);
    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $result = json_decode($result);
    $access_token = $result->access_token;  
    curl_close($curl);

    // Initiate transaction
    $initiate_url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
    $stkheader = ['Content-Type:application/json','Authorization:Bearer '.$access_token];
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $initiate_url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $stkheader);
    $curl_post_data = array(
        'BusinessShortCode' => $BusinessShortCode,
        'Password' => $Password,
        'Timestamp' => $Timestamp,
        'TransactionType' => 'CustomerPayBillOnline',
        'Amount' => $Amount,
        'PartyA' => $PartyA,
        'PartyB' => $BusinessShortCode,
        'PhoneNumber' => $PartyA,
        'CallBackURL' => $CallBackURL,
        'AccountReference' => 'Your_Account_Reference',
        'TransactionDesc' => 'Test Payment'
    );
    $data_string = json_encode($curl_post_data);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
    $curl_response = curl_exec($curl);
    curl_close($curl);
    
    // Display response
    echo $curl_response;
} else {
    echo 'Form not submitted.';
}
?>
