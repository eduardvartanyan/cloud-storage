<?php

class File {

    static public function getFile($param, $id = NULL)
    {

        if (isset($id) && ($id != '')) {

            $connection = new PDO('mysql:host=' . HOST . ';dbname=' . DBNAME . ';charset=utf8', USERNAME,PASSWORD);
            $statement = $connection->prepare("SELECT * FROM file WHERE id = ?");
            $statement->execute([$id]);
            $result = $statement->fetch();

            if ($result != false) {

                return json_encode($result);

            } else {

                http_response_code(403);
                return 'Файл не найден';

            }

        } else {

            $connection = new PDO('mysql:host=' . HOST . ';dbname=' . DBNAME . ';charset=utf8', USERNAME,PASSWORD);
            $statement = $connection->prepare("SELECT * FROM file");
            $statement->execute();

            return $statement->fetchAll();

        }

    }

    static public function addFile($param, $id = NULL)
    {

        if (isset($id) && ($id != '')) {

            http_response_code(405);
            return false;

        } else {

            if (isset($param['file']) && !empty($param['file']['name'])) {

                $fileName = $param['file']['name'];

                try {

                    if ($param['file']['size'] <= 2147483648) {

                        if (!file_exists('./files/')) {

                            mkdir('./files/');

                        }

                        $fileNameArray = explode('.', $fileName);
                        $fileHash = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 48);
                        $fileHash .= '.' . $fileNameArray[count($fileNameArray) - 1];
                        move_uploaded_file($param['file']['tmp_name'], './files/' . $fileHash);

                        if (isset($param['directory_id'])) {

                            $directoryId = (int) $param['directory_id'];

                        } else {

                            $directoryId = 0;

                        }

                        $connection = new PDO('mysql:host=' . HOST . ';dbname=' . DBNAME . ';charset=utf8', USERNAME,PASSWORD);
                        $statement = $connection->prepare("INSERT INTO file (name, hash, size, directory_id) VALUES (?, ?, ?, ?)");
                        $statement->execute([$fileName, $fileHash, $param['file']['size'], $directoryId]);

                        return 'Файл добавлен';

                    } else {

                        http_response_code(400);
                        return 'Размер файла превышают максимальные 2 ГБ';

                    }

                } catch (Exception $e) {

                    return 'Не удалось загрузить файл по причине: ' . $e->getMessage();

                }

            }

        }

    }

    static public function updateFile($param, $id = NULL)
    {

        if (isset($id) && ($id != '')) {

            http_response_code(405);
            return false;

        } else {

            if (isset($param['id']) && !empty($param['id'])) {

                $id = $param['id'];

                $connection = new PDO('mysql:host=' . HOST . ';dbname=' . DBNAME . ';charset=utf8', USERNAME,PASSWORD);
                $statement = $connection->prepare("SELECT * FROM file WHERE id = ?");
                $statement->execute([$id]);
                $result = $statement->fetch();

                if ($result != false) {

                    $statusString = '';

                    if (isset($param['name']) && !empty($param['name'])) {

                        $newName = $param['name'];

                        if ($result['name'] != $newName) {

                            $statement = $connection->prepare("UPDATE file SET name = ? WHERE id = ?");
                            $statement->execute([$newName, $id]);

                            $statusString .= 'Файл переименован';

                        } else {

                            $statusString .= 'Новое имя совпадает с текущим';

                        }

                    }

                    if (isset($param['directoryId']) && !empty($param['directoryId'])) {

                        $directoryId = (int) $param['directory_id'];

                        if ($result['directory_id'] != $directoryId) {

                            $statement = $connection->prepare("UPDATE file SET directory_id = ? WHERE id = ?");
                            $statement->execute([$directoryId, $id]);

                            $statusString .= 'Файл перемещен. ';

                        } else {

                            $statusString .= 'Файл уже находится в указанной папке. ';

                        }

                    }

                    if ($statusString == '') {

                        return 'Нет данных для обновления пользователя';

                    } else {

                        return $statusString;

                    }

                } else {

                    http_response_code(404);
                    return 'Файл с указанным id не найден';

                }

            } else {

                http_response_code(400);
                return 'Не указан id файла';

            }

        }

    }

    static public function deleteFile($param, $id = NULL)
    {

        if (isset($id) && ($id != '')) {

            return 'Удаление файла';

        } else {

            http_response_code(405);
            return false;

        }

    }

    static public function addDirectory($param, $id = NULL)
    {

        if (isset($id) && ($id != '')) {

            http_response_code(405);
            return false;

        } else {

            if (isset($param['name'])) {

                $nameDirectory = $param['name'];

                if ($nameDirectory != '') {

                    $connection = new PDO('mysql:host=' . HOST . ';dbname=' . DBNAME . ';charset=utf8', USERNAME,PASSWORD);
                    $statement = $connection->prepare("INSERT INTO directory (name, parent_id) VALUES (?, ?)");
                    $statement->execute([$param['name'], $param['parent']]);

                    return 'Папка добавлена';

                } else {

                    http_response_code(400);
                    return 'Имя папки не может быть пустым';

                }

            } else {

                http_response_code(400);
                return 'Не задано имя папки';

            }

        }

    }

    static public function renameDirectory($param, $id = NULL)
    {

        if (isset($id) && ($id != '')) {

            http_response_code(405);
            return false;

        } else {

            if (isset($param['id']) && !empty($param['id'])) {

                if (isset($param['name'])) {

                    $directoryNewName = $param['name'];

                    if ($directoryNewName != '') {

                        $connection = new PDO('mysql:host=' . HOST . ';dbname=' . DBNAME . ';charset=utf8', USERNAME,PASSWORD);
                        $statement = $connection->prepare("SELECT * FROM directory WHERE id = ?");
                        $statement->execute([$param['id']]);
                        $result = $statement->fetch();

                        if ($result != false) {

                            if ($result['name'] != $directoryNewName) {

                                $statement = $connection->prepare("UPDATE directory SET name = ? WHERE id = ?");
                                $statement->execute([$directoryNewName, $result['id']]);

                                return 'Папка переименована';

                            } else {

                                return 'Новое имя совпадает с текущим';

                            }

                        } else {

                            http_response_code(404);
                            return 'Папка с указанным id не найдена';

                        }

                    } else {

                        http_response_code(400);
                        return 'Имя папки не может быть пустым';

                    }

                } else {

                    http_response_code(400);
                    return 'Не задано имя папки';

                }

            } else {

                http_response_code(400);
                return 'Не указана папка для переименовыния';

            }

        }

    }

    static public function directoryInfo($param, $id = NULL)
    {

        if (isset($id) && ($id != '')) {

            $connection = new PDO('mysql:host=' . HOST . ';dbname=' . DBNAME . ';charset=utf8', USERNAME,PASSWORD);
            $statement = $connection->prepare("SELECT * FROM directory WHERE id = ?");
            $statement->execute([$id]);
            $result = $statement->fetch();

            if ($result != false) {

                $statement = $connection->prepare("SELECT * FROM file WHERE directory_id = ?");
                $statement->execute([$id]);

                return $statement->fetchAll();

            } else {

                http_response_code(404);
                return 'Папка с указанным id не найдена';

            }

        } else {

            http_response_code(405);
            return false;

        }

    }

    static public function deleteDirectory($param, $id = NULL)
    {

        if (isset($id) && ($id != '')) {

            $connection = new PDO('mysql:host=' . HOST . ';dbname=' . DBNAME . ';charset=utf8', USERNAME,PASSWORD);
            $statement = $connection->prepare("SELECT * FROM directory WHERE id = ?");
            $statement->execute([$id]);
            $result = $statement->fetch();

            if ($result != false) {

                $statement = $connection->prepare("DELETE FROM directory WHERE id = ?");
                $statement->execute([$id]);

                return 'Папка удалена';

            } else {

                http_response_code(404);
                return 'Папка с указанным id не найдена';

            }

        } else {

            http_response_code(405);
            return false;

        }

    }

    static public function getShares($param)
    {

        if (isset($param['id']) && ($param['id'] != '') && !isset($param['userId'])) {

            $id = $param['id'];

            $connection = new PDO('mysql:host=' . HOST . ';dbname=' . DBNAME . ';charset=utf8', USERNAME,PASSWORD);
            $statement = $connection->prepare("SELECT * FROM file WHERE id = ?");
            $statement->execute([$id]);
            $result = $statement->fetch();

            if ($result != false) {

                $statement = $connection->prepare("SELECT user_id FROM file_user WHERE file_id = ?");
                $statement->execute([$id]);
                return $statement->fetchAll();

            } else {

                http_response_code(404);
                return 'Файл не найден';

            }

        } else {

            http_response_code(405);
            return false;

        }

    }

    static public function addShare($param)
    {

        if (isset($param['id']) && ($param['id'] != '') && isset($param['userId']) && ($param['userId'] != '')) {

            $id = $param['id'];
            $userId = $param['userId'];

            $connection = new PDO('mysql:host=' . HOST . ';dbname=' . DBNAME . ';charset=utf8', USERNAME,PASSWORD);
            $statement = $connection->prepare("SELECT * FROM file WHERE id = ?");
            $statement->execute([$id]);
            $result = $statement->fetch();

            if ($result != false) {

                $statement = $connection->prepare("SELECT * FROM user WHERE id = ?");
                $statement->execute([$userId]);
                $result = $statement->fetch();

                if ($result != false) {

                    $statement = $connection->prepare("INSERT INTO file_user (file_id, user_id) VALUES (?, ?)");
                    $statement->execute([$id, $userId]);

                    return 'Доступ к файлу открыт';

                } else {

                    http_response_code(404);
                    return 'Пользователь не найден';

                }

            } else {

                http_response_code(404);
                return 'Файл не найден';

            }

        } else {

            http_response_code(405);
            return false;

        }

    }

    static public function deleteShare($param)
    {

        if (isset($param['id']) && ($param['id'] != '') && isset($param['userId']) && ($param['userId'] != '')) {

            $id = $param['id'];
            $userId = $param['userId'];

            $connection = new PDO('mysql:host=' . HOST . ';dbname=' . DBNAME . ';charset=utf8', USERNAME,PASSWORD);
            $statement = $connection->prepare("SELECT * FROM file WHERE id = ?");
            $statement->execute([$id]);
            $result = $statement->fetch();

            if ($result != false) {

                $statement = $connection->prepare("SELECT * FROM user WHERE id = ?");
                $statement->execute([$userId]);
                $result = $statement->fetch();

                if ($result != false) {

                    $statement = $connection->prepare("SELECT * FROM file_user WHERE user_id = ? AND file_id = ?");
                    $statement->execute([$id, $userId]);
                    $result = $statement->fetch();

                    if ($result != false) {

                        $statement = $connection->prepare("DELETE FROM file_user WHERE user_id = ? AND file_id = ?");
                        $statement->execute([$id, $userId]);

                        return 'Доступ к файлу закрыт';

                    } else {

                        return 'Пользователь и так не имел доступ к файлу';

                    }

                } else {

                    http_response_code(404);
                    return 'Пользователь не найден';

                }

            } else {

                http_response_code(404);
                return 'Файл не найден';

            }

        } else {

            http_response_code(405);
            return false;

        }

    }

}
