<?php
if(!function_exists('checkStack')){
    /**
     * 验证调用是否被合法的调用
     * @param $access
     * @param string $type
     * @return bool
     */
    function checkStack($access,$type = 'function')
    {
        if(!in_array($type,array('function','class'))) return false;

        $callByAccess = false;
        foreach (debug_backtrace() as $_stack){
            if(isset($_stack[$type]) && $_stack[$type] == $access){
                $callByAccess = true;
                break;
            }
        }

        return $callByAccess;
    }
}