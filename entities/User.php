<?php

class User {

    static public function list() : array
    {

        $connection = new PDO('mysql:host=localhost;dbname=cloud_storage;charset=utf8', 'phpstorm','phpstorm');
        $statement = $connection->prepare("SELECT * FROM user");
        $statement->execute();

        return $statement->fetchAll();

    }

    static public function get($id) : string
    {

        $connection = new PDO('mysql:host=localhost;dbname=cloud_storage;charset=utf8', 'phpstorm','phpstorm');
        $statement = $connection->prepare("SELECT * FROM user WHERE id = ?");
        $statement->execute([$id]);

        return json_encode($statement->fetch());

    }

    static public function add($post) : string
    {

        if (isset($post['email']) && !empty($post['email'])) {

            if (isset($post['password']) && !empty($post['password'])) {

                $hash = password_hash($post['password'], PASSWORD_BCRYPT);

                $connection = new PDO('mysql:host=localhost;dbname=cloud_storage;charset=utf8', 'phpstorm','phpstorm');
                $statement = $connection->prepare("INSERT INTO user (email, hash) VALUES (?, ?)");
                $statement->execute([$post['email'], $hash]);

                return 'Пользователь добавлен';

            } else {

                return 'Не задан пароль';

            }

        } else {

            return 'Не задан e-mail пользователя';

        }

    }

    static public function update($put) : string
    {

        if (isset($put['id']) && !empty($put['id'])) {

            $id = $put['id'];

            $connection = new PDO('mysql:host=localhost;dbname=cloud_storage;charset=utf8', 'phpstorm','phpstorm');
            $statement = $connection->prepare("SELECT * FROM user WHERE id = ?");
            $statement->execute([$id]);
            $result = $statement->fetch();

            if ($result != false) {

                $statusString = '';

                if (isset($put['email']) && !empty($put['email'])) {

                    $statement = $connection->prepare("UPDATE user SET email = ? WHERE id = ?");
                    $statement->execute([$put['email'], $id]);

                    $statusString .= 'Email обоновлен. ';

                }

                if (isset($put['password']) && !empty($put['password'])) {

                    $hash = password_hash($put['password'], PASSWORD_BCRYPT);

                    $statement = $connection->prepare("UPDATE user SET hash = ? WHERE id = ?");
                    $statement->execute([$hash, $id]);

                    $statusString .= 'Пароль обоновлен. ';

                }

                if (isset($put['admin']) && !empty($put['admin'])) {

                    $isAdmin = $put['admin'];

                    if (($isAdmin == 0) || ($isAdmin == 1)) {

                        $statement = $connection->prepare("UPDATE user SET admin = ? WHERE id = ?");
                        $statement->execute([$isAdmin, $id]);

                        $statusString .= 'Роль обновлена.';

                    } else {

                        $statusString .= 'Не корректно задана роль пользователя';

                    }

                }

                if ($statusString == '') {

                    return 'Нет данных для обновления пользователя';

                } else {

                    return $statusString;

                }

            } else {

                return 'Пользователь с указанным id не найден';

            }

        } else {

            return 'Не указан id пользователя';

        }

    }

    static public function delete($id) : string
    {

        $connection = new PDO('mysql:host=localhost;dbname=cloud_storage;charset=utf8', 'phpstorm','phpstorm');
        $statement = $connection->prepare("SELECT * FROM user WHERE id = ?");
        $statement->execute([$id]);
        $result = $statement->fetch();

        if ($result != false) {

            $statement = $connection->prepare("DELETE FROM user WHERE id = ?");
            $statement->execute([$id]);

            return 'Пользователь удален';

        } else {

            return 'Не найден пользователь, которого вы хотите удалить';

        }

    }

    static function login($get) : string
    {

        if (isset($get['email']) && !empty($get['email'])) {

            $connection = new PDO('mysql:host=localhost;dbname=cloud_storage;charset=utf8', 'phpstorm','phpstorm');
            $statement = $connection->prepare("SELECT hash FROM user WHERE email = ?");
            $statement->execute([$get['email']]);
            $result = $statement->fetch();

            if ($result != false) {

                if (isset($get['password']) && !empty($get['password'])) {

                    if (password_verify($get['password'], $result['hash'])) {

                        session_start();

                        return 'Вы авторизованы';

                    } else {

                        return 'Указан неверный пароль';

                    }

                } else {

                    return 'Пароль пользователя не задан';

                }

            } else {

                return 'Пользователь с указанным email не задан';

            }

        } else {

            return 'Email пользователя не задан';

        }

    }

    static function logout()
    {

        if (isset($_COOKIE['PHPSESSID'])) {

            foreach ($_COOKIE as $key => $cookie) {

                setcookie($key, '', time()-1000);
                setcookie($key, '', time()-1000, '/');

            }

            header('Location: /');

        } else {

            return 'Вы не авторизованы';

        }

    }

    static function resetPassword($get)
    {

        if (isset($get['email']) && !empty($get['email'])) {

            $email = $get['email'];

            $connection = new PDO('mysql:host=localhost;dbname=cloud_storage;charset=utf8', 'phpstorm','phpstorm');
            $statement = $connection->prepare("SELECT id FROM user WHERE email = ?");
            $statement->execute([$email]);
            $result = $statement->fetch();

            if ($result != false) {

                $code = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 24);
                $statement = $connection->prepare("INSERT INTO reset_password_code(code, user_id) VALUES (?, ?)");
                $statement->execute([$code, $result['id']]);

                require_once './phpmailer/send.php';
                $letterBody = 'Для восстановления пароля перейдите по <a href="http://' . $_SERVER['SERVER_NAME'] . '/reset_password_form.php?code=' . $code . '">' . 'ссылке</a>.';
                sendEmail($email, 'Восстановление пароля', $letterBody);

                return 'На вашу почту отправлено письмо со ссылкой на восстановление пароля';

            } else {

                return 'Пользователь с указанным email не найден';

            }

        }

    }

}