$(document).ready(function() {
    /**
     *  send post data
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
     *  generate comment output
     */
    function create_comment_placeholder(id, avatar, post_text, date_created, comment_number, like_number, full_name){ 
        if(avatar != '') {
            avatar_img = '<img class="img-responsive thumbnail-image" src="'+ avatar +'" />';
        }
        else{
            avatar_img = '<img class="img-responsive thumbnail-image" src="images/no_image.jpg" />';
        }

        date_formated = moment(date_created).format('D.M.Y H:mm') + 'h';

        return '<div class="row comment-container">' +
                    '<div class="col-md-3 right-border text-center">' +
                        avatar_img +
                        full_name +                   
                    '</div>' +
                    '<div class="col-md-9">' +
                        '<strong>Objavljeno: </strong>' +
                        date_formated + 
                        '<hr>' +
                        post_text +
                    '</div>' +
                '</div>';
    }

    /**
     *  load more posts
     */
    function post_load_more(request, start, limit){
        $.ajax({
            type: 'POST',
            url: 'ajax_functions.php',
            dataType: 'json',
            data: {
                request: request,
                start: start,
                limit: limit
            },
            success: function(response, textStatus, jqXHR) {
                if(response.status == 0){
                    start = parseInt($('#start').val());
                    limit = parseInt($('#limit').val());
                    $('#start').val(start + limit);

                    response.message.forEach(function(item, index){
                        if ($("div.comment-container").length){
                            $("div.comment-container:last").after(create_comment_placeholder(item['id'], item['avatar'], 
                                                                                    item['post_text'], item['date_created'], 
                                                                                    item['comment_number'], item['like_number'], 
                                                                                    item['full_name']));    
                        }
                        else{
                            $("div.container-comments").html(create_comment_placeholder(item['id'], item['avatar'], 
                                                                                    item['post_text'], item['date_created'], 
                                                                                    item['comment_number'], item['like_number'], 
                                                                                    item['full_name']));
                        }

                        $("div.ontainer-comments:last").hide().fadeIn(1000);
                    });

                    $("html, body").animate({ scrollTop: $(document).height() - $(window).height() }, 500, 'linear');
                }
                else{
                    $("#notification-data-load").attr("class", "notification-container failure-container");
                    $("#notification-data-load").text(response.message).fadeIn(2000);
                    $("#load-more-button").prop("disabled", true);

                    setTimeout(function(){
                        $("#notification-data-load").attr("class", "notification-container");
                        $("#notification-data-load").empty();
                    }, 3000);
                }
            },
            error: function(response, textStatus, jqXHR) {
                console.log(response.status);
                console.log(response.message);
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

    /**
     *  new post
     */
    $("#new-post-button").click(function(e) {
        e.preventDefault();

        var data = $("#new-post-form").serialize();        
        data_post_sender(data, false, "");
    });

    /**
     *  load more on newsfeed
     */
    $("#load-more-button").click(function(e) {
        e.preventDefault();


        request = $('#load-more-request').val();
        start = $('#start').val();
        limit = $('#limit').val();

        post_load_more(request, start, limit);
    });

});