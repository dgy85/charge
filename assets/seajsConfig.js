try{
    seajs.config({
        base: '/assets/',
        //plugins:['shim'],
        alias: {
            "jquery":"js/lib/jquery-1.10.2.min.js",
            "boot":'js/lib/bootstrap.min.js',
            "bootbox":"js/lib/bootbox.min.js",
            "easyui_zh":'js/lib/easyui-lang-zh_CN.js',
            "easyui":"js/lib/jquery.easyui.min.js",
            "editor":"js/lib/bootstrap-wysiwyg.min.js",
            "respond":'js/lib/respond.min.js',
            "ace-ele":'js/lib/ace-elements.min.js',
            "ace":'js/lib/ace.min.js',
            'frameevent':'js/lib/frameEvents.js'
        },
        preload:['jquery'],
        map: [[/^(.*\.(?:css))(.*)$/i, '$1?v='+Math.random()]]
    });
}catch(e){
    if(typeof console == 'undefined'){
        console = {
            log: function () {}
        }
    }
}