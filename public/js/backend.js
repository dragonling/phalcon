$.noty.defaults = {
    layout: 'bottomRight',
    theme: 'defaultTheme',
    type: 'information',
    text: '',
    dismissQueue: true, // If you want to use queue feature set this true
    template: '<div class="noty_message"><span class="noty_text"></span><div class="noty_close"></div></div>',
    animation: {
        open: {height: 'toggle'},
        close: {height: 'toggle'},
        easing: 'swing',
        speed: 500 // opening & closing animation speed
    },
    timeout: false, // delay for closing event. Set false for sticky notifications
    force: false, // adds notification to the beginning of queue when set to true
    modal: false,
    maxVisible: 10, // you can set max visible notification for dismissQueue true option
    closeWith: ['click'], // ['click', 'button', 'hover']
    callback: {
        onShow: function() {},
        afterShow: function() {},
        onClose: function() {},
        afterClose: function() {}
    },
    buttons: false // an array of buttons
};

$(document).ready(function(){
    //form validation
    $('form').parsley({
        successClass: "has-success",
        errorClass: "has-error",
        classHandler: function(el) {
            return el.$element.closest(".form-group");
        },

        errorsWrapper: "<span class='help-block' style='margin:0;'></span>",
        errorTemplate: "<span></span>"
    });


    $('input[type=submit], button[type=submit]').on('click', function(){
        $(this).closest('form').find('input[name=__redirect]').val($(this).attr('data-redirect-btn'));
    });

    $('.select2').select2();

    $('table th input:checkbox').on('click' , function(){
        var that = this;
        $(this).closest('table').find('tr > td:first-child input:checkbox')
        .each(function(){
            this.checked = that.checked;
            $(this).closest('tr').toggleClass('selected');
        });

    });

    var ckeditorContainers = $( 'textarea.wysiwyg' );
    if(ckeditorContainers[0]) {
        CKEDITOR.config.contentsCss = '/css/editor.css';
        var ckeditor = ckeditorContainers.ckeditor().ckeditorGet();
        ckeditor.on('instanceReady', function( ev ) {
            $("#test").on('click', function(){
                ckeditor.insertHtml('<p>a</p>');
                return false;
            });
        });
    }



    $('.date-picker').datepicker({autoclose:true}).next().on(ace.click_event, function(){
        $(this).prev().focus();
    });
    $('.time-picker').timepicker({
        minuteStep: 1,
        showSeconds: true,
        showMeridian: false
    }).next().on(ace.click_event, function(){
        $(this).prev().focus();
    });

    $('.ace-file-input').ace_file_input({
        style:'well',
        btn_choose:'Drop files here or click to choose',
        btn_change:null,
        no_icon:'icon-cloud-upload',
        droppable:true,
        thumbnail:'small'//large | fit
        //,icon_remove:null//set null, to hide remove/reset button
        /**,before_change:function(files, dropped) {
        //Check an example below
        //or examples/file-upload.html
        return true;
        }*/
        /**,before_remove : function() {
          return true;
          }*/
    ,
preview_error : function(filename, error_code) {
    //name of the file that failed
    //error_code values
    //1 = 'FILE_LOAD_FAILED',
    //2 = 'IMAGE_LOAD_FAILED',
    //3 = 'THUMBNAIL_FAILED'
    //alert(error_code);
}

    }).on('change', function(){
        //console.log($(this).data('ace_input_files'));
        //console.log($(this).data('ace_input_method'));
    });


    /*
    var editor = new EpicEditor({
        autogrow : {
            minHeight : 500,
            maxHeight : 800
        },
        theme: {
            base: 'http://www.goldtoutiao.com//vendor/js/epiceditor/themes/base/epiceditor.css',
            preview: 'http://www.goldtoutiao.com/vendor/js/epiceditor/themes/preview/bartik.css',
            editor: 'http://www.goldtoutiao.com/vendor/js/epiceditor/themes/editor/epic-light.css'
        },
        button: {
            preview: false,
            fullscreen: true,
            bar: "auto"
        },
        string: {
            togglePreview: '预览(快捷键Alt+p)',
            toggleEdit: 'Toggle Edit Mode',
            toggleFullscreen: '全屏(快捷键Alt+f)'
        },
    }).load();
   */

    $(".tag-input").select2({
        tags:[],
        tokenSeparators: [",", " "]
    });

    var pasteUploader = $('.paste-uploader');
    var onPaste = function(e) {
        if(!e || !e.originalEvent || !e.originalEvent.clipboardData) {
            return;
        }
        //Filefox use clipboardData.files
        var items = e.originalEvent.clipboardData.files,
             i = 0,
             item,
             notice,
             uploadUrl = pasteUploader.attr('data-upload-url'),
             uploadMaxNum = pasteUploader.attr('data-upload-allow-num');
        if(pasteUploader.find('.paste-upload-image').length >= uploadMaxNum) {
            return;
        }

        var upload = function(fileinfo, filedata) {
            var reader = new FileReader();
            notice = noty({text: 'Uploading : ' + fileinfo.name});
            reader.onload = function(event){
                $.ajax({
                    url : uploadUrl,
                    type : 'POST',
                    data : {
                        name : fileinfo.name,
                        size : fileinfo.size,
                        type : fileinfo.type,
                        file : event.target.result
                    },
                    success : function(response) {
                        pasteUploader.find('input[name=image]').val(response.fullUrl);
                        pasteUploader.find('input[name=image_id]').val(response.id);
                        pasteUploader.append('<img src="' + response.fullUrl + '" class="paste-upload-image" width="100%" alt="">');
                        notice.setText(fileinfo.name + ' uploaded').setType('success');
                    },
                    error : function(response) {
                        notice.setText(fileinfo.name + ' upload failed').setType('error');
                    }
                }); 
                //$('#epiceditor').after('<img src="' + event.target.result + '" alt="">');
            }
            reader.readAsDataURL(item); 

        };
        for(i in items) {
            item = items[i];
            if(item.type && item.type.match(/^image\//i)) {
                if(i > uploadMaxNum) {
                    continue;
                }
                upload(item);
            }
        }
    }
    if(pasteUploader) {
        $(window).on("paste", onPaste);
    }

});

