<?php

class Admin {

    static private function checkAccess()
    {

        if (isset($_COOKIE['PHPSESSID']) && !empty($_COOKIE['PHPSESSID'])) {

            $connection = new PDO('mysql:host=' . HOST . ';dbname=' . DBNAME . ';charset=utf8', USERNAME,PASSWORD);
            $statement = $connection->prepare("SELECT user_id FROM session WHERE session = ?");
            $statement->execute([$_COOKIE['PHPSESSID']]);
            $result = $statement->fetch();

            if ($result != false) {

                $userId = $result['user_id'];

                $statement = $connection->prepare("SELECT admin FROM user WHERE id = ?");
                $statement->execute([$userId]);
                $result = $statement->fetch();
                $isAdmin = (bool) $result['admin'];

                if ($isAdmin) {

                    return array('code' => 200, 'message' => 'Доступ разрешен');

                } else {

                    return array('code' => 403, 'message' => 'Доступ запрещен');

                }

            } else {

                return array('code' => 401, 'message' => 'Доступ запрещен');

            }

        } else {

            return array('code' => 401, 'message' => 'Доступ запрещен');

        }

    }

    static public function get($param, $id = NULL)
    {

        $checkResult = self::checkAccess();

        if ($checkResult['code'] == 200) {

            if (isset($id)) {

                $connection = new PDO('mysql:host=' . HOST . ';dbname=' . DBNAME . ';charset=utf8', USERNAME,PASSWORD);
                $statement = $connection->prepare("SELECT * FROM user WHERE id = ?");
                $statement->execute([$id]);

                return json_encode($statement->fetch());

            } else {

                $connection = new PDO('mysql:host=' . HOST . ';dbname=' . DBNAME . ';charset=utf8', USERNAME,PASSWORD);
                $statement = $connection->prepare("SELECT * FROM user");
                $statement->execute();

                return $statement->fetchAll();

            }

        } else {

            http_response_code($checkResult['code']);
            return $checkResult['message'];

        }

    }

    static public function update($param, $id = NULL)
    {

        $checkResult = self::checkAccess();

        if ($checkResult['code'] == 200) {

            if (isset($id) && !empty($id)) {

                http_response_code(405);
                return false;

            } else {

                if (isset($param['id']) && !empty($param['id'])) {

                    $id = $param['id'];

                    $connection = new PDO('mysql:host=' . HOST . ';dbname=' . DBNAME . ';charset=utf8', USERNAME,PASSWORD);
                    $statement = $connection->prepare("SELECT * FROM user WHERE id = ?");
                    $statement->execute([$id]);
                    $result = $statement->fetch();

                    if ($result != false) {

                        $statusString = '';

                        if (isset($param['email']) && !empty($param['email'])) {

                            $statement = $connection->prepare("UPDATE user SET email = ? WHERE id = ?");
                            $statement->execute([$param['email'], $id]);

                            $statusString .= 'Email обоновлен. ';

                        }

                        if (isset($param['password']) && !empty($param['password'])) {

                            $hash = password_hash($param['password'], PASSWORD_BCRYPT);

                            $statement = $connection->prepare("UPDATE user SET hash = ? WHERE id = ?");
                            $statement->execute([$hash, $id]);

                            $statusString .= 'Пароль обоновлен. ';

                        }

                        if (isset($param['admin'])) {

                            $admin = $param['admin'];

                            if (($admin == 0) || ($admin == 1)) {

                                $statement = $connection->prepare("UPDATE user SET admin = ? WHERE id = ?");
                                $statement->execute([$admin, $id]);

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

            }

        } else {

            http_response_code($checkResult['code']);
            return $checkResult['message'];

        }

    }

    static public function delete($param, $id = NULL)
    {

        $checkResult = self::checkAccess();

        if ($checkResult['code'] == 200) {

            if (isset($id) && !empty($id)) {

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

        } else {

            http_response_code($checkResult['code']);
            return $checkResult['message'];

        }

    }

}