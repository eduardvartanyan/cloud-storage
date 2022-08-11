<?php

require_once './config.php';

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
    'admin/user/' => [
        'GET' => 'Admin::get',
        'PUT' => 'Admin::update',
        'DELETE' => 'Admin::delete'
    ],
    'login/' => [
        'GET' => 'User::login'
    ],
    'logout/' => [
        'GET'=> 'User::logout'
    ],
    'reset_password/' => [
        'GET' => 'User::resetPassword'
    ],
    'user/' => [
        'GET' => 'User::list',
        'POST' => 'User::add',
        'PUT' => 'User::update',
        'DELETE' => 'User::delete'
    ],
    'users/' => [
        'GET' => 'User::get'
    ],
];

$isValidRequest = false;

if (isset($_GET) && !empty($_GET)) {

    $keysGetArray = array_keys($_GET);
    $requestMethod = $_SERVER['REQUEST_METHOD'];
    $func = 'Метод не найден';

    foreach ($urlList as $key => $url) {

        $patternWithId = '/^(' . str_replace('/', '\/', $key) . ')[0-9]\/$/';
        $patternWithoutId = '/^(' . str_replace('/', '\/', $key) . ')$/';

        if (isset($url[$requestMethod])) {

            if (preg_match($patternWithId, $keysGetArray[0])) {

                $isValidRequest = true;

                $func = $url[$requestMethod];
                $urlArray = explode('/', $keysGetArray[0]);
                $id = $urlArray[count($urlArray) - 2];
                parse_str(file_get_contents('php://input'), $_PUT);

                var_dump($func(array_merge($_GET, $_PUT), $id));

            } elseif (preg_match($patternWithoutId, $keysGetArray[0])) {

                $isValidRequest = true;

                $func = $url[$requestMethod];
                parse_str(file_get_contents('php://input'), $_PUT);

                var_dump($func(array_merge($_GET, $_PUT)));

            }

        }

    }

}

if (!$isValidRequest) {

    http_response_code(405);
    var_dump(false);

}

//END Роутинг -------------------------------------------------