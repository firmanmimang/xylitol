var uploadedImage = {}; // nanti baru dipindahkan jika perlu

function preventSubmitBeforeJSLoaded(obj){
    event.preventDefault(); 
    alert('Please wait until the web fully loaded.');
}

function addEventListenerByClass(className, event, fn) {
    var list = document.getElementsByClassName(className);
    for (var i = 0, len = list.length; i < len; i++) {
        list[i].addEventListener(event, fn, false);
    }
}
function removeEventListenerByClass(className, event, fn) {
    var list = document.getElementsByClassName(className);
    for (var i = 0, len = list.length; i < len; i++) {
        list[i].removeEventListener(event, fn, false);
    }
}

function hideOverlayScreen(){ 
    $("html, body").css("overflow","inherit"); 
    $("#popup-panel").fadeOut("fast");   
    //$(".hide-on-dismiss").hide();
}

function loadOverlayScreen(arrParam){      
    
    $(':focus').blur();
    $("html, body").css("overflow","hidden");
    $("#popup-panel").fadeIn("fast"); 
     
    if (arrParam.url){ 
        $("#popup-panel").load(arrParam.url); 
    } 
    $("#popup-panel" ).on( "click", function(e) {  
        if ($(e.target).hasClass('content-panel')) { return; }
        if ($(e.target).closest('.content-panel').length) { return; }
        hideOverlayScreen();
    });
    
}

function createImageUploader(fileUploaderTarget,fileInfo,multipleFile){ 
       
	var target = $("." + fileUploaderTarget.name); // sementara asumsi semua nama class gk ad yg sama
	   
	 if (fileInfo.token == undefined || fileInfo.token == "")  
		 fileInfo.token = Math.floor((Math.random() * 1000) + 1).toString() + $.now();     
	
/*  
	uploadedImage[fileInfo.token] = Array();*/
	target.append("<input type=\"hidden\" name=\"" + fileUploaderTarget.name + "\" />"); 
	target.append("<input type=\"hidden\" name=\"token-" + fileUploaderTarget.name + "\" value=\"" + fileInfo.token + "\" />");
	  
	/*
    // ini nanti saja
    if (fileInfo.arrImage != undefined || fileInfo.arrImage == ""){ 
         var i;
      	 for(i=0;i<fileInfo.arrImage.length;i++)  { 
             pushImageThumb(fileUploaderTarget,{"folder":fileInfo.folder, "token":fileInfo.token, "fileName":fileInfo.arrImage[i],"phpThumbHash":fileInfo.phpThumbHash[i]},multipleFile,multipleColor,variantTarget) 
         }
	}*/
	 
    var btnLabel = (fileUploaderTarget.btnLabel) ? fileUploaderTarget.btnLabel : 'Upload File';
    
	var uploader = new qq.FileUploader({
						element: target.find('.file-uploader')[0], 
						action: '/fileuploader.php?action=upload&folder=' + fileInfo.folder + '&token='+ fileInfo.token, 
						allowedExtensions:['jpg','jpeg','png','gif','ico'],
                        template : '<div class="qq-uploader"><div class="qq-upload-drop-area"><span>Drop files here to upload</span></div><div class="qq-upload-button">'+btnLabel+'</div><ul class="qq-upload-list"></ul></div>',
						onComplete: function(id, fileName, responseJSON){   
							if (responseJSON.success == true)
								pushImageThumb(fileUploaderTarget,{"folder":fileInfo.folder, "token":fileInfo.token, "fileName":responseJSON.fileName,"phpThumbHash":responseJSON.phpThumbHash},multipleFile); 
						} 
					});   
	 
	return fileInfo.token;				  
}
   

function pushImageThumb(fileUploaderTarget,fileInfo,multipleFile){  
      
    var path = PHP_CONFIG['uploadTempDocShort']; 
    
	var target = $("." + fileUploaderTarget.name); // sementara asumsi semua nama class gk ad yg sama
	var iconMultipleColor = '';
	 
	if (multipleFile == false) 
		target.find(".image-list").html("");
 
	var extension = fileInfo.fileName.substr( (fileInfo.fileName.lastIndexOf('.') +1) );
	fileurl = "../phpthumb/phpThumb.php?src="+path+ fileInfo.folder + fileInfo.token+ "/"+fileInfo.fileName+"&w=150&h=150&far=C&hash=" + fileInfo.phpThumbHash;
	
	if (extension == 'ico')
		fileurl = PHP_CONFIG['uploadTempURL'] + fileInfo.folder + fileInfo.token+ "/"+fileInfo.fileName;
	  
	 
 	var temp = "<li relfilename=\""+fileInfo.fileName+"\" relPHPThumbHash=\""+fileInfo.phpThumbHash+"\">";
	temp += "<div class=\"file-uploader-image\"><img src=\""+ fileurl +"\"/></div>";
	//temp += "<div class=\"file-uploader-action-bar\">";
	//temp += "<a href=\"#\" onClick=\"deleteImageUploaderThumb(this,{'tabID':'"+fileUploaderTarget.tabID+"' , 'name':'"+fileUploaderTarget.name+"'},'" + fileInfo.token  + "')\"><i class=\"far fa-times\" style=\"float:right; font-size:1.2em;\" ></i></a>";
	//temp += "<a href=\"/phpthumb/phpThumb.php?src="+path+fileInfo.folder + fileInfo.token+ "/"+fileInfo.fileName+"&far=C&hash="+fileInfo.phpThumbHash+"\" target=\"_blank\"><i class=\"far fa-eye\" style=\"float:right; font-size:1.2em; margin-right:0.5em; padding-bottom:0.2em\"></i></a>";
	//temp += "</div>";
	temp += "</li>"; 
	
	target.find(".image-list").append(temp);	
	   
	updateItemImageArray(fileUploaderTarget,fileInfo.token);
	  
    target.find('.image-list li:last img').on('load', function(){
        $(this).closest(".file-uploader-image").css("background-image","none"); 
    }); 
			
}

function updateItemImageArray(fileUploaderTarget,token){
    
	var target = $("." + fileUploaderTarget.name); // sementara asumsi semua nama class gk ad yg sama
    
	 uploadedImage[token] = Array();
	 target.find(".image-list li").each(function(i) {      
		uploadedImage[token].push($(this).attr("relfilename")); 
     });
	   
	 target.find("[name=" + fileUploaderTarget.name + "]").val(uploadedImage[token]);
	 
}

// === update per project    
function loadSubMenuProfile(){
    $(".subsec-profile").hover(
          function() {
            $( this ).find(".profile-sub-menu").fadeIn("fast");  
          }, function() {
            $( this ).find(".profile-sub-menu").fadeOut("fast");  
          }
   ); 
}


addEventListenerByClass('prevent-form-submit', 'submit', preventSubmitBeforeJSLoaded); 
