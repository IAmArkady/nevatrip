<!DOCTYPE html>
<html lang="">
<head>
    <meta charset="utf-8">
    <title>Nevatrip</title>
    <link rel="stylesheet" href="index.css">

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
        <h1> Купленные билеты</h1>
        <div class="tickets">
                <table>
                    <thead>
                    <tr>
                        <th scope="row">event_id</th>
                        <th scope="row">event_date</th>
                        <th scope="row">ticket_adult_price</th>
                        <th scope="row">ticket_adult_quantity</th>
                        <th scope="row">ticket_kid_price</th>
                        <th scope="row">ticket_kid_quantity</th>
                        <th scope="row">barcode</th>
                        <th scope="row">user_id</th>
                        <th scope="row">equal_price</th>
                        <th scope="row">created</th>
                    </tr>
                    </thead>

                    <tbody>
                    <?php
                    require "functions.php";
                    $result = getOrders();
                    foreach ($result as $row){
                        echo "<tr>" .
                            "<td>" . $row["event_id"] . "</td>".
                            "<td>" . $row["event_date"] . "</td>".
                            "<td>" . $row["ticket_adult_price"] . "</td>".
                            "<td>" . $row["ticket_adult_quantity"] . "</td>".
                            "<td>" . $row["ticket_kid_price"] . "</td>".
                            "<td>" . $row["ticket_kid_quantity"] . "</td>".
                            "<td>" . $row["barcode"] . "</td>".
                            "<td>" . $row["user_id"] . "</td>".
                            "<td>" . $row["equal_price"] . "</td>".
                            "<td>" . $row["created"] . "</td>".
                            "</tr>";
                    }
                    ?>
                    </tbody>
                </table>
        </div>
    </div>
    <footer></footer>
</div>
</body>
</html>


