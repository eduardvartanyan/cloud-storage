<?php

class Admin {

    static public function get($id = NULL)
    {

        if (isset($_COOKIE['PHPSESSION']) && !empty($_COOKIE['PHPSESSION'])) {

            $connection = new PDO('mysql:host=localhost;dbname=cloud_storage;charset=utf8', 'phpstorm','phpstorm');
            $statement = $connection->prepare("SELECT user_id FROM session WHERE session = ?");
            $statement->execute([$_COOKIE['PHPSESSION']]);
            $result = $statement->fetch();

            if ($result != false) {

                if (isset($id)) {

                    return 'Информация о пользователе ' . $id;

                } else {

                    return 'Список пользователей';

                }

            } else {

                http_response_code(401);
                return 'Доступ запрещен';

            }

        } else {

            http_response_code(401);
            return 'Доступ запрещен';

        }

    }

    static public function update($put = NULL)
    {

        return 'Обновление пользователя';

    }

    static public function delete($id = NULL)
    {

        return 'Удаление пользователя';

    }

}