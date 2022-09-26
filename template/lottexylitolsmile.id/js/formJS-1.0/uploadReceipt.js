function UploadReceipt(opt){   
    var thisObj = this;   
    //var uploadedImage = {}; // gk tau perlu gk 
    
    this.loadOnReady = function loadOnReady(){   
        
        createImageUploader({"name":opt.imageUploaderTarget, "btnLabel":"Upload Struk" },{"folder":opt.uploadFolder},false);
        $("#form-upload [name=btnSave] .loading-icon").remove();
        $('#form-upload')
            .bootstrapValidator({
            feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
            } 
            })
            .on('success.form.bv', function(e) {
            
                // Prevent form submission
                e.preventDefault(); 
                var $form = $(e.target); 
                var bv = $form.data('bootstrapValidator'); 

                disableButton($form.find("[name=btnSave]"));
 
                $.post($form.attr('action'), $form.serialize(), function(result) {

                    disableButton($form.find("[name=btnSave]"),false);
                    
                    var error = "";
                    for (i=0;i<result.length;i++) error=error + "<li>" + result[i].message + "</li>";
                    if (error != "")
                    error = "<ul class=\"message-dialog-ul\">" + error + "</ul>";

                    var notifObj = $form.find(".notification-msg");
                    notifObj.html(error).hide().fadeToggle("fast");

                    if (!result[0].valid){
                        setStatusColorNotification(notifObj,1);
                        $form.data('bootstrapValidator').resetForm();
                    }else{
                        setStatusColorNotification(notifObj,2);
                        $(".subsec-form").html("<div class=\"subsec-03 label-color\">Upload struk telah berhasil.<br>Tim kami akan memvalidasi strukmu terlebih dahulu dan akan kami email hasilnya.</div>");
                    }
                    
                    scrollToTopForm($form);
                }, 'json');
            });
  
    };

}