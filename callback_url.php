<?php
header("Content-Type: application/json");

$response = '{
    "ResultCode": 0,
    "ResultDesc": "Confirmation Received Successfully"
}';

// DATA
$mpesaResponse = file_get_contents('php://input');

// Parse the payload
$payload = json_decode($mpesaResponse, true);

// Check if the payment was successful
if ($payload['Body']['stkCallback']['ResultCode'] == 0) {
  $phone = $payload['Body']['stkCallback']['CallbackMetadata']['Item'][4]['Value'];
  $amount = $payload['Body']['stkCallback']['CallbackMetadata']['Item'][0]['Value'];
  $transactionStatus = $payload['Body']['stkCallback']['ResultDesc'];

  // Update user's access status in the database
  $conn = new mysqli("localhost", "username", "password", "database_name");
  $stmt = $conn->prepare("UPDATE users SET access_status = 'active', access_expiry = DATE_ADD(NOW(), INTERVAL ? HOUR) WHERE phone = ?");
  $duration = getDurationFromAmount($amount); // Function to determine duration based on payment amount
  $stmt->bind_param("is", $duration, $phone);
  $stmt->execute();
  $stmt->close();
  $conn->close();

  // Integrate with Wi-Fi access control system
  grantWiFiAccess($phone, $duration);

  // Log the successful payment
  $logFile = "successful_payments.txt";
  $log = fopen($logFile, "a");
  fwrite($log, "Phone: $phone, Amount: $amount, Status: $transactionStatus\n");
  fclose($log);
} else {
  // Log the failed payment
  $logFile = "failed_payments.txt";
  $log = fopen($logFile, "a");
  fwrite($log, $mpesaResponse);
  fclose($log);
}

echo $response;

function getDurationFromAmount($amount)
{
  // Implement your logic to determine the access duration based on the payment amount
  // For example:
  if ($amount == 10) {
    return 2; // 2 hours
  } elseif ($amount == 20) {
    return 4; // 4 hours
  }
  // ... Add more cases for different payment amounts ...
  return 1; // Default duration (1 hour)
}

function grantWiFiAccess($phone, $duration)
{
  // Assuming your Wi-Fi access control system has a CLI interface
  // and accepts commands like: grant_access <phone_number> <duration_hours>

  $command = "grant_access $phone $duration";

  // Execute the command to grant Wi-Fi access
  $output = null;
  $returnValue = null;
  exec($command, $output, $returnValue);

  // Handle the command output
  if ($returnValue === 0) {
    // Access granted successfully
    $logFile = "wifi_access_granted.txt";
    $log = fopen($logFile, "a");
    fwrite($log, "Phone: $phone, Duration: $duration hours\n");
    fclose($log);
  } else {
    // Failed to grant access
    $logFile = "wifi_access_failed.txt";
    $log = fopen($logFile, "a");
    fwrite($log, "Phone: $phone, Duration: $duration hours, Output: " . implode("\n", $output) . "\n");
    fclose($log);
  }
}