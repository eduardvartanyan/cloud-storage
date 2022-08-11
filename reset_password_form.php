<?php

$isSuccess = false;
$message = '';

if (isset($_POST['password']) && !empty($_POST['password'])) {

    $newPassword = $_POST['password'];

    if ($newPassword == $_POST['password_again']) {

        if (isset($_GET['code']) && !empty($_GET['code'])) {

            $code = $_GET['code'];

            $connection = new PDO('mysql:host=localhost;dbname=cloud_storage;charset=utf8', 'phpstorm','phpstorm');
            $statement = $connection->prepare("SELECT user_id FROM reset_password_code WHERE code = ?");
            $statement->execute([$code]);
            $result = $statement->fetch();

            if ($result != false) {

                $userId = $result['user_id'];
                $hash = password_hash($newPassword, PASSWORD_BCRYPT);

                $statement = $connection->prepare("UPDATE user SET hash = ? WHERE id = ?");
                $statement->execute([$hash, $userId]);

                $statement = $connection->prepare("DELETE FROM reset_password_code WHERE code = ?");
                $statement->execute([$code]);

                echo 'Новый пароль успешно установлен';
                $isSuccess = true;

            } else {

                $message = 'Что-то пошло не так. Перейдите еще раз по ссылке в письме.';

            }

        } else {

            $message = 'Что-то пошло не так. Перейдите еще раз по ссылке в письме.';

        }

    } else {

        $message = 'Вы ошиблись при повторной вводе пароля';

    }

}

if (!$isSuccess) {

?>

<!DOCTYPE html>
<html>
<head>
    <title>Восстановление пароля</title>
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
            width: 60%;
        }
    </style>
</head>
<body>

    <?php

    if (isset($_GET['code']) && !empty($_GET['code'])) {

        $code = $_GET['code'];

        $connection = new PDO('mysql:host=localhost;dbname=cloud_storage;charset=utf8', 'phpstorm','phpstorm');
        $statement = $connection->prepare("SELECT user_id FROM reset_password_code WHERE code = ?");
        $statement->execute([$_GET['code']]);
        $result = $statement->fetch();

        if ($result != false) {

            if ($message != '') { ?>

                <p><?=$message ?></p>

            <?php }

        ?>

            <form action="reset_password_form.php?code=<?=$code ?>" class="message-form" method="post">
                <h1>Задайте новый пароль</h1>
                <div>
                    <label for="password">Введите новый пароль:</label>
                    <input type="password" id="password" name="password" class="message-form_input" required>
                </div>
                <div>
                    <label for="password_again">Введите пароль ещё раз:</label>
                    <input type="password" id="password_again" name="password_again" class="message-form_input" required>
                </div>
                <div>
                    <input type="submit" value="Сохранить пароль">
                </div>
            </form>

        <?php

    } else {

        echo 'Ваша ссылка устарела. Воспользуйтесь восстановлением пароля повторно';

    }

    } else {

        echo 'Перейдите по ссылке в письме.';

    }

}

?>

</body>
</html>


