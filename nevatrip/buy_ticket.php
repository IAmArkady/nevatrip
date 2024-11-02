<?php
require_once "functions.php";
/* Проверяем, что был отправлен POST запрос */
if ($_SERVER["REQUEST_METHOD"]=="POST") {
    /* Сохраняем переданные данные (кол. взрослых и детских билетов, дату полета) */
    $post_data = json_decode(file_get_contents('php://input'), true);
    if (isset($post_data['count_adult']) && isset($post_data['count_kid']) && isset($post_data['date'])) {
        global $price_kid, $price_adult;
        try {
            /* Форматируем переданную дату к виду "Y-m-d H:i:s" */
            $post_data['date'] = (new DateTime($post_data['date']))->format('Y-m-d H:i:s');

            /* Вызываем функцию для создания заказа */
            addOrder(rand(1, 10000), $post_data['date'], $price_adult, $post_data['count_adult'], $price_kid, $post_data['count_kid']);

            /* Создаем ассоциативный массив с ответом об успешном создании заказа */
            $response = [
                'status' => 'success',
                'message' => 'The ticket has been purchased'
            ];
        }catch (Exception $e){
            /* Создаем ассоциативный массив с ответом об ошибке создания заказа (ловим исключение, выбрасываемое фукнцией "addOrder") */
            $response = [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }
    else{
        /* Создаем ассоциативный массив с ответом об ошибке некорректности данных */
        $response = [
            'status' => 'error',
            'message' => 'Incorrect data!'
        ];
    }
} else {
    /* Создаем ассоциативный массив с ответом об ошибке некорректности запроса */
    $response = [
        'status' => 'error',
        'message' => 'The data transfer method is not a POST request!'
    ];
}

/* Форматируем ассоциативный массив в JSON и отправляем клиенту */
echo json_encode($response);