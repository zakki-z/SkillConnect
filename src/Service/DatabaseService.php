<?php
namespace Src\Service;

use PDO;

class DatabaseService {
    private static $pdo = null;

    public static function getConnection() {
        if (self::$pdo === null) {
            self::$pdo = new PDO('pgsql:host=localhost;port=5432;dbname=freelancedb', 'zakiazhari', '', [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
        }
        return self::$pdo;
    }
}
