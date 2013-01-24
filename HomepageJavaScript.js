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
        var alt = false;
        if (un.length < 6 || un.length > 15){
            $('#signuser').next().html("Username must be 6-15 characters");
            alt = true;}
        else{$('#signuser').next().html("")}
        if(pw.length < 6 || pw.length > 15){
            $('#signpass').next().html("Password must be 6-15 characters!")
            alt = true;}
        else{$('#signpass').next().html("")}
        if(pw != cpw){
            $('#conpass').next().html("Password does not match");
            alt = true;}
        else{$('#conpass').next().html("")}
        if(first == ""){
            $('#infirst').next().html("Must enter firstname!");
            alt = true;}
        else{$('#infirst').next().html("")}
        if(last == ""){
            $('#inlast').next().html("Must enter lastname!");
            alt = true;}
        else{$('#inlast').next().html("")}
        if(!$('#inmale').is(':checked') && !$('#infemale').is(':checked')){
            $('#infemale').next().html("Must choose a gender!");
            alt = true;}
        else{$('#infemale').next().html("")}
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
                success: function(response){signlog(response);},
                error: function (jqXHR, textStatus, errorThrown) {
                alert("error!");
                alert(jqXHR.responseText);},
            })
        }else{
            resetpass();
            alt=true;
        }
    })
    
    function resetpass(){
        $('#signpass').val("");
        $('#conpass').val("");
    }
    
    function signlog(response){
        if(response == "login_success"){
            updateHTML();
        }else{
			alert(response);
		}
    }
    
    function updateHTML(){
        window.location.replace('WebsiteMockup.html');
    }
})