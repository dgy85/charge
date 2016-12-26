<!doctype html>
<html lang="zh_CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <?php
    //css样式表输出
        if(isset($css) && is_array($css)){
            foreach ($css as $_cssItem){
                printf('<link rel="stylesheet" href="%s" />',$_cssItem);
            }
        }
    ?>
    <title><?php echo isset($title) ? $title : '后台管理'?></title>
</head>