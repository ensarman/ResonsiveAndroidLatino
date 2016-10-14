    $(document).ready(function(){ 
 
        $(window).scroll(function(){
            if ($(this).scrollTop() > 100) {
                $('.scrollup').fadeIn();
            } else {
                $('.scrollup').fadeOut();
            }
        }); 
 
        $('#ir_arriba').click(function(){
            $("html, body").animate({ scrollTop: 0 }, 850);
            return false;
        });
 
    });