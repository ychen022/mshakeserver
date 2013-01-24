$('document').ready(function(){
    $('#dropdown_list').hide();
    $('#useraccount').click(function(){
        $('#dropdown_list').toggle('slow');
    })
    
    $('.memberfull_list').hide();
    $('.group').click(function(){
        $(this).parent().children('.memberfull_list').toggle('slide');
    })
    
    var req = {};
    req['action'] = 'init'
    $.ajax({
        url: 'door.php',
        data: req,
        type: "POST",
        datatype: "JSON",
        success: function(response){updateOption(response)},
        error: function (jqXHR, textStatus, errorThrown) {
        alert("error!");
        alert(jqXHR.responseText);},
    })
    
    function updateOption(response){
        var result = $.parseJSON(response);
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
    
    $('#logout').click(function(){
        var req = {};
        req['action'] = "logout";
        $.ajax({
                url: 'door.php',
                data: req,
                type: "POST",
                datatype: "html",
                success: function(response){logOut(response)},
                error: function (jqXHR, textStatus, errorThrown) {
                alert("error!");
                alert(jqXHR.responseText);},
        })
    });
    
    function logOut(response){
        if(response == "logout_success"){window.location.replace('index.html');}
        else{alert(response)}
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
        if(!checkZip()){altext += 'You must enter a 5 digit number in the zip textbox\n'}
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
                success: function(response){infoParser(response);},
                error: function (jqXHR, textStatus, errorThrown) {
                alert("error!");
                alert(jqXHR.responseText);},
            })
        }
        else{alert(altext);}
    })
    
    $('#cuisinesAll').click(function(){
        if($('#cuisinesAll').is(':checked')){$('input[name=cuisine]').attr('checked',true);}
        else{$('input[name=cuisine]').attr('checked',false);}
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
        else if($('#people_3').is(':checked')){return $('#people_3').next().html();}
        else {return null;}
    }
    
    function checkNumber(){
        if($('#number_1').is(':checked')){return $('#number_1').next().html();}
        else if($('#number_2').is(':checked')){return $('#number_2').next().html();}
        else {return null;}
    }
    
    function addOption(input, array){
        if(input.is(':checked')){return 1;}
        else{return 0;}
    }
    
    function infoParser(response){
        var match = $.parseJSON(response).match;
        var divinfo = "";
        for(var i=0; i<10; i++){
            var g = match[0];
            var groupinfo = '<div class="group_span">\
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
            divinfo += groupinfo;
        }
        divinfo += '<script>\
                        $(".memberfull_list").hide();\
                        $(".group").click(function(){\
                        $(this).parent().children(".memberfull_list").toggle("slide");\
                        })</script>'
        $('#match').html(divinfo);
        $('#dropdown_list').hide();
        setTimeout(function(){get();}, 5000)
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
    
    function get(){
        if (shaking()){
            var req = {};
            req['action'] = 'get';
            var feedback = $.ajax({
                type: "POST",
                url: "door.php",
                data: req,
                dataType: 'JSON',
                async: false,
                success: function(response){infoParser(response);},
                error: function (jqXHR, textStatus, errorThrown) {
                alert("error!");
                alert(jqXHR.responseText);},
            }).complete(function(){
                setTimeout(function(){get();}, 5000);
            })
        }
    }
    
    function shaking(){
        return ($('#ringer').val()=="shaking");
    }
})