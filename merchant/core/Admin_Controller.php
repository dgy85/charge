<?php
!defined('APPPATH')  && exit('Access Denied');

class Admin_Controller extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if(!self::_checkLogin() && $this->uri->rsegments[2]!='login'){
            redirect('/admin/login');
        }
    }

    protected function _checkLogin()
    {
        return false;
    }

    protected function loadView()
    {

    }
}