try{
    seajs.config({
        base: '/assets/',
        //plugins:['shim'],
        alias: {
            "jquery":"libs/lib/jquery/jquery.js",
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