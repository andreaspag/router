<?php

namespace App\Controllers;

class UsersController extends Controller {

    public function list() {
        $data = [];
        foreach(range(1, 10) as $index) {
           $data[] = 'Random user ['.mt_rand(1, 1000).']';     
        }
        $this->render('users', $data);
    }

    public function register() {
        $this->render('register');
    }

    public function doRegister() {
        echo 'Thank you for registering. Your name is: '.$_POST['name'];
    }

}