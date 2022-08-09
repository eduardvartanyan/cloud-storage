<?php

class Admin {

    static public function get($id = NULL)
    {

        if (isset($id)) {

            return 'Информация о пользователе ' . $id;

        } else {

            return 'Список пользователей';

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