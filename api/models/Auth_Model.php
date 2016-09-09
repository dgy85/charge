<?php
!defined('APPPATH') && exit('Access Denied');

class Auth_Model extends R_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 客户端权限校验
     * @param array $Auth 0=>client_id 1=>secret
     * @return bool
     */
    public function verify(array $Auth)
    {
        if(!checkStack('R_Controller','class')){
            return false;
        }

        return  true;
    }
}