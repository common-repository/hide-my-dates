jQuery(document).ready(function($){

    checkExpTime();

    $('#close-donat').on('click',function(e) {
        localStorage.setItem('hmd-close-donat', 'yes');
        $('#donat').slideUp(300);
        $('#restore-hide-blocks').show(300);
        setExpTime();
    });

    $('#close-about').on('click',function(e) {
        localStorage.setItem('hmd-close-about', 'yes');
        $('#about').slideUp(300);
        $('#restore-hide-blocks').show(300);
        setExpTime();
    });

    $('#close-help').on('click',function(e) {
        localStorage.setItem('hmd-close-help', 'yes');
        $('#help').slideUp(300);
        $('#restore-hide-blocks').show(300);
        setExpTime();
    });

    $('#restore-hide-blocks').on('click',function(e) {
        localStorage.removeItem('hmd-time');
        localStorage.removeItem('hmd-close-donat');
        localStorage.removeItem('hmd-close-help');
        localStorage.removeItem('hmd-close-about');
        $('#restore-hide-blocks').hide(300);
        $('#donat').slideDown(300);
        $('#help').slideDown(300);
        $('#about').slideDown(300);
    });

    function setExpTime() {
        var limit = 90 * 24 * 60 * 60 * 1000; // 3 месяца
        var time = localStorage.getItem('hmd-time');
        if (time === null) {
            localStorage.setItem('hmd-time', +new Date());
        } else if(+new Date() - time > limit) {
            localStorage.removeItem('hmd-time');
            localStorage.removeItem('hmd-close-donat');
            localStorage.removeItem('hmd-close-help');
            localStorage.removeItem('hmd-close-about');
            localStorage.setItem('hmd-time', +new Date());
        }
    }

    function checkExpTime() {
        var limit = 90 * 24 * 60 * 60 * 1000; // 3 месяца
        var time = localStorage.getItem('hmd-time');
        if (time === null) {

        } else if(+new Date() - time > limit) {
            localStorage.removeItem('hmd-time');
            localStorage.removeItem('hmd-close-donat');
            localStorage.removeItem('hmd-close-help');
            localStorage.removeItem('hmd-close-about');
        }
    }

});