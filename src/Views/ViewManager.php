<?php

namespace Src\Views;

class ViewManager
{
     /**
      * fill required call specific template
      * @param string $templatePath
      * @param array $params
      * @return void
      */
     public static function renderView(string $templatePath, array $params = [])
     {
         foreach ($params as $key => $param) {
             $_GET[$key] = $param;
         }
 
         if (file_exists(__DIR__.'/'.$templatePath)) {
             require_once $templatePath;
         } else {
             echo sprintf(
                 '<span style="color: red">Template <b>%s</b> does not exists in <b>%s</b></span>',
                 $templatePath,
                 __DIR__
             );
         }
     }
}

