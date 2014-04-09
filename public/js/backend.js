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
   $(this).parentsUntil('form').parent().find('input[name=__redirect]').val($(this).attr('data-redirect-btn'));
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

CKEDITOR.config.contentsCss = '/css/editor.css';
$( 'textarea.wysiwyg' ).ckeditor();

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

$(".tag-input").select2({
    tags:[],
    tokenSeparators: [",", " "]
});



$(window).on("paste", function(e){
    if(!e || !e.originalEvent || !e.originalEvent.clipboardData) {
        return;
    }
    console.log(e);
    var items = e.originalEvent.clipboardData.files,
    i = 0,
    item;
    for(i in items) {
        item = items[i];
        if(item.type && item.type.match(/^image\//i)) {
            console.log(item);

            //var blob = item.getAsFile();
            var reader = new FileReader();
            reader.onload = function(event){
                console.log(event.target.result);
                $('#epiceditor').after('<img src="' + event.target.result + '" alt="">');
            }
            reader.readAsDataURL(item); 


        }
    }
});
