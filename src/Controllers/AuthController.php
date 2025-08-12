<?php
namespace Src\Controllers;

use Src\Service\AuthService;

class AuthController {
    private $authService;

    public function __construct() {
        $this->authService = new AuthService();
    }

    public function showLogin() {
        include __DIR__ . '/../Views/auth/login.php';
    }

    public function showSignin() {
        include __DIR__ . '/../Views/auth/signin.php';
    }

    public function handleLogin() {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        if ($this->authService->login($email, $password)) {
            $role = $_SESSION['user']['role'];
            if ($role === 'freelancer') {
                header("Location: /SkillConnect/index.php/dashboard");
            } else {
                header("Location: /SkillConnect/index.php/client");
            }
        }
    }

    public function handleSignin() {
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'freelancer';

        if ($this->authService->register($name, $email, $password, $role)) {
            header("Location: /SkillConnect/index.php/login");
        } else {
            echo "User already exists or registration failed.";
        }
    }


    public function welcomePage(array $params) {
        include __DIR__ . '/../Views/auth/welcomePage.php';
    }

}
