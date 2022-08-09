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

    $url = $urlArray['0'];
    $requestMethod = $_SERVER['REQUEST_METHOD'];

    if (isset($urlList[$url][$requestMethod]) && !empty($urlList[$url][$requestMethod])) {

        $func = $urlList[$url][$requestMethod];

        switch ($requestMethod) {

            case 'GET':

                if (count($urlArray) == 1) {

                    var_dump($func($_GET));

                } elseif (count($urlArray) > 1) {

                    if ($urlArray[1] != '') {

                        var_dump($func($urlArray['1'], $_GET));

                    } else {

                        var_dump($func($_GET));

                    }

                }

                break;

            case 'POST':

                if (isset($_POST) && !empty($_POST)) {

                    var_dump($func($_POST));

                } else {

                    echo 'Пустой запрос';

                }

                break;

            case 'PUT':

                parse_str(file_get_contents('php://input'), $_PUT);

                if (isset($_PUT) && !empty($_PUT)) {

                    var_dump($func($_PUT));

                } else {

                    echo 'Пустой запрос';

                }

                break;

            case 'DELETE':

                if (count($urlArray) > 1) {

                    if ($urlArray[1] != '') {

                        var_dump($func($urlArray['1']));

                    }

                }

        }

    }

}

//END Роутинг -------------------------------------------------