$(document).ready(function(){
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
                success: function(response){signlog(response);},
                error: function (jqXHR, textStatus, errorThrown) {
                alert("error!");
                alert(jqXHR.responseText);},
            })
        }
    })
    
    $('#username').keypress(function(event){
        if (event.keyCode == 13){
            $('#loginbutton').click();
        }
    })
    
    $('#password').keypress(function(event){
        if (event.keyCode == 13){
            $('#loginbutton').click();
        }
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
        var state = $('#inState').val();
        var zip = $('#inzip').val();
        var altext = "";
        if (un.length < 6 || un.length > 15){altext += "Your username must be between 6 and 15 characters!\n";}
        if(pw.length < 6 || pw.length > 15){altext += "Your password must be between 6 and 15 characters!\n";}
        if(pw != cpw){altext += "Your password does not match your confirm password!\n";}
        if(first == ""){altext += "Your must enter your first name!\n";}
        if(last == ""){altext += "Your must enter your last name!\n";}
        if(!$('#inmale').is(':checked') && !$('#infemale').is(':checked')){
            altext += "Your must select your gender!";}
        console.log(altext);
        if(altext == ""){
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
                success: function(response){signlog(response);},
                error: function (jqXHR, textStatus, errorThrown) {
                alert("error!");
                alert(jqXHR.responseText);},
            })
        }else{
            alert(altext);
            resetpass();
        }
    })
    
    function resetpass(){
        $('#signpass').val("");
        $('#conpass').val("");
    }
    
    function signlog(response){
        if(response == "login_success"){
            updateHTML();
        }
    }
    
    function updateHTML(){
        window.location.replace('WebsiteMockup.html');
    }
})