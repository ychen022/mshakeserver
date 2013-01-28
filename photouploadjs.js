$('#document').ready(function(){
    $('#photoupload').change(function(){
        num = this.files.length;
        type = this.files[0].type;
        size = this.files[0].size;
        altext = ""
        if(num != 1){altext += "You can only unload one profile phicture! \n";}
        allowtype = ['gif', 'jpg', 'png'];
        if(!type in allowtype){altext += "File type not allowed! Must be 'gif', 'jpg', or 'png'. \n";}
        if(size > 1048576){altext += "File must be less than 1 MB! \n"}
        $('loadinfo').html(altext);
    })

    $('#submitphoto').click(function(){
        if($('loadinfo').html()==""){
            var req = {}
            req['action'] = "uploadphoto"
            req['picture'] = new FormData($('#photoupload')[0]);
            $.ajax({
                url: 'door.php',
                type: 'POST',
                data: req,
                xhr: function() {$.ajaxSettings.xhr();
                    if($.ajaxSettings.xhr().upload){
                        $.ajaxSettings.xhr().upload.addEventListener('progress', progressDis(file), false);
                    }
                    return myXhr;
                },
                datatype: "text",
                success: upload(response),
                error: function (jqXHR, textStatus, errorThrown) {
                    alert("error!");
                    alert(jqXHR.responseText);},
                cache: false,
                contentType: false,
                processData: false
            });
        }
    })
    
    function progressDis(file){
        $('#uploadprogress').attr('max', file.total);
        $('#uploadprogress').attr('value', file.loaded);
    }
    
    function upload(response){
        if(response == "upload_success"){
            $('#upload').show(slide);
            $('#upload')
        }
    }
    
    if($('#photo').width()>$('#photo').height()){
        $('#photo').css('width', 500);
        var deter = "width";}
    else{$('#photo').css('height', 500)
        var deter = "height";};
    
    $('#photo').imgAreaSelect({
        handles: true,
        aspectRatio: '1:1',
        onSelectChange: preview,
        onSelectEnd: store,
    });
    
    function preview(img, selection){
        var scaleX = 500 / (selection.width);
        var scaleY = 500 / (selection.height);
        if(deter=="width"){
            var h = $('#photo').height()*500/$('#photo').width()
            $('#photo + div > img').css({
                width: Math.round(scaleX * 500) + 'px',
                height: Math.round(scaleY * h) + 'px',
                marginLeft: '-' + Math.round(scaleX * selection.x1) + 'px',
                marginTop: '-' + Math.round(scaleY * selection.y1) + 'px'
            });}
        else{
            var w = $('#photo').width()*500/$('#photo').height()
            $('#photo + div > img').css({
                width: Math.round(scaleX * w) + 'px',
                height: Math.round(scaleY * 500) + 'px',
                marginLeft: '-' + Math.round(scaleX * selection.x1) + 'px',
                marginTop: '-' + Math.round(scaleY * selection.y1) + 'px'
            });}
    }
    
    function store(img, selection){
        $('input[name="x1"]').val(selection.x1);
        $('input[name="y1"]').val(selection.y1);
        $('input[name="x2"]').val(selection.x2);
        $('input[name="y2"]').val(selection.y2);
    }
    
    $('<div><img src="bells.png" style="position: relative;" /><div>')
        .css({
            float: 'left',
            position: 'relative',
            overflow: 'hidden',
            width: '500px',
            height: '500px'
        })
        .insertAfter($('#photo'));
    
    $('#savethumb').click(function(){
        var req = {};
        req['action'] = 'editthumbnail';
        req['x1'] = $('input[name="x1"]').val();
        req['x2'] = $('input[name="x2"]').val();
        req['y1'] = $('input[name="y1"]').val();
        req['y2'] = $('input[name="y2"]').val();
        $.ajax({
            url: 'door.php',
            type: 'POST',
            data: req,
            datatype: "text",
            success: function(response){},
            error: function (jqXHR, textStatus, errorThrown) {
                alert("error!");
                alert(jqXHR.responseText);},
        });
    })
})