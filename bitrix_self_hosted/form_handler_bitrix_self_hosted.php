<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");


global $USER;
if (!$USER->IsAuthorized()) {
    echo json_encode(["error" => "User is not authorized."]);
    exit;
}


$name = $_POST['name'];
$phone = $_POST['phone'];
$comment = $_POST['comment'];

if (empty($name) || empty($phone) || empty($comment)) {
    die('Заполните все поля формы.');
}


if (!CModule::IncludeModule('crm')) {
    die('Не удалось подключить модуль CRM.');
}


$contactData = [
    'NAME' => $name,
    'OPENED' => 'Y',
    'PHONE' => [
        ['VALUE' => $phone, 'VALUE_TYPE' => 'WORK']
    ]
];

$contactEntity = new CCrmContact();
$contactId = $contactEntity->Add($contactData);

if (!$contactId) {
    die('Ошибка создания контакта: ' . $contactEntity->LAST_ERROR);
}
echo "Контакт успешно создан. ID: " . $contactId . "<br>";


$dealData = [
    'TITLE' => 'Заявка с сайта ' . date('Y-m-d H:i:s'),
    'CONTACT_ID' => $contactId,
    'STAGE_ID' => 'NEW',
    'UF_CRM_SOURCE' => 'Сайт'
];

$dealEntity = new CCrmDeal();
$dealId = $dealEntity->Add($dealData);

if (!$dealId) {
    die('Ошибка создания сделки: ' . $dealEntity->LAST_ERROR);
}
echo "Сделка успешно создана. ID: " . $dealId . "<br>";


$activityData = [
    'TYPE_ID' => CCrmActivityType::Provider,
    'PROVIDER_ID' => 'CRM_EXTERNAL_CHANNEL',
    'PROVIDER_TYPE_ID' => 'COMMENT',
    'SUBJECT' => 'Комментарий к сделке',
    'DESCRIPTION' => $comment,
    'DESCRIPTION_TYPE' => CCrmContentType::PlainText,
    'RESPONSIBLE_ID' => $USER->GetID(),
    'BINDINGS' => [
        [
            'OWNER_TYPE_ID' => CCrmOwnerType::Deal,
            'OWNER_ID' => $dealId,
        ]
    ]
];

$activityId = CCrmActivity::Add($activityData, false);

if (!$activityId) {
    die('Ошибка добавления комментария: ' . $activityId);
}

echo "Комментарий успешно добавлен к сделке.<br>";
?>
