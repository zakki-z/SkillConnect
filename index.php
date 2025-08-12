<!DOCTYPE html>
<?php
ini_set('session.gc_maxlifetime', 3600); // 1 hour
ini_set('session.cookie_lifetime', 3600); // 1 hour
ini_set('session.gc_probability', 1);
ini_set('session.gc_divisor', 100);
require_once 'vendor/autoload.php';

use Src\Router\Router;

?>

<html lang="en">
<head>
    <title>PHP Project</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.slim.min.js"></script>

    <style>
      
    </style>
</head>
<body>
<?php

/** call the router */

Router::handleRequest();
//include "src/Ser/DatabaseService.php"
?>

<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

