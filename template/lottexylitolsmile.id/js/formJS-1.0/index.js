function Index(opt){   
    var thisObj = this;   
    this.loadOnReady = function loadOnReady(){   
        if(opt.loadPopup){ 
            var arrayPopup = new Array()
            arrayPopup['url'] = '/popup-login.php'; 
            loadOverlayScreen(arrayPopup); 
        } 

        // simple parralax
     /*   $(window).scroll( function(){

            var bottomOfWindow = $(window).scrollTop() + $(window).height();

            //fade-in
            $('.simple-parallax').each(function(){
                var bottomOfObject = $(this).offset().top + $(this).outerHeight();
                var offsetAdj = parseFloat($(this).attr("ref-offset")) || 0;

                bottomOfObject = (offsetAdj == 0) ?  bottomOfObject : bottomOfObject - ($(this).height() * offsetAdj);

                 if( bottomOfWindow > bottomOfObject )
                    $(this).addClass('showing'); 
                else
                    $(this).removeClass('showing'); 
            }); 
        }); 

        $(window).scroll(); */

    };

}