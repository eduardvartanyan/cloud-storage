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
    'check_reset_password_code/' => [
        'GET' => 'User::checkResetPasswordCode'
    ],
    'directory/' => [
        'GET' => 'File::directoryInfo',
        'POST' => 'File::addDirectory',
        'PUT' => 'File::renameDirectory',
        'DELETE' => 'File::deleteDirectory'
    ],
    'file/' => [
        'GET' => 'File::getFile',
        'POST' => 'File::addFile',
        'PUT' => 'File::updateFile',
        "DELETE" => 'File::deleteFile'
    ],
    'file/share/' => [
        'GET' => 'File::getShares',
        'PUT' => 'File::addShare',
        'DELETE' => 'File::deleteShare'
    ],
    'login/' => [
        'GET' => 'User::login'
    ],
    'logout/' => [
        'GET'=> 'User::logout'
    ],
    'reset_password/' => [
        'GET' => 'User::resetPassword',
        'PUT' => 'User::updatePassword'
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
$func = 'Метод не найден';
$requestMethod = $_SERVER['REQUEST_METHOD'];
$param = [];

if (isset($_GET) && !empty($_GET)) {

    $keysGetArray = array_keys($_GET);

    foreach ($urlList as $key => $url) {

        $patternWithId = '/^(' . str_replace('/', '\/', $key) . ')[0-9]+$/';
        $patternWithoutId = '/^(' . str_replace('/', '\/', $key) . ')$/';
        $patternWithTwoId = '/^(' . str_replace('/', '\/', $key) . ')[0-9]+\/[0-9]+$/';

        if (isset($url[$requestMethod])) {

            if (preg_match($patternWithId, $keysGetArray[0])) {

                $isValidRequest = true;

                $func = $url[$requestMethod];
                $urlArray = explode('/', $keysGetArray[0]);
                $id = $urlArray[count($urlArray) - 1];
                parse_str(file_get_contents('php://input'), $_PUT);
                $param = array_merge($_GET, $_PUT, $_FILES, array('id' => $id));

            } elseif (preg_match($patternWithoutId, $keysGetArray[0])) {

                $isValidRequest = true;

                $func = $url[$requestMethod];
                parse_str(file_get_contents('php://input'), $_PUT);
                $param = array_merge($_GET, $_PUT, $_FILES);

            } elseif (preg_match($patternWithTwoId, $keysGetArray[0])) {

                $isValidRequest = true;

                $func = $url[$requestMethod];
                $urlArray = explode('/', $keysGetArray[0]);
                $id = $urlArray[count($urlArray) - 2];
                $userId = $urlArray[count($urlArray) - 1];
                parse_str(file_get_contents('php://input'), $_PUT);
                $param = array_merge($_GET, $_PUT, $_FILES, array(
                    'id' => $id,
                    'userId' => $userId
                ));

            }

        }

    }

}

if ($isValidRequest) {

    print_r($func($param));

} else {

    http_response_code(405);
    print_r(false);

}

//END Роутинг -------------------------------------------------
