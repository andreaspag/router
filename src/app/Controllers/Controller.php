<?php

namespace App\Controllers;

class Controller {
    protected function render($fileName, $data = []) {
        $view = "{$fileName}.tpl.php";
        extract($data);
        include(VIEW_PATH . DIRECTORY_SEPARATOR . $view);
    }
}