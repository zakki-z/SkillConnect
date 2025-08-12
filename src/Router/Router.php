<?php
// Router.php
namespace Src\Router;

use Src\Controllers\AuthController;
use Src\Controllers\ClientController;
use Src\Controllers\FreelancerController;

class Router {
    private const ROUTES = [
        '#^/dashboard$#' => [FreelancerController::class, 'dashboard'],
        '#^/accept-project/(\d+)$#' => [FreelancerController::class, 'acceptProject'],
        '#^/reject-project/(\d+)$#' => [FreelancerController::class, 'rejectProject'],
        '#^/finish-project/(\d+)$#' => [FreelancerController::class, 'finishProject'],
        '#^/cancel-project/(\d+)$#' => [FreelancerController::class, 'cancelProject'],
        // Client routes
        '#^/client$#' => [ClientController::class, 'client'],
        '#^/add-project$#' => [ClientController::class, 'addProject'],
        '#^/update-project-status$#' => [ClientController::class, 'updateProjectStatus'],

        //to handle the Auth
        '#^/login$#' => [AuthController::class, 'showLogin'],
        '#^/signin$#' => [AuthController::class, 'showSignin'],
        '#^/submit-login$#' => [AuthController::class, 'handleLogin'],
        '#^/submit-signin$#' => [AuthController::class, 'handleSignin'],
        '#^/welcome$#' => [AuthController::class, 'welcomePage'],
        '#^/$#' => [AuthController::class, 'welcomePage'],
    ];

    public static function handleRequest()
    {
        // Get the current path without query parameters
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Get the base directory (e.g., /SkillConnect)
        $baseDir = dirname($_SERVER['SCRIPT_NAME']); // /FreelancerPlatform
        $baseDir = rtrim($baseDir, '/');

        // Remove /FreelancerPlatform/index.php or /FreelancerPlatform from $uri
        $uri = preg_replace("#^$baseDir(/index\.php)?#", '', $uri);

        if ($uri === '' || $uri === false) {
            $uri = '/';
        }

        foreach (self::ROUTES as $pattern => $action) {
            if (preg_match($pattern, $uri, $params)) {
                $controller = new $action[0];
                $controller->{$action[1]}(array_slice($params, 1));
                return;
            }
        }

        http_response_code(404);
        echo "Not Found: No route matched for '$uri'";
    }
}
