<?php

class User {

    static public function list($param)
    {

        if (!isset($param['id']) && !isset($param['userId'])) {

            $connection = new PDO('mysql:host=' . HOST . ';dbname=' . DBNAME . ';charset=utf8', USERNAME,PASSWORD);
            $statement = $connection->prepare("SELECT * FROM user");
            $statement->execute();

            return $statement->fetchAll();

        } else {

            http_response_code(405);
            return false;

        }

    }

    static public function get($param)
    {

        if (isset($param['id']) && ($param['id'] != '') && !isset($param['userId'])) {

            $connection = new PDO('mysql:host=' . HOST . ';dbname=' . DBNAME . ';charset=utf8', USERNAME,PASSWORD);
            $statement = $connection->prepare("SELECT * FROM user WHERE id = ?");
            $statement->execute([$param['id']]);
            $result = $statement->fetch();

            if ($result != false) {

                return json_encode($result);

            } else {

                http_response_code(403);
                return 'Пользователь с указанным id не найден';

            }

        } else {

            http_response_code(405);
            return false;

        }

    }

    static public function add($param)
    {

        if (!isset($param['id']) && !isset($param['userId'])) {

            if (isset($param['email']) && !empty($param['email'])) {

                if (isset($param['password']) && !empty($param['password'])) {

                    $email = $param['email'];

                    $connection = new PDO('mysql:host=' . HOST . ';dbname=' . DBNAME . ';charset=utf8', USERNAME,PASSWORD);
                    $statement = $connection->prepare("SELECT id FROM user WHERE email = ?");
                    $statement->execute([$email]);
                    $result = $statement->fetch();

                    if (($result == false)) {

                        $hash = password_hash($param['password'], PASSWORD_BCRYPT);

                        $statement = $connection->prepare("INSERT INTO user (email, hash) VALUES (?, ?)");
                        $statement->execute([$email, $hash]);

                        return 'Пользователь добавлен';

                    } else {

                        http_response_code(400);
                        return 'Пользователь с указанным email уже существует';

                    }

                } else {

                    http_response_code(400);
                    return 'Не задан пароль';

                }

            } else {

                http_response_code(400);
                return 'Не задан e-mail пользователя';

            }

        } else {

            http_response_code(405);
            return false;

        }

    }

    static public function update($param)
    {

        if (!isset($param['id']) && !isset($param['userId'])) {

            if (isset($param['user']) && !empty($param['user'])) {

                $id = $param['user'];

                $connection = new PDO('mysql:host=' . HOST . ';dbname=' . DBNAME . ';charset=utf8', USERNAME,PASSWORD);
                $statement = $connection->prepare("SELECT * FROM user WHERE id = ?");
                $statement->execute([$id]);
                $result = $statement->fetch();

                if ($result != false) {

                    $statusString = '';

                    if (isset($param['email']) && !empty($param['email'])) {

                        $email = $param['email'];

                        $statement = $connection->prepare("SELECT id FROM user WHERE email = ?");
                        $statement->execute([$email]);
                        $result = $statement->fetch();

                        if (($result == false) || ($result['id'] == $id)) {

                            $statement = $connection->prepare("UPDATE user SET email = ? WHERE id = ?");
                            $statement->execute([$email, $id]);

                            $statusString .= 'Email обоновлен. ';

                        } else {

                            http_response_code(400);
                            return 'Пользователь с указанным email уже существует';

                        }

                    }

                    if (isset($param['password']) && !empty($param['password'])) {

                        $hash = password_hash($param['password'], PASSWORD_BCRYPT);

                        $statement = $connection->prepare("UPDATE user SET hash = ? WHERE id = ?");
                        $statement->execute([$hash, $id]);

                        $statusString .= 'Пароль обоновлен. ';

                    }

                    if (isset($param['admin']) && !empty($param['admin'])) {

                        $isAdmin = $param['admin'];

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

                    http_response_code(404);
                    return 'Пользователь с указанным id не найден';

                }

            } else {

                http_response_code(400);
                return 'Не указан id пользователя';

            }

        } else {

            http_response_code(405);
            return false;

        }

    }

    static public function delete($param)
    {

        if (isset($param['id']) && ($param['id'] != '') && !isset($param['userId'])) {

            $id = $param['id'];

            $connection = new PDO('mysql:host=' . HOST . ';dbname=' . DBNAME . ';charset=utf8', USERNAME,PASSWORD);
            $statement = $connection->prepare("SELECT * FROM user WHERE id = ?");
            $statement->execute([$id]);
            $result = $statement->fetch();

            if ($result != false) {

                $statement = $connection->prepare("DELETE FROM user WHERE id = ?");
                $statement->execute([$id]);

                return 'Пользователь удален';

            } else {

                http_response_code(404);
                return 'Не найден пользователь, которого вы хотите удалить';

            }


        } else {

            http_response_code(405);
            return false;

        }

    }

    static function login($param)
    {

        if (!isset($param['id']) && !isset($param['userId'])) {

            if (isset($_COOKIE['PHPSESSID']) && !empty($_COOKIE['PHPSESSID'])) {

                $connection = new PDO('mysql:host=' . HOST . ';dbname=' . DBNAME . ';charset=utf8', USERNAME,PASSWORD);
                $statement = $connection->prepare("SELECT * FROM session WHERE session = ?");
                $statement->execute([$_COOKIE['PHPSESSID']]);
                $session = $statement->fetch();

                if ($session != false) {

                    return 'Вы уже авторизованы';

                } else {

                    foreach ($_COOKIE as $key => $cookie) {

                        setcookie($key, '', time()-1000);
                        setcookie($key, '', time()-1000, '/');

                    }

                }

            }

            if (isset($param['email']) && !empty($param['email'])) {

                $connection = new PDO('mysql:host=' . HOST . ';dbname=' . DBNAME . ';charset=utf8', USERNAME,PASSWORD);
                $statement = $connection->prepare("SELECT * FROM user WHERE email = ?");
                $statement->execute([$param['email']]);
                $result = $statement->fetch();

                if ($result != false) {

                    if (isset($param['password']) && !empty($param['password'])) {

                        if (password_verify($param['password'], $result['hash'])) {

                            session_start();

                            $statement = $connection->prepare("INSERT INTO session (session, user_id) VALUES (?, ?)");
                            $statement->execute([session_id(), $result['id']]);

                            return 'Вы авторизованы';

                        } else {

                            http_response_code(401);
                            return 'Указан неверный пароль';

                        }

                    } else {

                        http_response_code(400);
                        return 'Пароль пользователя не задан';

                    }

                } else {

                    http_response_code(401);
                    return 'Пользователь с указанным email не найден';

                }

            } else {

                http_response_code(400);
                return 'Email пользователя не задан';

            }
            
        } else {

            http_response_code(405);
            return false;
            
        }

    }

    static function logout($param)
    {

        if (!isset($param['id']) && !isset($param['userId'])) {

            if (isset($_COOKIE['PHPSESSID'])) {

                $connection = new PDO('mysql:host=' . HOST . ';dbname=' . DBNAME . ';charset=utf8', USERNAME,PASSWORD);
                $statement = $connection->prepare('DELETE FROM session WHERE session = ?');
                $statement->execute([$_COOKIE['PHPSESSID']]);

                foreach ($_COOKIE as $key => $cookie) {

                    setcookie($key, '', time()-1000);
                    setcookie($key, '', time()-1000, '/');

                }

                return 'До новых встреч';

            } else {

                return 'Вы не авторизованы';

            }

        } else {

            http_response_code(405);
            return false;

        }

    }

    static function resetPassword($param)
    {

        if (!isset($param['id']) && !isset($param['userId'])) {

            if (isset($param['email']) && !empty($param['email'])) {

                $email = $param['email'];

                $connection = new PDO('mysql:host=' . HOST . ';dbname=' . DBNAME . ';charset=utf8', USERNAME,PASSWORD);
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

                    http_response_code(401);
                    return 'Пользователь с указанным email не найден';

                }

            }

        } else {

            http_response_code(405);
            return false;

        }

    }

    static public function updatePassword($param) {

        if (!isset($param['id']) && !isset($param['userId'])) {

            $code = $param['code'];

            $connection = new PDO('mysql:host=' . HOST . ';dbname=' . DBNAME . ';charset=utf8', USERNAME,PASSWORD);
            $statement = $connection->prepare("SELECT user_id FROM reset_password_code WHERE code = ?");
            $statement->execute([$code]);
            $result = $statement->fetch();

            if ($result != false) {

                $userId = $result['user_id'];
                $hash = password_hash($param['password'], PASSWORD_BCRYPT);

                $statement = $connection->prepare("UPDATE user SET hash = ? WHERE id = ?");
                $statement->execute([$hash, $userId]);

                $statement = $connection->prepare("DELETE FROM reset_password_code WHERE code = ?");
                $statement->execute([$code]);

                $statement = $connection->prepare('DELETE FROM session WHERE user_id = ?');
                $statement->execute([$userId]);

                foreach ($_COOKIE as $key => $cookie) {

                    setcookie($key, '', time()-1000);
                    setcookie($key, '', time()-1000, '/');

                }

                return 'Новый пароль успешно установлен';

            } else {

                http_response_code(400);
                return 'Что-то пошло не так. Перейдите еще раз по ссылке в письме.';

            }

        } else {

            http_response_code(405);
            return false;

        }

    }

    static public function checkResetPasswordCode($param)
    {

        $connection = new PDO('mysql:host=localhost;dbname=cloud_storage;charset=utf8', 'phpstorm','phpstorm');
        $statement = $connection->prepare("SELECT user_id FROM reset_password_code WHERE code = ?");
        $statement->execute([$_GET['code']]);
        $result = $statement->fetch();

        if ($result != false) {

            return true;

        } else {

            return false;

        }

    }

}
