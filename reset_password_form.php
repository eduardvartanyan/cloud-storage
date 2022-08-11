<?php

$isSuccess = false;
$message = '';

if (isset($_POST['password']) && !empty($_POST['password'])) {

    $newPassword = $_POST['password'];

    if ($newPassword == $_POST['password_again']) {

        if (isset($_GET['code']) && !empty($_GET['code'])) {

            $methodUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/reset_password/';
            $ch = curl_init($methodUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query(array_merge($_GET, $_POST)));
            $response = curl_exec($ch);
            $info = curl_getinfo($ch);
            curl_close($ch);

            if ($info['http_code'] == 200) {

                $isSuccess = true;
                echo $response;

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

        $methodUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/check_reset_password_code/?code=' . $code;
        $ch = curl_init($methodUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $response = curl_exec($ch);
        curl_close($ch);

        if ($response == true) {

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