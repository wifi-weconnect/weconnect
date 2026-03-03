<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// 1. YOUR DARAJA KEYS
$consumerKey = 'zXw1CTJmWjMyX6g3QpbvHJkcGLk1YWnOKbbo0Y8gWdmopO2N';
$consumerSecret = 'NeDkw6Ab3qjZy3QJVUHgU5oSRPE2NRUghPCGlXxbAaixniUzoANKcFhGEBRJ4Z2T';

// 2. SANDBOX SETTINGS
$BusinessShortCode = '174379'; 
$Passkey = 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919';

$phone = $_POST['phone'] ?? '254700000000';
$amount = "2";
$timestamp = date('YmdHis');
$password = base64_encode($BusinessShortCode . $Passkey . $timestamp);

// 3. GET ACCESS TOKEN
$headers = ['Authorization: Basic ' . base64_encode($consumerKey . ':' . $consumerSecret)];
$curl = curl_init('https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials');
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$res = json_decode(curl_exec($curl));

if (!isset($res->access_token)) {
    echo json_encode(["ResponseCode" => "1", "errorMessage" => "Invalid API Keys"]);
    exit;
}
$accessToken = $res->access_token;

// 4. TRIGGER STK PUSH
$stkHeader = ['Content-Type:application/json', 'Authorization:Bearer ' . $accessToken];
$curl_post_data = [
    'BusinessShortCode' => $BusinessShortCode,
    'Password' => $password,
    'Timestamp' => $timestamp,
    'TransactionType' => 'CustomerPayBillOnline',
    'Amount' => $amount,
    'PartyA' => $phone,
    'PartyB' => $BusinessShortCode,
    'PhoneNumber' => $phone,
    'CallBackURL' => 'https://example.com/callback',
    'AccountReference' => 'WIFI_EXPLOIT',
    'TransactionDesc' => 'WiFi Payment'
];

$curl = curl_init('https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest');
curl_setopt($curl, CURLOPT_HTTPHEADER, $stkHeader);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($curl_post_data));

$response = curl_exec($curl);
echo $response; 
?>
