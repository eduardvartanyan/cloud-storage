<?php

//START Автозагрузка классов ----------------------------------

function loaderEntities($className)
{

    if(file_exists('entities/' . $className . '.php')) {

        require_once 'entities/' . $className . '.php';

    }

}

spl_autoload_register('loaderEntities');

//END Автозагрузка классов ------------------------------------

//START Роутинг -----------------------------------------------

$urlList = [
    'user' => [
        'GET' => 'User::list',
        'POST' => 'User::add',
        'PUT' => 'User::update',
        'DELETE' => 'User::delete'
    ],
    'users' => [
        'GET' => 'User::get'
    ],
    'login' => [
        'GET' => 'User::login'
    ],
    'logout' => [
        'GET'=> 'User::logout'
    ],
    'reset_password' => [
        'GET' => 'User::resetPassword'
    ]
];

if (isset($_GET) && !empty($_GET)) {

    $keysGetArray = array_keys($_GET);
    $urlArray = explode('/', $keysGetArray[0]);

    if (isset($urlList[$urlArray['0']][$_SERVER['REQUEST_METHOD']]) && !empty($urlList[$urlArray['0']][$_SERVER['REQUEST_METHOD']])) {

        $func = $urlList[$urlArray['0']][$_SERVER['REQUEST_METHOD']];

        if (count($urlArray) == 1) {

            var_dump($func());

        } else {

            var_dump($func($urlArray['1']));

        }

    }

}

//END Роутинг -------------------------------------------------