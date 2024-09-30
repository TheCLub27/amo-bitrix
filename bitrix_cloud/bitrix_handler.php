<?php

$name = $_POST['name'];
$phone = $_POST['phone'];
$comment = $_POST['comment'];

if (empty($name) || empty($phone) || empty($comment)) {
    die('Заполните все поля формы.');
}


$webhook_url = "https://b24-rwxdsm.bitrix24.ru/rest/1/ef55lqwyhvap91bx/";

    
function send_request_to_bitrix($method, $data) {
global $webhook_url;    
$url = $webhook_url . $method;

$curl = curl_init();
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
$response = curl_exec($curl);
curl_close($curl);

return json_decode($response, true);
}

$contact_data = [
    'fields' => [
        'NAME' => $name,
        'OPENED' => 'Y',
        'PHONE' => [
            ['VALUE' => $phone, 'VALUE_TYPE' => 'WORK']
            ]
    ]
];

$response = send_request_to_bitrix('crm.contact.add.json', $contact_data);

if (isset($response['result'])) {
    $contact_id = $response['result'];
    echo "Контакт успешно создан. ID: " . $contact_id . "<br>";
} else {
    die('Ошибка создания контакта: ' . json_encode($response));
}

$deal_data = [
    'fields' => [
        'TITLE' => 'Заявка с сайта ' . date('Y-m-d H:i:s'),
        'CONTACT_ID' => $contact_id, 
        'STAGE_ID' => 'NEW', 
        'UF_CRM_SOURCE' => 'Сайт' 
        ]
    ];

$response = send_request_to_bitrix('crm.deal.add.json', $deal_data);

if (isset($response['result'])) {
    $deal_id = $response['result'];
    echo "Сделка успешно создана. ID: " . $deal_id . "<br>";
} else {
    die('Ошибка создания сделки: ' . json_encode($response));
}

$comment_data = [
    'fields' => [
        'ENTITY_ID' => $deal_id,
        'ENTITY_TYPE' => 'deal',
        'COMMENT' => $comment
    ]
];

$response = send_request_to_bitrix('crm.timeline.comment.add.json', $comment_data);

if (isset($response['result'])) {
    echo "Комментарий успешно добавлен к сделке.<br>";
} else {
    die('Ошибка добавления комментария: ' . json_encode($response));
}

echo "Контакт, сделка и комментарий успешно созданы!";
?>