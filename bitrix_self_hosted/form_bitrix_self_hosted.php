<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Форма отправки данных Bitrix24</title>
</head>
<body>
    <h1>Отправьте заявку</h1>
    <form action="/bitrix/local/form_handler_bitrix_self_hosted.php" method="POST">
        <label for="name">Имя:</label>
        <input type="text" id="name" name="name" required><br><br>

        <label for="phone">Телефон:</label>
        <input type="text" id="phone" name="phone" required><br><br>

        <label for="comment">Комментарий:</label>
        <textarea id="comment" name="comment" required></textarea><br><br>

        <button type="submit">Отправить</button>
    </form>
</body>
</html>