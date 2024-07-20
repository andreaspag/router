<?php

namespace App\Controllers;

class HelloController extends Controller {
    
    function hello($fname, $surname)
    {
       die('Hello '. $fname. ' ' . $surname);      
    }
}