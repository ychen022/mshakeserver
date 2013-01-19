$(document).ready(function(){
    $('#logbutton').click(function(){
        var un = $('#username').val();
        var pw = $('#password').val();
        if(un != "" & pw != ""){
            var req = {};
            req['action'] = 'login';
            req['username'] = un;
            req['password'] = pw;
            $.post('door.php', req, function(data){$('#doc').html(data)}, 'html');
        }
    })
    
    $('#username').keypress(function(event){
        if (event.keyCode == 13){
            $('#logbutton').click();
        }
    })
    
    $('#password').keypress(function(event){
        if (event.keyCode == 13){
            $('#logbutton').click();
        }
    })
    
    $('#submit').click(function(){
        var req = {};
        req['action'] = "signup";
        var un = $('#signuser').val();
        var pw = $('#signpass').val();
        var cpw = $('#conpass').val();
        var first = $('#first').val();
        var last = $('#last').val();
        var email = $('#email').val();
        var phone = $('#phone').val();
        var address = $('#address').val();
        var city = $('#city').val();
        var state = $('#state').val();
        var zip = $('#zip').val();
        if (un.length < 6 || un.length > 15){
            alert("Your username must be between 6 and 15 characters!");
            resetpass();}
        else if(pw.length < 6 || pw.length > 15){
            alert("Your password must be between 6 and 15 characters!");
            resetpass();}
        else if(pw != cpw){
            alert("Your password does not match your confirm password!");
            resetpass();}
        else if(first == ""){
            alert("Your must enter yur first name!");
            resetpass();}
        else if(last == ""){
            alert("Your must enter yur last name!");
            resetpass();}
        else if(!$('#male').is(':checked') && !$('#female').is(':checked')){
            alert("Your must select yur gender!");
            resetpass();}
        else{
            req['username'] = un;
            req['password'] = pw;
            req['firstname'] = first;
            req['lastname'] = last;
            if ($('#male').is(':checked')){req['gender']="Male"}
            else if($('#female').is(':checked')){req['gender']="Female"}
            req['email'] = $('#email').val();
            req['phone'] = $('#phone').val();
            req['address'] = $('#address').val();
            req['city'] = $('#city').val();
            req['state'] = $('#state').val();
            req['zipcode'] = $('#zip').val();
            $.post('door.php', req, function(data){$('#doc').html(data)}, 'html');
        }
    })
    
    function resetpass(){
        $('#signpass').val("");
        $('#conpass').val("");
    }
})