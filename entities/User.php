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

    static public function add()
    {

        echo 'Пользователь добавлен';

    }

    static public function update()
    {

        echo 'Пользователь обновлен';

    }

    static public function delete()
    {

        echo 'Пользователь удален';

    }

    static function login()
    {

        echo 'Логин';

    }

    static function logout()
    {

        echo 'Выход';

    }

    static function resetPassword()
    {

        echo 'Сброс пароля';

    }

}