<?php

include './StringeeApi/StringeeCurlClient.php';

// Replace with your actual Stringee credentials
$keySid = 'SK.0.aKolUvebgJY2aRzZkq32mkKkSjVSLkkB';
$keySecret = 'OW5LNEYzOWxaTTduVlBsY1k3bURQU1F6MUF2UVdhd3Y=';

$curlClient = new StringeeCurlClient($keySid, $keySecret);

// Function to generate a random OTP
function generateOTP($length = 6) {
    return implode('', array_map(function() {
        return rand(0, 9);
    }, range(1, $length)));
}

// Function to make a call with the OTP
function makeCall($from, $to, $message) {
    global $curlClient;
    
    $data = json_encode([
        "from" => [
            "type" => "external",
            "number" => $from,
            "alias" => $from
        ],
        "to" => [[
            "type" => "external",
            "number" => $to,
            "alias" => $to
        ]],
        "actions" => [
            [
                "action" => "talk",
                "text" => $message,
                "loop" => 2
            ]
        ]
    ]);

    $url = 'https://api.stringee.com/v1/call2/callout';
    $resJson = $curlClient->post($url, $data, 15);
    return json_decode($resJson->getStatusCode());
}

// Main logic
$otp = generateOTP();
$otpMessage = 'Mã xác thực của bạn là ' . implode(' ', str_split($otp)) . ' vui lòng không chia sẻ cho bất cứ ai!';

// Loop to call each digit
// foreach (str_split($otp) as $digit) {
//     if (makeCall("842473001664", "84869895748", $digit) !== 200) {
//         echo "Error: Failed to make call for OTP digit $digit.<br>";
//         break;
//     }
// }

// Final message with the full OTP
if (makeCall("842473001664", "84973976616", $otpMessage) === 200) {
    echo "OTP sent successfully.<br>";
} else {
    echo "Error: Failed to send final OTP message.<br>";
}

?>
