$(document).ready(function(){
    var req = {};
    req['action'] = "usercheck"
    $.ajax({
        type: "POST",
        url: "door.php",
        data: req,
        dataType: 'text',
        error: function (jqXHR, textStatus, errorThrown) {
        alert("error!");
        alert(jqXHR.responseText);},
    })
    
    $('#signup').hide();
    $('#signupbutton').click(function(){
        $('#signup').show(300);
    })
    
    $('#incancel').click(function(){
        $('#signup').hide();
    })
    
    $('#loginbutton').click(function(){
        var un = $('#inusername').val();
        var pw = $('#inpassword').val();
        if(un != "" & pw != ""){
            var req = {};
            req['action'] = 'login';
            req['username'] = un;
            req['password'] = pw;
            $.ajax({
                url: 'door.php',
                data: req,
                type: "POST",
                datatype: "text",
                success: function(response){login(response);},
                error: function (jqXHR, textStatus, errorThrown) {
                alert("error!");
                alert(jqXHR.responseText);}
            })
        }
    })
        
    $('#inusername').keyup(function(event){
        if (event.keyCode == 13){
            $('#loginbutton').click();
        }
        else {
            if($(this).val()==""){$(this).css('color', '#c8c8c8')}
            else{$(this).css("color", "black")}
        }
    })
    
    $('#inpassword').keyup(function(event){
        if (event.keyCode == 13){
            $('#loginbutton').click();
        }
        else {
            if($(this).val()==""){$(this).css('color', '#c8c8c8')}
            else{$(this).css("color", "black")}
        }
    })
    
    function login(response){
        if(response == "login_success"){
            updateHTML();
        }
        else{
            $('loginfo').html("Incorrect Log in Information");
        }
    }
    
    function updateHTML(){
        window.location.replace('userpage.html');
    }
    
    $('#signuser').keyup(function(){
        if($(this).val()==""){$(this).css('color', '#c8c8c8')}
        else{$(this).css("color", "#888888")}
    })
 
    $('#inemail').keyup(function(){
        if($(this).val()==""){$(this).css('color', '#c8c8c8')}
        else{$(this).css("color", "#888888")}
    })
 
    $('#signpass').keyup(function(){
        if($(this).val()==""){$(this).css('color', '#c8c8c8')}
        else{$(this).css("color", "#888888")}
    })
    
    $('#conpass').keyup(function(){
            if($(this).val()==""){$(this).css('color', '#c8c8c8')}
            else{$(this).css("color", "#888888")}
    })
    
    $('#infirst').keyup(function(){
            if($(this).val()==""){$(this).css('color', '#c8c8c8')}
            else{$(this).css("color", "#888888")}
    })
    
    $('#inlast').keyup(function(){
            if($(this).val()==""){$(this).css('color', '#c8c8c8')}
            else{$(this).css("color", "#888888")}
    })
    
    $('#inphone').keyup(function(){
            if($(this).val()==""){$(this).css('color', '#c8c8c8')}
            else{$(this).css("color", "#888888")}
    })
    
    $('#inaddress').keyup(function(){
            if($(this).val()==""){$(this).css('color', '#c8c8c8')}
            else{$(this).css("color", "#888888")}
    })
    
    $('#incity').keyup(function(){
            if($(this).val()==""){$(this).css('color', '#c8c8c8')}
            else{$(this).css("color", "#888888")}
    })
    
    $('#instate option').css('color', '#888888')
    
    $('#instate').change(function(){
            if($(this).val()==""){$(this).css('color', '#c8c8c8')}
            else{$(this).css("color", "#888888")}
    })
    
    $('#inzip').keyup(function(){
            if($(this).val()==""){$(this).css('color', '#c8c8c8')}
            else{$(this).css("color", "#888888")}
    })
    
    $('#signuser').blur(function(){
        var un = $('#signuser').val();
        var req = {};
        req['action'] = "checkusername";
        req['username'] = $('#signuser').val();
        if (un.length < 6 || un.length > 15){
            $('#signuser').next().html("Username must be 6-15 characters!");
            alt = true;}
        else{
            $('#signuser').next().html("")
            $.ajax({
                url: 'door.php',
                data: req,
                type: "POST",
                datatype: "text",
                success: function(response){checkusername(response);},
                error: function (jqXHR, textStatus, errorThrown) {
                alert("error!");
                alert(jqXHR.responseText);}
            })
        }
    })
    
    function checkusername(response){
        if(response=="bad"){$('#signuser').next().html("Someone else picked the same username!")}
        else if(response=="good"){
            $('#signuser').next().html("You can use the username!");
            $('#signuser').next().css('color', '#7b9a25');
            }
        else{}
    }
    
    $('#signpass').blur(function(){
        var pw = $('#signpass').val();
        var cpw = $('#conpass').val();
        if(pw.length < 6 || pw.length > 15){
            $('#signpass').next().html("Password must be 6-15 characters!")
            alt = true;}
        else{$('#signpass').next().html("")}
        if(pw != cpw){
            $('#conpass').next().html("Password does not match!");
            alt = true;}
        else{$('#conpass').next().html("")}
    })
    
    $('#conpass').blur(function(){
        var pw = $('#signpass').val();
        var cpw = $('#conpass').val();
        if(pw.length < 6 || pw.length > 15){
            $('#signpass').next().html("Password must be 6-15 characters!")
            alt = true;}
        else{$('#signpass').next().html("")}
        if(pw != cpw){
            $('#conpass').next().html("Password does not match!");
            alt = true;}
        else{$('#conpass').next().html("")}
    })
    
    $('#infirst').blur(function (){
        var first = $('#infirst').val();
        if(first == ""){
            $('#infirst').next().html("Must enter your firstname!");
            alt = true;}
        else{$('#infirst').next().html("")}
    })
    
    $('#inlast').blur(function (){
        var last = $('#inlast').val();
        if(last == ""){
            $('#inlast').next().html("Must enter your firstname!");
            alt = true;}
        else{$('#inlast').next().html("")}
    })
    
    $('#insubmit').click(function(){
        var req = {};
        req['action'] = "signup";
        var un = $('#signuser').val();
        var pw = $('#signpass').val();
        var cpw = $('#conpass').val();
        var first = $('#infirst').val();
        var last = $('#inlast').val();
        var email = $('#inemail').val();
        var phone = $('#inphone').val();
        var address = $('#inaddress').val();
        var city = $('#incity').val();
        var state = $('#instate').val();
        var zip = $('#inzip').val();
        var alt = false;
        if ($('#signpass').next().html()!=""){alt = true;}
        else if (un.length < 6 || un.length > 15){
            $('#signuser').next().html("Username must be 6-15 characters!");
            alt = true;}
        else{$('#signuser').next().html("")}
        if(pw.length < 6 || pw.length > 15){
            $('#signpass').next().html("Password must be 6-15 characters!")
            alt = true;}
        else{$('#signpass').next().html("")}
        if(pw != cpw){
            $('#conpass').next().html("Password does not match!");
            alt = true;}
        else{$('#conpass').next().html("")}
        if(first == ""){
            $('#infirst').next().html("Must enter your firstname!");
            alt = true;}
        else{$('#infirst').next().html("")}
        if(last == ""){
            $('#inlast').next().html("Must enter your lastname!");
            alt = true;}
        else{$('#inlast').next().html("")}
        if(!$('#inmale').is(':checked') && !$('#infemale').is(':checked')){
            $('#infemale').parent().next().html("Must choose a gender!");
            alt = true;}
        else{$('#infemale').parent().next().html("")}
        if(!alt){
            req['username'] = un;
            req['password'] = pw;
            req['firstname'] = first;
            req['lastname'] = last;
            if ($('#inmale').is(':checked')){req['gender']="Male"}
            else if($('#infemale').is(':checked')){req['gender']="Female"}
            req['email'] = email;
            req['phone'] = phone;
            req['address'] = address;
            req['city'] = city;
            req['state'] = state;
            req['zipcode'] = zip;
            $.ajax({
                url: 'door.php',
                data: req,
                type: "POST",
                datatype: "text",
                success: function(response){signup(response);},
                error: function (jqXHR, textStatus, errorThrown) {
                alert("error!");
                alert(jqXHR.responseText);},
            })
        }else{
            resetpass();
        }
    })
    
    function resetpass(){
        $('#signpass').val("");
        $('#conpass').val("");
    }
    
    function signup(response){
        if(response == "signup_success"){
            var req = {};
            req['action'] = 'login';
            req['username'] = $('#signuser').val();
            req['password'] = $('#signpass').val();
            $.ajax({
                url: 'door.php',
                data: req,
                type: "POST",
                datatype: "text",
                success: function(response){login(response);},
                error: function (jqXHR, textStatus, errorThrown) {
                alert("error!");
                alert(jqXHR.responseText);}
            })
        } else {
            alert(response);
        }
    }
})