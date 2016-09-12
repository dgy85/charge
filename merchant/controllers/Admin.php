<?php

!defined('APPPATH') && exit('Access Denied');

class Admin extends Admin_Controller
{
    public function index()
    {
        echo 'in';
    }

    public function login()
    {
        echo 'login';
    }
}