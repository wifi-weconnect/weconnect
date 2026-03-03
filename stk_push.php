<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// 1. DARAJA KEYS
$consumerKey = 'zXw1CTJmWjMyX6g3QpbvHJkcGLk1YWnOKbbo0Y8gWdmopO2N';
$consumerSecret = 'NeDkw6Ab3qjZy3QJVUHgU5oSRPE2NRUghPCGlXxbAaixniUzoANKcFhGEBRJ4Z2T';

// 2. STK PUSH DETAILS (SANDBOX)
$shortCode = '174379';
$passkey = 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919';
$callbackUrl = 'https://mydomain.com/callback.php'; // Must be HTTPS for live, can be dummy for sandbox

$phone = $_POST['phone'] ?? '';
$amount = '2'; 

// 3. GENERATE ACCESS TOKEN
$credentials = base64_encode($consumerKey . ':' . $consumerSecret);
$ch = curl_init('https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials');
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Basic ' . $credentials]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
$token = json_decode($response)->access_token;

// 4. INITIATE STK PUSH
$timestamp = date('YmdHis');
$password = base64_encode($shortCode . $passkey . $timestamp);

$curl_post_data = [
    'BusinessShortCode' => $shortCode,
    'Password' => $password,
    'Timestamp' => $timestamp,
    'TransactionType' => 'CustomerPayBillOnline',
    'Amount' => $amount,
    'PartyA' => $phone,
    'PartyB' => $shortCode,
    'PhoneNumber' => $phone,
    'CallBackURL' => $callbackUrl,
    'AccountReference' => 'WIFI_EXPLOIT',
    'TransactionDesc' => 'WiFi Payment'
];

$ch = curl_init('https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $token
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($curl_post_data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$result = curl_exec($ch);
echo $result; // This sends the response back to your terminal log
?>
