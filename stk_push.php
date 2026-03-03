<?php
// Enable error reporting to see what's wrong
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// 1. YOUR DARAJA KEYS
$consumerKey = 'zXw1CTJmWjMyX6g3QpbvHJkcGLk1YWnOKbbo0Y8gWdmopO2N';
$consumerSecret = 'NeDkw6Ab3qjZy3QJVUHgU5oSRPE2NRUghPCGlXxbAaixniUzoANKcFhGEBRJ4Z2T';

// 2. SANDBOX SETTINGS (DO NOT CHANGE THESE IN SANDBOX)
$BusinessShortCode = '174379'; 
$Passkey = 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919';

$phone = $_POST['phone'] ?? '';
if(empty($phone)) {
    echo json_encode(["errorMessage" => "No phone number provided"]);
    exit;
}

// 3. GET ACCESS TOKEN
$headers = ['Authorization: Basic ' . base64_encode($consumerKey . ':' . $consumerSecret)];
$curl = curl_init('https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials');
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // Required if testing on Localhost
$res = json_decode(curl_exec($curl));

if (!isset($res->access_token)) {
    echo json_encode(["errorMessage" => "Could not generate Access Token. Check your Key/Secret."]);
    exit;
}
$accessToken = $res->access_token;

// 4. PREPARE STK PUSH
$timestamp = date('YmdHis');
$password = base64_encode($BusinessShortCode . $Passkey . $timestamp);

$stkHeader = ['Content-Type:application/json', 'Authorization:Bearer ' . $accessToken];
$curl_post_data = [
    'BusinessShortCode' => $BusinessShortCode,
    'Password' => $password,
    'Timestamp' => $timestamp,
    'TransactionType' => 'CustomerPayBillOnline',
    'Amount' => 1, // Sandbox often prefers 1 KES for testing
    'PartyA' => $phone,
    'PartyB' => $BusinessShortCode,
    'PhoneNumber' => $phone,
    'CallBackURL' => 'https://callback.requestcatcher.com/test', // Dummy callback
    'AccountReference' => 'WIFI_TEST',
    'TransactionDesc' => 'WiFi Payment'
];

$curl = curl_init('https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest');
curl_setopt($curl, CURLOPT_HTTPHEADER, $stkHeader);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($curl_post_data));
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($curl);
echo $response; 
?>
