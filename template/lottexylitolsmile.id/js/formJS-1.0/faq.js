function FAQ(){   
    var thisObj = this;  
    
    this.loadOnReady = function loadOnReady(){  
        $( ".faq-index li" ).click(function() {
          var pkey = $(this).attr("rel");
          $(".faq-detail>li").hide(); 
          $(".faq-detail-"+pkey).show(); 
        });
    }

}