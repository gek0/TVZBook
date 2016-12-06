$(document).ready(function() {
    /**
     *  function to send post data
     */
    function data_post_sender(data, redirect, redirectLocation){
        $.ajax({
            type: 'POST',
            url: 'ajax_functions.php',
            dataType: 'json',
            data: data,
            success: function(response, textStatus, jqXHR) {
                if(response.status == 0){
                    $("#notification-data").attr("class", "notification-container success-container");
                    $("#notification-data").text(response.message).fadeIn(2000);

                    setTimeout(function(){
                        $("#notification-data").attr("class", "notification-container");
                        $("#notification-data").empty();
                    }, 3000);

                    if(redirect === true && redirectLocation !== ""){
                        setTimeout(function(){
                            window.location.replace(redirectLocation);
                        }, 1500);
                    }
                }
                else{
                    $("#notification-data").attr("class", "notification-container failure-container");
                    $("#notification-data").text(response.message).fadeIn(2000);

                    setTimeout(function(){
                        $("#notification-data").attr("class", "notification-container");
                        $("#notification-data").empty();
                    }, 3000);
                }
            },
            error: function(response, textStatus, jqXHR) {
                $("#notification-data").attr("class", "notification-container failure-container");
                $("#notification-data").text("Dogodila se greška. Pokušajte kasnije.").fadeIn(2000);
                console.log(response);

                setTimeout(function(){
                    $("#notification-data").attr("class", "notification-container");
                    $("#notification-data").empty();
                }, 3000);
            }
        });
    }

    /**
     *	user login
     */
    $("#login-button").click(function(e) {
        e.preventDefault();

        var data = $("#login-form").serialize();        
        data_post_sender(data, true, "index.php");
    });

    /**
     *  user registration
     */
    $("#register-button").click(function(e) {
        e.preventDefault();

        var data = $("#register-form").serialize();        
        data_post_sender(data, false, "");
    });

});