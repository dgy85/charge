<!doctype html>
<html lang="zh_CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <base href="<?php echo site_url() ?>">
    <link rel="stylesheet" href="/assets/libs/css/zui.min.css">
    <?php
    //css样式表输出
        if(isset($css)){
            if(is_array($css)){
                foreach ($css as $_cssItem){
                    printf('<link rel="stylesheet" href="%s" />',$_cssItem);
                }
            }elseif ($css){
                printf('<link rel="stylesheet" href="/assets/apps/styles/%s.css" />',$css);
            }
        }
    ?>
    <title><?php echo isset($title) ? $title : '商户后台管理'?></title>
</head>