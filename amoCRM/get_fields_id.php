<?php
require_once 'access.php'; 
require_once 'config.php';


$link = "https://$subdomain.amocrm.ru/api/v4/leads/custom_fields";

$curl = curl_init();
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-oAuth-client/1.0');
curl_setopt($curl, CURLOPT_URL, $link);
curl_setopt($curl, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $access_token", 
    'Content-Type: application/json'
]);
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
$out = curl_exec($curl);
$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

$response = json_decode($out, true);

if ($code < 200 || $code > 204) {
    echo "Ошибка получения полей. Код ответа: $code. Ответ: " . json_encode($response);
    die();
}

echo '<pre>';
print_r($response);
echo '</pre>';
?>