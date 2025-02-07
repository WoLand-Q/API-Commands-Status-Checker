<?php
// process.php

ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');

// Подключаем "базу знаний"
require_once 'knowledge_base.php';

$apiLogin       = $_POST['apiLogin']       ?? '';
$organizationId = $_POST['organizationId'] ?? '';
$correlationId  = $_POST['correlationId']  ?? '';

// URL из документации Syrve / iikoCloud
$apiUrlAccessToken  = 'https://api-eu.syrve.live/api/1/access_token';
$apiUrlCommandStatus = 'https://api-eu.syrve.live/api/1/commands/status';

// Результирующий объект, который вернём в JSON
$responseData = [
    'apiResponse'   => null,   // Ответ от /commands/status
    'knowledgeBase' => null    // Текст подсказки из базы
];

// 1) Получить token по apiLogin
$token = getApiToken($apiUrlAccessToken, $apiLogin);
if (!$token) {
    $responseData['apiResponse'] = [
        'error' => 'Не удалось получить токен по API-логину.'
    ];
    echo json_encode($responseData, JSON_UNESCAPED_UNICODE);
    exit;
}

// 2) Запросить статус операции
$apiResponse = getCommandStatus(
    $apiUrlCommandStatus,
    $token,
    $organizationId,
    $correlationId
);

// Поместим API-ответ в $responseData
$responseData['apiResponse'] = $apiResponse;

// Вместо проверки только на Error, формируем общий $message
$message = trim(
    ($apiResponse['state']            ?? '') . ' ' .
    ($apiResponse['error']            ?? '') . ' ' .
    ($apiResponse['errorDescription'] ?? '')
);

// Если в сообщении что-то есть, ищем подсказку в $knowledgeBase
if ($message !== '') {
    error_log("Сообщение для поиска: $message");

    foreach ($knowledgeBase as $key => $tip) {
        if (stripos($message, $key) !== false) {
            $responseData['knowledgeBase'] = $tip;
            error_log("Совпадение найдено: $key");
            break;
        }
    }
}




// Возвращаем всё как JSON
echo json_encode($responseData, JSON_UNESCAPED_UNICODE);


/** 
 * Функция: Получить токен по apiLogin 
 * (POST { "apiLogin": "..."} => { "token": "..."} )
 */
function getApiToken($url, $apiLogin) {
    $requestData = [ 'apiLogin' => $apiLogin ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $result = curl_exec($ch);
    $error  = curl_error($ch);
    curl_close($ch);

    if ($error) {
        // cURL вернула ошибку, например, недоступна сеть
        return null;
    }

    $decoded = json_decode($result, true);
    // Ожидаем, что там есть 'token'
    return $decoded['token'] ?? null;
}

/** 
 * Функция: Проверить статус команды 
 * (Передаём Bearer-токен в заголовке)
 */
function getCommandStatus($url, $token, $orgId, $corrId) {
    $requestData = [
        'organizationId' => $orgId,
        'correlationId'  => $corrId
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $token
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $result = curl_exec($ch);
    $error  = curl_error($ch);
    curl_close($ch);

    if ($error) {
        return ['error' => 'Ошибка при запросе /commands/status: ' . $error];
    }

    return json_decode($result, true);
}
