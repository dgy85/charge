<?php

if (!function_exists('tpl')) {
    /**
     * 模版搜索调用方法
     * 根据传入的文件名称，从调用的文件上级开始逐层往下查找模版文件的位置
     * @author Ding
     * @param string $viewFile 加载的模版文件
     * @param string $tplfolder 放置模版的文件夹
     */
    function tpl($viewFile, $vars = array() , $tplfolder = 'tpl')
    {
        $fileInfo = pathinfo($viewFile);//文件分析
        $stack = debug_backtrace();//调用栈
        $pathArr = explode(DIRECTORY_SEPARATOR, dirname($stack[0]['file']));//文件路径
        $viewIdx = array_keys($pathArr, 'views');

        if (empty($viewIdx)) {
            show_error('只能从视图页面调用模板函数！', 503, '调用错误');
        }
        $viewIdx = $viewIdx[0];
        $viewpath = implode(DIRECTORY_SEPARATOR , array_slice($pathArr,0,$viewIdx)) . DIRECTORY_SEPARATOR;
        $pathLength = sizeof($pathArr);
        $tplExists = false;
        if (isset($fileInfo['extension'])) {
            $ext = '.' . $fileInfo['extension'];
        } else {
            $ext = '.php';
        }

        //搜索tpl文件夹的位置
        while ($viewIdx < $pathLength - 1 || !$tplExists) {
            $viewpath .= $pathArr[$viewIdx++] . DIRECTORY_SEPARATOR;

            if (is_dir($viewpath . $tplfolder)) {
                $tplExists = true;
                $viewpath .= $tplfolder . DIRECTORY_SEPARATOR;
                break;
            }
            ($viewIdx == $pathLength) && ($tplExists = true);
        }

        if (!$tplExists) {
            show_error('模版目录不存在！', 200, '错误');
        }
        $viewFile = $viewpath . $viewFile . $ext;
        if (!file_exists($viewFile)) {
            show_error('模版不存在！', 404, '错误');
        }
        //加载模版文件
        $CI = &get_instance();
        $CI->load->view(str_ireplace(VIEWPATH,'',$viewFile),$vars);
    }
}

/**
 * select导航树
 */
if(!function_exists('optionTree')){
    function optionTree($menu,$parentId='',&$sub=0) {
        if(!is_array($menu)) return;
        foreach($menu as $_menuItem){
            $hasChild = isset($_menuItem['children'])&&is_array($_menuItem['children'])&&$_menuItem['children'];
            printf('<option value="%s" %s>%s</option>',$_menuItem['cate_id'],$_menuItem['cate_id']==$parentId ? 'selected' : '',str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;',$sub).$_menuItem['cate_name']);
            if($hasChild){
                $sub++;
                optionTree($_menuItem['children'],$parentId,$sub);
            }
        }
        $sub--;
    };
}
/**
 * 权限
 */
if(!function_exists('acl')){
    function acl($rights,$control='',$parent = '') {
        if(strtolower($control) == 'home'){
            return 1;
        }
        if($_SESSION['username']=='admin' && $_SESSION['uid']==1){
            return 1;
        }
        $CI = &get_instance();
        if(!$control){
            $control = strtolower(get_class($CI));
        }
        if($parent){
            $menu = $CI->getData();
            if(!isset($menu['menu'][$control])) return 0;
            $submenuArr = array_keys($menu['menu'][$control]['children']);
            if(!$submenuArr) $submenuArr=$control;
            $submenu ="'".implode("','",$submenuArr)."'";
            $subNum =$CI->db->query("select * from material_acl where methord in (".$submenu.") and uid=?",array($_SESSION['uid']))->num_rows();
            if($subNum) return 1;
        }
        $CI->db->query("select * from material_acl where methord = ? and uid=?",array($control,$_SESSION['uid']))->num_rows();
        if($rights){
            $queryreslt = $CI->db->query("select * from material_acl where access =? and uid=?",array($control.$rights,$_SESSION['uid']))->num_rows();
        }else{
            $queryreslt = $CI->db->query("select * from material_acl where methord = ? and uid=?",array($control,$_SESSION['uid']))->num_rows();
        }
        return $queryreslt;
    };
}

/**
 * 控制台页面左侧导航栏生成
 */
if(!function_exists('menuShow')){
    function menuShow($menu,$selectedItem='',$sub=false) {
        if(!is_array($menu)) return;
        if($sub){
            printf('<ul class="submenu">');
        }

        foreach($menu as $_key=>$_menuItem){
            if(!$_menuItem['show']) continue;
            if(!acl('',$_key,!$sub)) continue;
            $hasChild = is_array($_menuItem['children'])&&$_menuItem['children'];
            $linktar = $_menuItem['target'] ? $_menuItem['target'] : 'home/error';
            printf('<li class="%s">',$selectedItem ==$_menuItem['text'] ? 'active' : '');
            printf('<a href="%s" class="%s">',site_url($linktar),$hasChild?'dropdown-toggle':'');
            printf('<i class="%s"></i>',$_menuItem['icon']);
            printf('<span class="menu-text"> %s </span>',$_menuItem['text']);
            if($hasChild){
                printf('<b class="arrow icon-angle-down"></b>');
            }
            printf('</a>');
            if($hasChild){
                menuShow($_menuItem['children'],'',true);
            }
            printf('</li>');
        }
        if($sub){
            printf('</ul>');
        }
    };
}

/**
 * 控制台页面左侧导航栏生成
 */
if(!function_exists('categoryList')){
    function categoryList($menu,$selectedItem='',$sub=false) {
        if(!is_array($menu)) return;
        if($sub){
            printf('<ul class="submenu">');
        }
        foreach($menu as $_menuItem){
            if(!$_menuItem['show']) continue;
            $hasChild = is_array($_menuItem['children'])&&$_menuItem['children'];
            printf('<li class="%s">',$selectedItem ==$_menuItem['text'] ? 'active' : '');
            printf('<a _href="%s" class="%s">',site_url($_menuItem['target']),$hasChild?'dropdown-toggle':' nav_click_event');
            printf('<i class="%s"></i>',$_menuItem['icon']);
            printf('<span class="menu-text"> %s </span>',$_menuItem['text']);
            if($hasChild){
                printf('<b class="arrow icon-angle-down"></b>');
            }
            printf('</a>');
            if($hasChild){
                menuShow($_menuItem['children'],'',true);
            }
            printf('</li>');
        }
        if($sub){
            printf('</ul>');
        }
    };
}

if(!function_exists('sendNotice')){
    function sendNotice($title,$content){
        $CI = &get_instance();
        $CI->db->insert('sys_notice',array(
            'touser'=>0,
            'title'=>$title,
            'content'=>$content,
            'cdate'=>date('Y-m-d H:i:s'),
            'expdate'=>date('Y-m-d H:i:s',strtotime('+365 days')),
            'isvalid'=>1,
            'togroup'=>0
        ));
    }
}

if(!function_exists('stripanchor')){
    function stripanchor($content){
        return preg_replace("/href=[\"|\'](.+?)[\"|\']/","",$content);
    }
}