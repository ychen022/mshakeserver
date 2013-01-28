$("document").ready(function(){
    $("input").attr("disabled", "disabled");
    $("select").attr("disabled", "disabled");
    $(".input_text").css("border", "none");
    $("#save").hide();
    $("#crop").hide();
    $("#upload").hide();
    
    
    var req = {};
    req['action'] = "getprofile"
    $.ajax({
        url: 'door.php',
        data: req,
        type: "POST",
        datatype: "JSON",
        success: function(response){profile(response);},
        error: function (jqXHR, textStatus, errorThrown) {
        alert("error!");
        alert(jqXHR.responseText);}
    })
    
    function profile(response){
        var result = $.parseJSON(response);
        $('#image img').attr('src', result['photo']);
        $('#firstname').val(result['firstname']);
        $('#lastname').val(result['lastname']);
        profileGenderParser(result['gender']);
        $('#address').val(result['address']);
        $('#city').val(result['city']);
        $('#state').val(result['state']);
        S('#zip').val(result['zip']);
    }
    
    function profileGenderParser(input){
        if(input=='male'){$('#male').attr('checked', true)}
        else if(input=='female'){$('#female').attr('checked', true)}
    }
    
    $("#editicon").click(function(){
        $("#save").show();
        $(this).hide();
        $("input").attr("disabled", false);
        $("select").attr("disabled", false);
        $(".input_text").css("border", "1px solid black");
    })
    
    $("#save").click(function(){
        var req = {};
        req['action'] = "editprofile"
        $.ajax({
            url: 'door.php',
            data: req,
            type: "POST",
            datatype: "text",
            success: function(response){edit(response);},
            error: function (jqXHR, textStatus, errorThrown) {
            alert("error!");
            alert(jqXHR.responseText);}
        })
    })
    
    function edit(response){
        if(response=="edit_success"){
            $('#editinfo').html("Your profile hava been saved!");
            $('#editinfo').css("color", "#7b9a25");
            $("#editicon").show();
            $("input").attr("disabled", "disabled");
            $("select").attr("disabled", "disabled");
            $(".input_text").css("border", "none");
            $("#save").hide();
        }
        else{
            $('#editinfo').html("Error! Can't be saved");
            $('#editinfo').css("color", "rgb(192, 30, 41)");
        }    
    }
    
    function checkget(response){
        var result = $.parseJSON(response);
        if(result.wantsget==0){}
        else if(result.wantsget==1){
            get();
            notifParser(result.get.notification);
        }
    }
    
        var req = {};
    req['action'] = 'init'
    $.ajax({
        url: 'fakedoor.php',
        data: req,
        type: "POST",
        datatype: "JSON",
        success: function(response){checkget(response);},
        error: function (jqXHR, textStatus, errorThrown) {
        alert("error!");
        alert(jqXHR.responseText);},
    })
    
    $('#dropdown_list').hide();
    $('#useraccount').click(function(){
        $('#dropdown_list').toggle('slow');
    })

    $('#logout').click(function(){
        var req = {};
        req['action'] = "logout";
        $.ajax({
                url: 'door.php',
                data: req,
                type: "POST",
                datatype: "text",
                success: function(response){logOut(response)},
                error: function (jqXHR, textStatus, errorThrown) {
                alert("error!");
                alert(jqXHR.responseText);},
        }) 
    });
    
    function logOut(response){
        if(response == "logout_success"){window.location.replace('index.html');}
        else{alert("Log Out Failed!")}
    }
    
    function notifParser(notifiction){
        var invite = ''
        var join = ''
        var num = notifiction.length;
        for(var i=num-1; i>=0; i--){
            var noti = notifiction[i];
            var type = noti.type
            if(type == "joinDecisionSend"){
                join += '<a name="join_decision" class="notif_item">\
                            Your request to join \
                            <span class="group_name">'
                            + getGruopMember(noti.group)
                            +'</span>\
                            has been '+getJoinDecision(noti.decisionType)+'.\
                            </a>'
            }else if(type == "joinDecisionReceive"){
                join += '<a name="join_decision" class="notif_item">\
                            <span class="group_name">'
                            + getGroupMember(noti.group)+'\'s'
                            +'</span>\
                            invitation has been '+getJoinDecision(noti.decisionType)+'.\
                            </a>'
            }else if(type == "inviteDecision"){
                invite += '<a name="add_decision" class="notif_item">\
                            The request to add \
                            <span class="group_name">'+getGroupMember(noti.group)+'</span>\
                            to your group has been\ '+getInviteDecision(noti.decisionType)+'.\
                            </a>'
            }else if(type == "joinRequest"){
                join += '<a name="join_request" val='+noti.groupID+' class="notif_item" name="joinrequest">\
                            <span class="group_name">'+getGroupMember(noti.group)+'</span>\
                            invite you to join their group.\
                            </a>'
            }else if(type == "inviteRequest"){
                invite += '<a name="add_request" val='+noti.groupID+'class="notif_item" name="addrequest">'
                            +noti.initiator.firstname+' '+noti.initiator.lastname+'\'s send a request to add\
                            <span class="group_name">'+getGroupMember(noti.group)+'</span>\
                            to your group.\
                            </a>'
            }else{alert("Yang Chen is stupid!")}
        }
        join += $('#joinmess').html();
        invite += $('#addmess').html();
        $('#joinmess').html(join);
        $('#addmess').html(invite);
    }
    
    function addRequestParser(response){
        var g = $.parseJSON(response)
        var groupinfo = '<div class="group_span_noti" id="addvote" val='+g.group.ID+'>\
                        <div class="group">\
                        <div class="member_list">'
        var number = g.group.nop;
        for(var k=0; k<number; k++){
            groupinfo += '<div class="group_member">\
                            <img src='+g.member[k].photolink+'>\
                        </div>'
        }
        groupinfo += '</div>\
                        <div class="group_info">\
                             <div class="distance">\
                                 Average distance: '+g.group.avgdist+' miles\
                             </div>\
                             <div class="food_type">\
                                 Food type: '+foodtyping(g.group.foodtype[0])+' '+foodtyping(g.group.foodtype[1])+' '+foodtyping(g.group.foodtype[2])+'\
                             </div>\
                             <div class="price_range">\
                                 Price range $'+g.group.pricemin+'-$'+g.group.pricemax+'\
                             </div>\
                             <div class="capacity">\
                                 Capacity: '+g.group.capacity+'\
                             </div>\
                         </div>\
                     </div>\
                     <ul class="memberfull_list">'
        for(var k=0; k<number; k++){
            var foodtype = "";
            for(var j=0; j<g.member[k].foodtype.length; j++){
                foodtype += foodtyping(g.member[k].foodtype[j])+' ';
            }
            groupinfo += '<li class="member_fullinfo">\
                                    <a class="member_img" href="">\
                                        <img src='+g.member[k].photolink+'>\
                                    </a>\
                                    <ul class="member_info">\
                                        <li>\
                                            '+g.member[k].firstname+' '+g.member[k].lastname+'\
                                        </li>\
                                        <li>\
                                            Gender: '+g.member[k].gender+'\
                                        </li>\
                                        <li>\
                                            Distance: '+g.member[k].distance+'\
                                        </li>\
                                        <li>\
                                            Food Type: '+foodtype+'\
                                        </li>\
                                    </ul>\
                            </li>'
        }
        groupinfo += '</ul></div>';
        $('#popinfo').html(groupinfo);
        $('#requsetpopup').show(slow);
    }
    
    function joinRequestParser(response){
        var g = $.parseJSON(response)
        var groupinfo = '<div class="group_span_noti" id="joinvote" val='+g.group.ID+'>\
                        <div class="group">\
                        <div class="member_list">'
        var number = g.group.nop;
        for(var k=0; k<number; k++){
            groupinfo += '<div class="group_member">\
                            <img src='+g.member[k].photolink+'>\
                        </div>'
        }
        groupinfo += '</div>\
                        <div class="group_info">\
                             <div class="distance">\
                                 Average distance: '+g.group.avgdist+' miles\
                             </div>\
                             <div class="food_type">\
                                 Food type: '+foodtyping(g.group.foodtype[0])+' '+foodtyping(g.group.foodtype[1])+' '+foodtyping(g.group.foodtype[2])+'\
                             </div>\
                             <div class="price_range">\
                                 Price range $'+g.group.pricemin+'-$'+g.group.pricemax+'\
                             </div>\
                             <div class="capacity">\
                                 Capacity: '+g.group.capacity+'\
                             </div>\
                         </div>\
                     </div>\
                     <ul class="memberfull_list">'
        for(var k=0; k<number; k++){
            var foodtype = "";
            for(var j=0; j<g.member[k].foodtype.length; j++){
                foodtype += foodtyping(g.member[k].foodtype[j])+' ';
            }
            groupinfo += '<li class="member_fullinfo">\
                                    <a class="member_img" href="">\
                                        <img src='+g.member[k].photolink+'>\
                                    </a>\
                                    <ul class="member_info">\
                                        <li>\
                                            '+g.member[k].firstname+' '+g.member[k].lastname+'\
                                        </li>\
                                        <li>\
                                            Gender: '+g.member[k].gender+'\
                                        </li>\
                                        <li>\
                                            Distance: '+g.member[k].distance+'\
                                        </li>\
                                        <li>\
                                            Food Type: '+foodtype+'\
                                        </li>\
                                    </ul>\
                            </li>'
        }
        groupinfo += '</ul></div>';
        $('#popinfo').html(groupinfo);
        $('#requsetpopup').show(slow);
    }
    
    $('#accept').click(function(){
        $('#requestpopup').hide();
        var req = {};
        req['action'] = $('#popinfo div').attr('id');
        req['groupID'] = $('#popinfo div').val();
        req['vote'] = "A";
        $.ajax({
            type: "POST",
            url: "door.php",
            data: req,
            dataType: 'text',
            async: false,
            error: function (jqXHR, textStatus, errorThrown) {
            alert("error!");
            alert(jqXHR.responseText);},
        })
    })
    
    $('#accept').click(function(){
        $('#requestpopup').hide();
        var req = {};
        req['action'] = $('#popinfo div').attr('id');
        req['groupID'] = $('#popinfo div').val();
        req['vote'] = "D";
        $.ajax({
            type: "POST",
            url: "door.php",
            data: req,
            dataType: 'text',
            async: false,
            error: function (jqXHR, textStatus, errorThrown) {
            alert("error!");
            alert(jqXHR.responseText);},
        })
    })
    
    function getGroupMember(group){
        var num = group.length;
        var name = "";
        if(num == 1){name = group[0].firstname+' '+group[0].lastname}
        else if(num == 2){name = group[0].firstname+' '+group[0].lastname+'\ and '
            +group[1].firstname+' '+group[1].lastname}
        else{
            for(var i=0; i<num-1; i++){name += group[i].firstname+' '+group[i].lastname+', ';}
            name += 'and\ '+group[num-1].firstname+' '+group[num-1].lastname;
        }
        return name;
    }
    
    function getJoinDecision(type){
        if(type=="A"){return "accepted"}
        else if(type=="D"){return "declined"}
        else{return ""}
    }
    
    function getInviteDecision(type){
        if(type=="A"){return "approved"}
        else if(type=="D"){return "rejected"}
        else{return ""}
    }
    
    function get(){
    if (true){
        var req = {};
        req['action'] = 'get';
        $.ajax({
            type: "POST",
            url: "door.php",
            data: req,
            dataType: 'JSON',
            async: false,
            success: function(response){infoParser(response);},
            error: function (jqXHR, textStatus, errorThrown) {
            alert("error!");
            alert(jqXHR.responseText);},
            complete: function(){setTimeout(function(){get();}, 5000);},
        })
    }
    }
    
    function infoParser(response){
        result = $.parseJSON(response);
        notifParser(result.notification);
    }
    
    function shaking(){
        return ($('#ringer').val()=="shaking");
    }
    
    $('#notification').hide();
    $('#notification').val("off");
    $('#notif').click(function(){
        if($('#notification').val()=="off"){
            $('#notification').show('slide');
            $('#notification').val("on");
        } else if($('#notification').val()=="on"){
            $('#notification').hide('slide');
            $('#notification').val("off");
            delDecision();
        }
    })
    
    function delDecision(){
        $('.notif_item[name=join_decision]').remove();
        $('.notif_item[name=add_decision]').remove();
    }
    
    $('#notification').hide();
    $('#notification').val("off");
    $('#notif').click(function(){
        if($('#notification').val()=="off"){
            $('#notification').show('slide');
            $('#notification').val("on");
        } else if($('#notification').val()=="on"){
            $('#notification').hide('slide');
            $('#notification').val("off");
            delDecision();
        }
    })
    
    $('#home').click(function(){
        window.location.replace('userpage.html');
    })
    
    $('#profile').click(function(){
        window.location.replace('profile.html');
    })
    
    $('#about').click(function(){
        window.location.replace('about.html');
    })
    
    var request = {};
    request['action'] = "getprofile";
    $.ajax({
        type: "POST",
        url: "door.php",
        data: req,
        dataType: 'JSON',
        success: function(response){updateProfile(response);},
        error: function (jqXHR, textStatus, errorThrown) {
        alert("error!");
        alert(jqXHR.responseText);},
    })
    
    function updateProfile(response){
        result = $.parseJSON(response);
        $('#image img').attr('src', result.photo);
        $('#firstname').val(result.firstname);
        $('#lastname').val(result.lastname);
        genderParser(result.gender);
        $('#address').val(result.address);
        $('#city').val(result.city);
        $('#state').val(result.state);
        $('#zip').val(result.zipcode);
        $('#email').val(result.email);
        $('#food').val(result.favoritefood);
    }
    
    function genderParser(input){
        if(input=='male'){$('#male').attr('checked', true)}
        else if(input=='female'){$('#female').attr('checked', true)}
    }
    
    $('#uploadicon').click(function(){
        $('input[type=file]').attr('disabled', false);
        $('#upload').show('slide');
        $(this).hide();
    })            
    
    $('#fileupload').change(function(){
        num = this.files.length;
        type = this.files[0].type;
        size = this.files[0].size;
        altext = ""
        if(num != 1){altext += "You can only unload one profile phicture! \n";}
        allowtype = ['gif', 'jpg', 'png'];
        if(!type in allowtype){altext += "File type not allowed! Must be 'gif', 'jpg', or 'png'. \n";}
        if(size > 1048576){altext += "File must be less than 1 MB! \n"}
        $('alert').html(altext);
    })

    $('#uploadbutton').click(function(){
        if($('alert').html()==""){
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
    
    var x1 = 0;
    var x2 = 0;
    var y1 = 0;
    var y2 = 0;
    
    function upload(response){
        $('#upload').hide();
        $('#crop').show(slide);
        $('#photo').attr('src', response);
        if($('#photo').width()>$('#photo').height()){
            $('#photo').css('width', 440);
            var deter = "width";}
        else{$('#photo').css('height', 440)
            var deter = "height";};
        $('#photo').imgAreaSelect({
            handles: true,
            aspectRatio: '1:1',
            onSelectChange: preview,
            onSelectEnd: store,
        });
        
        function preview(img, selection){
            var scaleX = 270 / (selection.width);
            var scaleY = 270 / (selection.height);
            if(deter=="width"){
                var h = $('#photo').height()*440/$('#photo').width()
                $('#photo + div > img').css({
                    width: Math.round(scaleX * 440) + 'px',
                    height: Math.round(scaleY * h) + 'px',
                    marginLeft: '-' + Math.round(scaleX * selection.x1) + 'px',
                    marginTop: '-' + Math.round(scaleY * selection.y1) + 'px'
                });}
            else{
                var w = $('#photo').width()*440/$('#photo').height()
                $('#photo + div > img').css({
                    width: Math.round(scaleX * w) + 'px',
                    height: Math.round(scaleY * 400) + 'px',
                    marginLeft: '-' + Math.round(scaleX * selection.x1) + 'px',
                    marginTop: '-' + Math.round(scaleY * selection.y1) + 'px'
                });}
        }
        
        function store(img, selection){
            x1 = selection.x1;
            y1 = selection.y1;
            x2 = selection.x2;
            y2 = selection.y2;
        }
        
        $('<div><img src="bells.png" style="position: relative;" /><div>')
            .css({
                float: 'left',
                position: 'relative',
                overflow: 'hidden',
                width: '270px',
                height: '270px'
            })
            .insertAfter($('#photodiv'));
    }
    
    $('#thumb').click(function(){
        var req = {};
        req['action'] = 'editthumbnail';
        req['x1'] = x1;
        req['x2'] = x2;
        req['y1'] = y1;
        req['y2'] = y2;
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