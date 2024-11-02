<?php

/* Функция имитирующая работу API (https://api.site.com/book) */
function apiBookResponse($data){
    /* Создаем ассоциативный массив с результатом */
    $api_responses = [
        ['message' => 'order successfully booked'],
        ['error' => 'barcode already exists']
    ];

    /* Возвращаем случайный результат */
    return $api_responses[array_rand($api_responses)];
}

/* Функция имитирующая работу API (https://api.site.com/approve) */
function apiApproveResponse($barcode){
    /* Создаем ассоциативный массив с результатом */
    $api_responses = [
        ['message' => 'order successfully aproved'],
        ['error' => 'event cancelled'],
        ['error' => 'no tickets'],
        ['error' => 'no seats'],
        ['error' => 'fan removed']
    ];

    /* Возвращаем случайный результат */
    return $api_responses[array_rand($api_responses)];
}

/* Функция для генерации штрихкода */
function generateBarcode(){
    return rand(10000000, 99999999);
}

/* Функция для получения всех записей заказов с БД */
function getOrders(){
    global $db;
    $data = [];
    $sql_get = $db->query("SELECT * FROM orders");
    while($row = $sql_get->fetch()){
        $data[] = $row;
    }
    return $data;
}

/* Функция для добавления записи в БД */
function insertOrderToDB($data){
    global $db;
    $sql_insert = $db->prepare("INSERT INTO orders (event_id, event_date, ticket_adult_price, ticket_adult_quantity, ticket_kid_price, ticket_kid_quantity, barcode, user_id, equal_price, created) 
                        VALUES (:event_id, :event_date, :ticket_adult_price, :ticket_adult_quantity, :ticket_kid_price, :ticket_kid_quantity, :barcode, :user_id, :equal_price, :created)");
    $sql_insert->bindParam(':event_id', $data['event_id'], PDO::PARAM_INT);
    $sql_insert->bindParam(':event_date', $data['event_date']);
    $sql_insert->bindParam(':ticket_adult_price', $data['ticket_adult_price'], PDO::PARAM_INT);
    $sql_insert->bindParam(':ticket_adult_quantity', $data['ticket_adult_quantity'], PDO::PARAM_INT);
    $sql_insert->bindParam(':ticket_kid_price', $data['ticket_kid_price'], PDO::PARAM_INT);
    $sql_insert->bindParam(':ticket_kid_quantity', $data['ticket_kid_quantity'], PDO::PARAM_INT);
    $sql_insert->bindParam(':barcode', $data['barcode'], PDO::PARAM_INT);
    $sql_insert->bindParam(':user_id', $data['user_id'], PDO::PARAM_INT);
    $sql_insert->bindParam(':equal_price', $data['equal_price'], PDO::PARAM_INT);
    $sql_insert->bindParam(':created', $data['created']);

    /* Возвращаем результат работы, 1 - успешно, 0 - ошибка */
    if ($sql_insert->execute())
        return true;
    else
        return false;
}

/* Функция для создания заказа */
function addOrder($event_id, $event_date, $ticket_adult_price, $ticket_adult_quantity, $ticket_kid_price, $ticket_kid_quantity){
    /* Создаем ассоциативный массив и помещайм в него данные, переданные функции*/
    $data = [
        'event_id' => $event_id,
        'event_date' => $event_date,
        'ticket_adult_price' => $ticket_adult_price,
        'ticket_adult_quantity' => $ticket_adult_quantity,
        'ticket_kid_price' => $ticket_kid_price,
        'ticket_kid_quantity' => $ticket_kid_quantity,
    ];

    /* Генерируем штрихкод и обращаемся к API (https://api.site.com/book) для брони заказа */
    do {
        $data['barcode'] = generateBarcode();
        $response = apiBookResponse($data);
    }
    while (isset($response['error']));

    if (isset($response['message'])){
        /* После успешной брони обращаемся к API (https://api.site.com/approve)*/
        $response = apiApproveResponse($data['barcode']);

        /* Если результат успешный добавляем в БД заказ*/
        if (isset($response['message'])){
            /* Добавлвяем в массив оставшиеся данные*/
            $data['user_id'] = rand(1, 500);
            $data['equal_price'] = $data['ticket_adult_price'] * $data['ticket_adult_quantity'] +
                                    $data['ticket_kid_price'] * $data['ticket_kid_quantity'];
            $data['created'] = date('Y-m-d H:i:s');

            /* Вызываем фйнкцию для добавления записи в БД, если произошла ошибка - выбрасываем исключение с текстом ошибки */
            if(!insertOrderToDB($data))
                throw new Exception('Error add to DB');
        }
        /* Иначе выбрасываем исключение с текстом ошибки */
        else
            throw new Exception('Error approve order: '. $response['error']);
    }
}

/* Данные для подключения к MYSQL */
$dbhost = "localhost";
$dbname = "nevatrip";
$dbusername = "root";
$dbpassword = "23112001";

$db = new PDO("mysql:host=$dbhost; dbname=$dbname", "$dbusername", "$dbpassword"); /* Подключение к БД */

$price_adult = 1500; /* Цена взрослого билета */
$price_kid = 800; /* Цена детского билета */

