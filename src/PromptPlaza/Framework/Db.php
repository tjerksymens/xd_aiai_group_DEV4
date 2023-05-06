<?php

namespace PromptPlaza\Framework;

abstract class Db
{
    private static $conn;

    private static function getConfig()
    {
        return parse_ini_file(dirname(dirname(dirname(__DIR__))) . '/config/config.ini');
    }


    public static function getConnection()
    {
        if (self::$conn != null) {
            return self::$conn;
        } else {
            $config = self::getConfig();
            $database = $config['database'];
            $user = $config['user'];
            $password = $config['password'];
            $host = $config['host'];

            self::$conn = new \PDO("mysql:host=$host;dbname=" . $database, $user, $password);
            return self::$conn;
        }
    }
}
