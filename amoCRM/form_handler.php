<?php
require_once 'config.php';
require_once 'access.php'; 


$name = $_POST['name'];
$phone = $_POST['phone'];
$comment = $_POST['comment'];


if (empty($name) || empty($phone) || empty($comment)) {
    die('Заполните все поля формы.');
}


function send_request($link, $access_token, $data) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-oAuth-client/1.0');
    curl_setopt($curl, CURLOPT_URL, $link);
    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $access_token,
        'Content-Type: application/json'
    ]);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
    $response = curl_exec($curl);
    $code_error = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    if ($code_error < 200 || $code_error > 204) {
        die("Ошибка при выполнении запроса. Код ответа: $code_error. Ответ: " . $response);
    }

    return json_decode($response, true);
}


$link = "https://$subdomain.amocrm.ru/api/v4/contacts";
$data = [
    [
        'name' => $name,
        'custom_fields_values' => [
            [
                'field_code' => 'PHONE',
                'values' => [
                    ['value' => $phone]
                ]
            ]
        ]
    ]
];
$response = send_request($link, $access_token, $data);
$contact_id = $response['_embedded']['contacts'][0]['id'];


$link = "https://$subdomain.amocrm.ru/api/v4/leads";
$data = [
    [
        'name' => "Заявка с сайта " . date("Y-m-d H:i:s"),
         '_embedded' => [
            'contacts' => [
                ['id' => $contact_id]
            ]
        ],
        'custom_fields_values' => [
            [
                'field_id' => 354755, 
                'values' => [
                    ['enum_id' => 205909] 
                ]
            ]
        ],
        
        'tags' => [
            ['name' => 'сайт']
        ]
    ]
];
$response = send_request($link, $access_token, $data);
$lead_id = $response['_embedded']['leads'][0]['id'];

$link = "https://$subdomain.amocrm.ru/api/v4/leads/$lead_id/notes";
$data = [
    [
        "note_type" => "common", 
        "params" => [
            "text" => $comment 
        ]
    ]
];
$response = send_request($link, $access_token, $data);



echo "Контакт и сделка созданы";
?>