$(document).ready(function(){
    $("#submit-registration").click(function(){
         $.ajax({
             url: "login/register",
             data:{
               cmd: "register",
               username: $('[name="username"]').val(),
               password: $('[name="password"]').val(),
               name: $('[name="name"]').val(),
               surname: $('[name="surname"]').val(),
               mail: $('[name="mail"]').val(),
               creditCard: $('[name="creditCard"]').val(),
               creditCardNumber: $('[name="creditCardNumber"]').val(),
               city: $('[name="city"]').val(),
               cap: $('[name="cap"]').val(),
               street: $('[name="street"]').val(),
               streetNumber: $('[name="streetNumber"]').val()
             },
             dataType: 'json',
             success : function(data, state){
                 setError(data);
             },
             error : function(data, state){
                 setConfirm();
             }
         })
    });

    function setError(data){
     $("#confirm_registration").html(" ");

     if(data.username != undefined)
        $("#error_username").html("<p>"+data.username+"</p>");
     if(data.password != undefined)
        $("#error_password").html("<p>"+data.password+"</p>");
     if(data.name != undefined)
        $("#error_name").html("<p>"+data.name+"</p>");
     if(data.surname != undefined)
        $("#error_surname").html("<p>"+data.surname+"</p>");
     if(data.mail != undefined)
        $("#error_mail").html("<p>"+data.mail+"</p>");
     if(data.creditCard != undefined)
        $("#error_creditCard").html("<p>"+data.creditCard+"</p>");
     if(data.creditCardNumber != undefined)
        $("#error_creditCardNumber").html("<p>"+data.creditCardNumber+"</p>");
     if(data.city != undefined)
        $("#error_city").html("<p>"+data.city+"</p>");
     if(data.cap != undefined)
        $("#error_cap").html("<p>"+data.cap+"</p>");
     if(data.street != undefined)
        $("#error_street").html("<p>"+data.street+"</p>");
     if(data.streetNumber != undefined)
        $("#error_streetNumber").html("<p>"+data.streetNumber+"</p>");
    }

    function setErrorMod(data){
     $("#confirm_mod").html(" ");

     if(data.username != undefined)
        $("#error_username").html("<p>"+data.username+"</p>");
     if(data.password != undefined)
        $("#error_password").html("<p>"+data.password+"</p>");
     if(data.name != undefined)
        $("#error_name").html("<p>"+data.name+"</p>");
     if(data.surname != undefined)
        $("#error_surname").html("<p>"+data.surname+"</p>");
     if(data.mail != undefined)
        $("#error_mail").html("<p>"+data.mail+"</p>");
     if(data.creditCard != undefined)
        $("#error_creditCard").html("<p>"+data.creditCard+"</p>");
     if(data.creditCardNumber != undefined)
        $("#error_creditCardNumber").html("<p>"+data.creditCardNumber+"</p>");
     if(data.city != undefined)
        $("#error_city").html("<p>"+data.city+"</p>");
     if(data.cap != undefined)
        $("#error_cap").html("<p>"+data.cap+"</p>");
     if(data.street != undefined)
        $("#error_street").html("<p>"+data.street+"</p>");
     if(data.streetNumber != undefined)
        $("#error_streetNumber").html("<p>"+data.streetNumber+"</p>");
    }

    function setConfirm(){
        $("#confirm_registration").html("<p>Utente registrato con successo!<br /> Ora sei parte del sito!</p>");

        $("#error_username").html(" ");
        $("#error_password").html(" ");
        $("#error_name").html(" ");
        $("#error_surname").html(" ");
        $("#error_mail").html(" ");
        $("#error_creditCard").html(" ");
        $("#error_creditCardNumber").html(" ");
        $("#error_city").html(" ");
        $("#error_cap").html(" ");
        $("#error_street").html(" ");
        $("#error_streetNumber").html(" ");
    }

    function setConfirmModCliente(){
        $("#confirm_modification").html("<p>Informazioni personali modificate con successo!</p>");

        $("#error_name").html(" ");
        $("#error_surname").html(" ");
        $("#error_mail").html(" ");
        $("#error_city").html(" ");
        $("#error_cap").html(" ");
        $("#error_street").html(" ");
        $("#error_streetNumber").html(" ");
    }

    $("#modificaDati").click(function(){
         $.ajax({
             url: "utente/modificaDati",
             data:{
               cmd: "modificaDati",
               name: $('[name="name"]').val(),
               surname: $('[name="surname"]').val(),
               mail: $('[name="mail"]').val(),
               city: $('[name="city"]').val(),
               cap: $('[name="cap"]').val(),
               street: $('[name="street"]').val(),
               streetNumber: $('[name="streetNumber"]').val()
             },
             dataType: 'json',
             success : function(data, state){
                 setErrorMod(data);
             },
             error : function(data, state){
                 setConfirmModUtente();
             }
         })
    });

    $("#admin_registerUser").click(function(){
         $.ajax({
             url: "admin/amministraUtenti",
             data:{
               cmd: "register",
               username: $('[name="username"]').val(),
               password: $('[name="password"]').val(),
               name: $('[name="name"]').val(),
               surname: $('[name="surname"]').val(),
               mail: $('[name="mail"]').val(),
               creditCard: $('[name="creditCard"]').val(),
               creditCardNumber: $('[name="creditCardNumber"]').val(),
               city: $('[name="city"]').val(),
               cap: $('[name="cap"]').val(),
               street: $('[name="street"]').val(),
               streetNumber: $('[name="streetNumber"]').val()
             },
             dataType: 'json',
             success : function(data, state){
                 setError(data);
             },
             error : function(data, state){
                 setConfirm();
             }
         })
    });
 });


