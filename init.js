$('document').ready(function(){
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
    
    
})



    $('#home').click(function(){
        window.location.replace('userpage.html');
    })
    
    $('#profile').click(function(){
        window.location.replace('profile.html');
    })