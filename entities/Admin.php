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

            $methodUrl = 'http://' . $_SERVER['HTTP_HOST'];

            if (isset($id)) {

                $methodUrl .= '/users/' . $id;

            } else {

                $methodUrl .= '/user/';

            }

            $ch = curl_init($methodUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HEADER, false);
            $response = curl_exec($ch);
            curl_close($ch);

            return $response;

        } else {

            http_response_code($checkResult['code']);
            return $checkResult['message'];

        }

    }

    static public function update($param, $id = NULL)
    {

        $checkResult = self::checkAccess();

        if ($checkResult['code'] == 200) {

            if (isset($id)) {

                http_response_code(405);
                return false;

            } else {

                $methodUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/user/';
                $ch = curl_init($methodUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_HEADER, false);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($param));
                $response = curl_exec($ch);
                $info = curl_getinfo($ch);
                curl_close($ch);

                http_response_code($info['http_code']);
                return $response;

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

                $methodUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/user/' . $id;
                $ch = curl_init($methodUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_HEADER, false);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                $response = curl_exec($ch);
                $info = curl_getinfo($ch);
                curl_close($ch);

                http_response_code($info['http_code']);
                return $response;

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