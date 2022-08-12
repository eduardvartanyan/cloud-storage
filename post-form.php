<!DOCTYPE html>
<html>
<head>
    <title></title>
    <style>
        .message-form {
            max-width: 500px;
            margin: 0 20px;
        }
        .message-form > div {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .message-form_input {
            width: 80%;
        }
    </style>
</head>
<body>
    <form action="./file/" class="message-form" method="post" enctype="multipart/form-data">
        <div>
            <label>Файл:</label>
            <input type="file" name="file" class="message-form_input">
        </div>
        <div>
            <input type="submit" value="Отправить">
        </div>
    </form>
</body>
</html>
