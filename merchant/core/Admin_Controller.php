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

    /**
     * 页面输出
     * @param string $view  视图文件
     * @param array $pageArgs  页面数据
     * @param string $layout 布局文件
     */
    protected function output($view,$pageArgs = array(),$layout='')
    {
        if($layout){
            $html = $this->load->view($view,$pageArgs,true);
            $pageArgs=array_merge(array('html'=>$html),$pageArgs);
            $view = $layout;
        }
        $this->load->view($view,$pageArgs);
    }
}