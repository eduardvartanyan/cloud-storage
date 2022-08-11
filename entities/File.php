<?php

class File {

    static public function getFile($param, $id = NULL)
    {

        if (isset($id) && !empty($id)) {

            return 'Информация о файле';

        } else {

            return 'Список файлов';

        }

    }

    static public function addFile($param, $id = NULL)
    {

        if (isset($id) && !empty($id)) {

            http_response_code(405);
            return false;

        } else {

            return 'Добавление файла';

        }

    }

    static public function editFile($param, $id = NULL)
    {

        if (isset($id) && !empty($id)) {

            http_response_code(405);
            return false;

        } else {

            return 'Изменение файла';

        }

    }

    static public function deleteFile($param, $id = NULL)
    {

        if (isset($id) && !empty($id)) {

            return 'Удаление файла';

        } else {

            http_response_code(405);
            return false;

        }

    }

    static public function addDirectory($param, $id = NULL)
    {

        if (isset($id) && !empty($id)) {

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

        if (isset($id) && !empty($id)) {

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

        if (isset($id) && !empty($id)) {

            return 'Содержимое папки';

        } else {

            http_response_code(405);
            return false;

        }

    }

    static public function deleteDirectory($param, $id = NULL)
    {

        if (isset($id) && !empty($id)) {

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

}
