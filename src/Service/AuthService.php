<?php
namespace Src\Service;

use Src\Models\userModel;

class AuthService {
    private $userModel;

    public function __construct() {
        $this->userModel = new userModel();
    }

    public function login(string $email, string $password): bool {
        $user = $this->userModel->getUserByEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['user'] = $user;
            return true;
        }
        return false;
    }

    public function register(string $name, string $email, string $password, string $role): bool {
        if ($this->userModel->getUserByEmail($email)) {
            return false;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        return $this->userModel->createUser($name, $email, $hashedPassword, $role);
    }

}
