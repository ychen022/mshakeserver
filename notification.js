$('document').ready(function(){
    function notifParser(notifiction){
        var invite = '<div class="header">Add</div>'
        var join = '<div class="header">Join</div>'
        var num = notifiction.length;
        for(var i=num-1; i>=0; i--){
            var noti = notifiction[i];
            var type = noti.type
            if(type == "joinDecisionSend"){
                join += '<a id="join_decision" class="notif_item">\
                            Your request to join \
                            <span class="group_name">'
                            + getGruopMember(noti.group)
                            +'</span>\
                            has been '+getJoinDecision(noti.decisionType)+'.\
                            </a>'
            }else if(type == "joinDecisionReceive"){
                join += '<a id="join_decision" class="notif_item">\
                            <span class="group_name">'
                            + getGruopMember(noti.group)+'\'s'
                            +'</span>\
                            invitation has been '+getJoinDecision(noti.decisionType)+'.\
                            </a>'
            }else if(type == "inviteDecsion"){
                invite += '<a id="add_decision" class="notif_item">\
                            The request to add \
                            <span class="group_name">+getGruopMember(noti.group)+</span>\
                            to your group has been'+getInviteDecision(noti.decisionType)+'.\
                            </a>'
            }else if(type == "joinRequest"){
                join += '<a id="join_request" val='+noti.groupID+' class="notif_item" name="joinrequest"><span class="group_name">'
                            + getGruopMember(noti.group);
                            + '</span>\
                            invite you to join their group.\
                            </a>'
            }else if(type == "inviteRequest"){
                invite += '<a id="add_request" val='+noti.groupID+'class="notif_item" name="addrequest">'
                            +noti.initiator.firstname+' '+noti.initiator.lastname+'\'s send a request to add\
                            <span class="group_name">'+getGruopMember(noti.group)+'</span>\
                            to your group.\
                            </a>'
            }else{alert("Yang Chen is stupid!")}
            join += $('.join').html();
            invite += $('.add').html();
            $('.join').html(join);
            $('.add').html(invite);
        }
    }
    
    $('.notif_item[name=addrequest]').click(function(){
        var req = {};
        req['action'] = "getGroup";
        req['groupID'] = $(this).val();
        $(this).remove();
        $.ajax({
            type: "POST",
            url: "door.php",
            data: req,
            dataType: 'JSON',
            async: false,
            success: function(response){addRequestParser(response);},
            error: function (jqXHR, textStatus, errorThrown) {
            alert("error!");
            alert(jqXHR.responseText);},
            complete: function(){setTimeout(function(){get();}, 5000);},
        })
    })
    
    $('.notif_item[name=joinrequest]').click(function(){
        var req = {};
        req['action'] = "getGroup";
        req['groupID'] = $(this).val();
        $(this).remove();
        $.ajax({
            type: "POST",
            url: "door.php",
            data: req,
            dataType: 'JSON',
            async: false,
            success: function(response){joinRequestParser(response);},
            error: function (jqXHR, textStatus, errorThrown) {
            alert("error!");
            alert(jqXHR.responseText);},
            complete: function(){setTimeout(function(){get();}, 5000);},
        })
    })
    
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
        if(num == 2){name = group[0].firstname+' '+group[0].lastname+' and '
            +group[1].firstname+' '+group[1].lastname}
        else{
            for(var i=0; i<num-1; i++){name += group[i].firstname+' '+group[i].lastname+', ';}
            name += 'and '+group[num-1].firstname+' '+group[num-1].lastname;
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
        matchParserNoti(result.notification);
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
        $('#join_decision').remove();
        $('#add_decision').remove();
    }
})