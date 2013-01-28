$('document').ready(function(){
    $('#current_group').hide();
    $('#option').show();
    
    var req = {};
    req['action'] = 'init'
    $.ajax({
        url: 'door.php',
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
    
    $('#requsetpopup').hide();
    
    $('#signuser').keyup(function(){
            if($(this).val()==""){$(this).css('color', '#c8c8c8')}
            else{$(this).css("color", "#888888")}
    })
    
    $('#signpass').keyup(function(){
            if($(this).val()==""){$(this).css('color', '#c8c8c8')}
            else{$(this).css("color", "#888888")}
    })
    
    $('.memberfull_list').hide();
    $('.group').click(function(){
        $(this).parent().children('.memberfull_list').toggle('slide');
    })
    
    function checkget(response){
        var result = $.parseJSON(response);
        if(result.wantsget==0){updateOption(result.option)}
        else if(result.wantsget==1){
            notifParser(result.get.notification);
            matchParser(result.get.match);
			groupParser(result.get.group);
			$('#option').hide();
			$('#current_group').show();
            get();
        }
    }
    
    function updateOption(result){
        $('#address').val(result.address);
        $('#city').val(result.city);
        $('#state').val(result.state);
        $('#zip').val(result.zipcode);
        $('#distance').val(result.distance);
        $('#cuisine_1').attr('checked', typeParser(result.type.cuisine_1));
        $('#cuisine_2').attr('checked', typeParser(result.type.cuisine_2));
        $('#cuisine_3').attr('checked', typeParser(result.type.cuisine_3));
        $('#cuisine_4').attr('checked', typeParser(result.type.cuisine_4));
        $('#cuisine_5').attr('checked', typeParser(result.type.cuisine_5));
        $('#cuisine_6').attr('checked', typeParser(result.type.cuisine_6));
        $('#cuisine_7').attr('checked', typeParser(result.type.cuisine_7));
        $('#cuisine_8').attr('checked', typeParser(result.type.cuisine_8));
        $('#cuisine_9').attr('checked', typeParser(result.type.cuisine_9));
        $('#cuisine_10').attr('checked', typeParser(result.type.cuisine_10));
        $('#cuisine_11').attr('checked', typeParser(result.type.cuisine_11));
        $('#cuisine_12').attr('checked', typeParser(result.type.cuisine_12));
        genderParser(result.gender);
        numberParser(result.groupsize);
        $('#pricemax').val(result.pricemax);
        $('#pricemin').val(result.pricemin);
    }
    
    function typeParser(input){
        if(input==1){return true;}
        else{return false;}
    }
    
    function genderParser(input){
        if(input=='male'){$('#people_1').attr('checked', true)}
        else if(input=='female'){$('#people_2').attr('checked', true)}
        else if(input=='any'){$('#people_3').attr('checked', true)}
    }
    
    function numberParser(input){
        if(input=='2'){$('#number_1').attr('checked', true)}
        if(input=='0'){$('#number_2').attr('checked', true)}
    }
    
    $('#shake').click(function(){
        var req = {};
        var altext = "";
        req['action'] = "startmatch"
        req['address'] = $('#address').val();
        if(checkEmpty(req['address'])){altext += 'You must enter in the address textbox\n'}
        req['city'] = $('#city').val();
        if(checkEmpty(req['city'])){altext += 'You must enter in the city textbox\n'}
        req['state'] = $('#state').val();
        req['zipcode'] = $('#zip').val();
        if(!checkZip()){altext += 'You must enter a 5 digit number in the zip code textbox\n'}
        req['distance'] = $('#distance').val();
        if(!checkNumberReg(req['distance'])){altext += 'You must enter a number in the distance textbox\n'}
        req['type'] = checkType();
        req['people'] = checkPeople();
        req['number'] = checkNumber();
        req['pricemin'] = $('#pricemin').val();
        req['pricemax'] = $('#pricemax').val();
        if(!checkNumberReg(req['pricemin'])||!checkNumberReg(req['pricemax'])){
            altext += 'You must enter number in price textboxes\n'}
        if(altext==""){
            $.ajax({
                url: 'door.php',
                data: req,
                type: "POST",
                datatype: "JSON",
                success: function(response){
                    result = $.parseJSON(response);
                    matchParser(result.match);
                    groupParser(result.group);
                    notifParser(result.notification);
                    $('#current_group').show();
                    $('#option').hide();},
                error: function (jqXHR, textStatus, errorThrown) {
                alert("error!");
                alert(jqXHR.responseText);},
                complete: function(){get();},
            })
        }
        else{alert(altext);}
    })
    
    $('#cuisinesAll').click(function(){
        if($('#cuisinesAll').is(':checked')){$('input[name=cuisine]').attr('checked',true);}
        else{$('input[name=cuisine]').attr('checked',false);}
    })
    
    $('#refresh').click(function(){
        var req = {};
        req['action'] = "refreshmatch";
        $.ajax({
            url: 'door.php',
            data: req,
            type: "POST",
            datatype: "JSON",
            success: function(response){
                result = $.parseJSON(response);
				matchParser(result.match);},
            error: function (jqXHR, textStatus, errorThrown) {
            alert("error!");
            alert(jqXHR.responseText);},
        })
    })
    
    function checkEmpty(input){
        return (input == "")
    }
    
    function checkNumberReg(input){
        var intReg = RegExp(/[0-9]+$/);
        var floatReg = RegExp(/[0-9]*\.[0-9]+$/);
        var result = (intReg.test(input)||floatReg.test(input))
        return result
    }
    
    function checkZip(){
        var zipReg = RegExp(/[0-9]{5}$/);
        return zipReg.test($('#zip').val());
    }
    
    function checkType(){
        var type = {};
        type['cuisine_1'] = addOption($('#cuisine_1'));
        type['cuisine_2'] = addOption($('#cuisine_2'));
        type['cuisine_3'] = addOption($('#cuisine_3'));
        type['cuisine_4'] = addOption($('#cuisine_4'));
        type['cuisine_5'] = addOption($('#cuisine_5'));
        type['cuisine_6'] = addOption($('#cuisine_6'));
        type['cuisine_7'] = addOption($('#cuisine_7'));
        type['cuisine_8'] = addOption($('#cuisine_8'));
        type['cuisine_9'] = addOption($('#cuisine_9'));
        type['cuisine_10'] = addOption($('#cuisine_10'));
        type['cuisine_11'] = addOption($('#cuisine_11'));
        type['cuisine_12'] = addOption($('#cuisine_12'));
        return type;
    }
    
    function checkPeople(){
        if($('#people_1').is(':checked')){return $('#people_1').next().html();}
        else if($('#people_2').is(':checked')){return $('#people_2').next().html();}
        else if($('#people_3').is(':checked')){return 'any';}
        else {return null;}
    }
    
    function checkNumber(){
        if($('#number_1').is(':checked')){return $('#number_1').next().html();}
        else if($('#number_2').is(':checked')){return 0;}
        else {return null;}
    }
    
    function addOption(input, array){
        if(input.is(':checked')){return 1;}
        else{return 0;}
    }
    
    function matchParser(match){
        var groupinfo = '';
        var num = match.length;
        for(var i=0; i<num; i++){
            var g = match[i];
            groupinfo += '<div class="group_span" value='+g.group.groupID+'>\
                            <div class="group_header">\
                                <div class="add">+</div>\
                            </div>\
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
        }
        groupinfo += '<script>\
                        $(".memberfull_list").hide();\
                        $(".group").click(function(){\
                        $(this).parent().children(".memberfull_list").toggle("slide");\
                        });\
                        $(".add").click(function(){\
                            var req = {};\
                            req["action"] = "startinvite";\
                            req["groupID"] = $(this).parent().parent().attr("value");\
                            $.ajax({\
                                type: "POST",\
                                url: "door.php",\
                                data: req,\
                                dataType: "text",\
                                success: function(response){startInviteParser(response);},\
                                error: function (jqXHR, textStatus, errorThrown) {\
                                alert("error!");\
                                alert(jqXHR.responseText);},\
                            })\
                        });\
                        function startInviteParser(response){\
                            if(response=="startinvite_success"){}\
                            else{alert("Sorry! This group no longer exist!")}\
                        }\
					</script>'
        $('#matchinfo').html(groupinfo);
    }
    
    function groupParser(result){
        var html = '<div class="cugroup_header">\
                        Current group\
                    </div>\
                    <div id="info_header">\
                        Group info\
                    </div>\
                    <div class="group_info">\
                        <div class="distance">\
                            Average distance:'+result.group.avgdist+'\ miles\
                        </div>\
                        <div class="food_type">\
                            Food: '+result.group.foodtype[0]+' '+result.group.foodtype[1]+' '+result.group.foodtype[2]+'\
                        </div>\
                        <div class="price_range">\
                            Price range: $'+result.group.pricemin+'-$'+result.group.pricemax+'\
                        </div>\
                    </div>\
                    <ul class="current_group_list">';
        var num = result.group.nop;
        for(var i=0; i<num; i++){
            var g = result.member[i];
            var food = "";
            for(var j=0; j<g.foodtype.length; j++){
                food += foodtyping(g.foodtype[j])+' ';
            }
            var ready = ""
            if(g.ready == 1){ready = "member_ready";}
            html += '<li class="current_group_member '+ready+'">\
                        <a class="member_img">\
                            <img src="'+g.photolink+'">\
                        </a>\
                        <i id="readyicon" class="icon-ok-circle icon-2x"></i>\
                        <ul class="member_info">\
                            <li class="info_name">\
                                '+g.firstname+' '+g.lastname+'\
                            </li>\
                            <li class="info_other">\
                                <span class="info_price">'+g.gender+'</span>\
                                <span class="info_distance">'+g.distance+' miles</span>\
                            </li>\
                            <li class="info_food">\
                                '+food+'\
                            </li>\
                        </ul>\
                    </li>'
        }
        html += '</ul>\
                <a id="ready" class="btn btn-success">Ready !</a>';
        html += '<script>\
                $("#ready").click(function(){\
                var req = {};\
                req["action"] = "ready";\
                $.ajax({\
                    type: "POST",\
                    url: "door.php",\
                    data: req,\
                    error: function (jqXHR, textStatus, errorThrown) {\
                    alert("error!");\
                    alert(jqXHR.responseText);},\
                })\
                })\
                </script>'
        $('#current_group').html(html);
    }
    
    function foodtyping(input){
        if(input=="cuisine_1"){return "American";}
        else if(input=="cuisine_2"){return "Chinese";}
        else if(input=="cuisine_3"){return "French";}
        else if(input=="cuisine_4"){return "Italian";}
        else if(input=="cuisine_5"){return "Mexican";}
        else if(input=="cuisine_6"){return "Japanese";}
        else if(input=="cuisine_7"){return "Indian";}
        else if(input=="cuisine_8"){return "Cafe & Dessert";}
        else if(input=="cuisine_9"){return "Korean";}
        else if(input=="cuisine_10"){return "Asian";}
        else if(input=="cuisine_11"){return "Seafood";}
        else if(input=="cuisine_12"){return "vegetarian";}
        else{return null;}
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
                join += '<a name="join_request" value='+noti.groupID+' class="notif_item" name="joinrequest">\
                            <span class="group_name">'+getGroupMember(noti.group)+'</span>\
                            invite you to join their group.\
                            </a>'
            }else if(type == "inviteRequest"){
                invite += '<a name="add_request" value='+noti.groupID+'class="notif_item" name="addrequest">'
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
                success: function(response){infoParser(response);},
                error: function (jqXHR, textStatus, errorThrown) {
                alert("error!");
                alert(jqXHR.responseText);},
                complete: function(){setTimeout(function(){get();}, 5000);},
            })
        }
    }
    
    function infoParser(response){
        notifParser(response.notification);
        groupParser(response.group);
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
    
    $('#home').click(function(){
        window.location.replace('userpage.html');
    })
    
    $('#profile').click(function(){
        window.location.replace('profile.html');
    })
    
    $('#about').click(function(){
        window.location.replace('about.html');
    })
})