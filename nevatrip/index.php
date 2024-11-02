<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Nevatrip</title>
    <link rel="stylesheet" href="index.css">

    <script>
        /* Получаем цену взрослого и детского билета из файла "functions.php" */
        var price = '<?php  require "functions.php"; global $price_adult, $price_kid; echo json_encode(["adult" => $price_adult,"kid" => $price_kid]); ?>';
        price = JSON.parse(price)

        /* Функция для перерасчета общей цены при изменении количества билетов */
        function updatePrice(){
            kid_value = document.getElementById('kid').value
            adult_value = document.getElementById('adult').value
            document.getElementById('price').innerHTML = adult_value * price.adult + kid_value * price.kid
        }

        /* Функция, вызывающаяся при загрузке страницы */
        function ready(){
            /* Устанавливаем текущую дату в поле выбора даты полета*/
            const now = new Date();
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const day = String(now.getDate()).padStart(2, '0');
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            document.getElementById('date').value = `${year}-${month}-${day} ${hours}:${minutes}`

            /* Отображаем текущие цены взрослого и детского билета при покупке */
            document.getElementById('price_adult').innerHTML = price.adult
            document.getElementById('price_kid').innerHTML = price.kid

            /* Вызываем функцию для обновления общей цены заказа при изменении количества билетов */
            document.getElementById('adult').addEventListener('input',function(e){
                updatePrice()
            });
            document.getElementById('kid').addEventListener('input',function(e){
                updatePrice()
            });

            /* При клике на кнопку "Купить" отправляем POST запрос с введенными данными, который обработает файл "buy_ticket.php" */
            document.getElementById('buy').addEventListener('click',function(e){
                e.preventDefault();

                /*  */
                const data = {
                    count_adult: document.getElementById('adult').value,
                    count_kid: document.getElementById('kid').value,
                    date: document.getElementById('date').value
                };

                /* Отправляем POST запрос */
                fetch('buy_ticket.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json; charset=utf-8'
                    },
                    body: JSON.stringify(data)
                }).then(response => response.json())
                    .then(result => {
                        /* Выводим результат успеха создания заказа */
                        alert(result.message)

                        /* Если заказ был успешно добавлен - обновляем страницу */
                        if(result.status === 'success')
                            document.location.reload()
                    })
                    .catch(error => {
                        /* Выводим результат ошибки запроса */
                        alert(error);
                    });
            });
        }

        document.addEventListener("DOMContentLoaded", ready);
    </script>

</head>
<body>
<div class="app">
    <header class="head">
        <a href="index.php"> <img src="./assets/logo.png"></a>
        <input type="text" placeholder="Поиск билетов...">
        <div class="pages">
            <a href="tickets.php">
                <span class="icon_report"> </span>
                <p>Покупки</p>
            </a>
        </div>
    </header>
    <div class="main">
        <h1> Покупка билетов</h1>
        <div class="ticket_buy">
            <form method="post">
                <div class="data">
                    <div>Дата полета: <input type="datetime-local" id="date"></div>
                    <div>Взрослый билет: <input type="number" id="adult" value="0"></div>
                    <div class="price">Цена <span id="price_adult"></span> ₽</div>
                    <div>Детский билет: <input type="number" id="kid" value="0"></div>
                    <div class="price">Цена <span id="price_kid"></span> ₽</div>
                </div>
                <div class="cost_buy">
                    <span><span id="price">0</span> ₽</span>
                    <button type="submit" id="buy">Купить</button>
                </div>
            </form>

        </div>
    </div>
    <footer></footer>
</div>
</body>
</html>

