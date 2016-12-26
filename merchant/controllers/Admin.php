<?php

!defined('APPPATH') && exit('Access Denied');

class Admin extends Admin_Controller
{
    public function index()
    {

    }

    public function login()
    {
        $this->output('login',array('css'=>'login'));
    }
}