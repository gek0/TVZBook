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
                        //is there any post before
                        if ($("div.post-container").length){
                            if(item['status'] == 'public'){
                                $("div.post-container:last").after(create_post_placeholder(item[0], item['avatar'], 
                                                                                        item['post_text'], item['date_created'], 
                                                                                        item['comment_number'], item['like_number'], 
                                                                                        item['full_name'], item['slug'], 'public'));                                 
                            }
                            else if(item['status'] == 'private' && (item['author_id'] == response.user_id)){
                                $("div.post-container:last").after(create_comment_placeholder(item[0], item['avatar'], 
                                                                                        item['post_text'], item['date_created'], 
                                                                                        item['comment_number'], item['like_number'], 
                                                                                        item['full_name'], item['slug'], 'private'));                                 
                            }
                            else{
                                return;
                            }
                        } //looks like not
                        else{
                            if(item['status'] == 'public'){
                                $("div.post-container:last").after(create_post_placeholder(item[0], item['avatar'], 
                                                                                        item['post_text'], item['date_created'], 
                                                                                        item['comment_number'], item['like_number'], 
                                                                                        item['full_name'], item['slug'], 'public'));                                 
                            }
                            else if(item['status'] == 'private' && (item['author_id'] == response.user_id)){
                                $("div.post-container:last").after(create_post_placeholder(item[0], item['avatar'], 
                                                                                        item['post_text'], item['date_created'], 
                                                                                        item['comment_number'], item['like_number'], 
                                                                                        item['full_name'], item['slug'], 'private'));                                 
                            }
                            else{
                                return;
                            }
                        }

                        $("div.post-container:last").hide().fadeIn(1000);
                    });

                    $("html, body").animate({ scrollTop: $(document).height() - $(window).height() }, 500, 'linear');

                    if(response.count < 5){
                        $("#notification-data-load").attr("class", "notification-container failure-container");
                        $("#notification-data-load").text(response.extra_message).fadeIn(2000);
                        $("#load-more-button").prop("disabled", true);

                        setTimeout(function(){
                            $("#notification-data-load").attr("class", "notification-container");
                            $("#notification-data-load").empty();
                        }, 3000);                        
                    }
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
     *  generate post output
     */
    function create_post_placeholder(id, avatar, post_text, date_created, comment_number, like_number, full_name, slug, status){ 
        if(avatar != '') {
            avatar_img = '<img class="img-responsive thumbnail-image" src="'+ avatar +'" />';
        }
        else{
            avatar_img = '<img class="img-responsive thumbnail-image" src="images/no_image.jpg" />';
        }

        date_formated = moment(date_created).format('D.M.Y H:mm') + 'h';

        if(status == 'public'){
            return '<div class="row post-container">' +
                        '<div class="col-md-3 right-border text-center">' +
                            avatar_img +
                            '<a href="profile.php?user=' + slug + '">' + full_name + '</a>' + 
                            '<br><i class="fa fa-heart" title="Broj sviđanja"></i> ' + like_number  + ' | ' +
                            '<i class="fa fa-pencil" title="Broj komentara"></i> ' + comment_number + '<br>' +
                            '<i class="fa fa-eye" title="Javna objava"></i>' +
                        '</div>' +
                        '<div class="col-md-9">' +
                            '<strong>Objavljeno: </strong>' +
                            date_formated + 
                            '<hr>' +
                            post_text +
                            '<div class="text-center">' +
                                '<a href="post.php?id=' + id + '">' +
                                    '<button class="inverse_main_small">Pregledaj <i class="fa fa-eye"></i></button>' +
                                '</a>' +
                            '</div>' +
                        '</div>' +
                    '</div>';
            }
            else{
            return '<div class="row post-container post-private">' +
                        '<div class="col-md-3 right-border text-center">' +
                            avatar_img +
                            '<a href="profile.php?user=' + slug + '">' + full_name + '</a>' + 
                            '<br><i class="fa fa-heart" title="Broj sviđanja"></i> ' + like_number  + ' | ' +
                            '<i class="fa fa-pencil" title="Broj komentara"></i> ' + comment_number + '<br>' +
                            '<i class="fa fa-eye-slash" title="Privatna objava"></i> Privatna objava' +
                        '</div>' +
                        '<div class="col-md-9">' +
                            '<strong>Objavljeno: </strong>' +
                            date_formated + 
                            '<hr>' +
                            post_text +
                            '<div class="text-center">' +
                                '<a href="post.php?id=' + id + '">' +
                                    '<button class="inverse_main_small">Pregledaj <i class="fa fa-eye"></i></button>' +
                                '</a>' +
                            '</div>' +
                        '</div>' +
                    '</div>';                
            }
    }

    /**
     *  generate comment output
     */
    function create_comment_placeholder(id, avatar, comment_text, date_created, full_name, slug){ 
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
                        '<a href="profile.php?user=' + slug + '">' + full_name + '</a>' +
                    '</div>' +
                    '<div class="col-md-9 text-left">' +
                        '<strong>Objavljeno: </strong>' +
                        date_formated + 
                        '<hr>' +
                        comment_text +
                    '</div>' +
                '</div>';
    }

    /**
     *  add new post
     */
    function new_post_create(data, redirect, redirectLocation){
        $.ajax({
            type: 'POST',
            url: 'ajax_functions.php',
            dataType: 'json',
            data: data,
            success: function(response, textStatus, jqXHR) {
                if(response.status == 0){
                    $("#notification-data").attr("class", "notification-container success-container");
                    $("#notification-data").text(response.message).fadeIn(2000);
                        //is there any post before
                    if ($("div.post-container").length){
                        if(response.post_data['status'] == 'public'){
                            var firstPost = $("div.post-container").first(); 
                            var newPost = $(create_post_placeholder(response.post_data[0], response.post_data['avatar'], 
                                                                                    response.post_data['post_text'], response.post_data['date_created'], 
                                                                                    response.post_data['comment_number'], response.post_data['like_number'], 
                                                                                    response.post_data['full_name'], response.post_data['slug'], 'public'));
                            newPost.hide().insertBefore(firstPost).fadeIn(2000);                                                          
                        }
                        else if(response.post_data['status'] == 'private' && (response.post_data['author_id'] == response.user_id)){
                            var firstPost = $("div.post-container").first(); 
                            var newPost = $(create_post_placeholder(response.post_data[0], response.post_data['avatar'], 
                                                                                    response.post_data['post_text'], response.post_data['date_created'], 
                                                                                    response.post_data['comment_number'], response.post_data['like_number'], 
                                                                                    response.post_data['full_name'], response.post_data['slug'], 'private'));
                            newPost.hide().insertBefore(firstPost).fadeIn(2000);                           
                        }
                        else{
                            return;
                        }
                    } //looks like not
                    else{
                        if(response.post_data['status'] == 'public'){
                            var postContainer = $("div.posts-list"); 
                            var newPost = $(create_post_placeholder(response.post_data[0], response.post_data['avatar'], 
                                                                                    response.post_data['post_text'], response.post_data['date_created'], 
                                                                                    response.post_data['comment_number'], response.post_data['like_number'], 
                                                                                    response.post_data['full_name'], response.post_data['slug'], 'public'));
                            newPost.hide().prependTo(commentPost).fadeIn(2000);                                  
                        }
                        else if(response.post_data['status'] == 'private' && (response.post_data['author_id'] == response.user_id)){
                            var postContainer = $("div.posts-list"); 
                            var newPost = $(create_post_placeholder(response.post_data[0], response.post_data['avatar'], 
                                                                                    response.post_data['post_text'], response.post_data['date_created'], 
                                                                                    response.post_data['comment_number'], response.post_data['like_number'], 
                                                                                    response.post_data['full_name'], response.post_data['slug'], 'private'));
                            newPost.hide().prependTo(postContainer).fadeIn(2000);                              
                        }
                        else{
                            return;
                        }
                    }             

                    $("#new-post-textarea").val('');

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
     *  add new post
     */
    function new_comment_create(data, redirect, redirectLocation){
        $.ajax({
            type: 'POST',
            url: 'ajax_functions.php',
            dataType: 'json',
            data: data,
            success: function(response, textStatus, jqXHR) {
                if(response.status == 0){
                    var commentsNumber = $("#comment-number-container").text();
                    var numberOfComments = parseInt(commentsNumber);
                    $("#notification-data").attr("class", "notification-container success-container");
                    $("#notification-data").text(response.message).fadeIn(2000);


                    //is there any post before
                    if ($("div.comment-container").length){
                        var firstComment = $("div.comment-container").first(); 
                        var newComment = $(create_comment_placeholder(response.comment_data[0], response.comment_data['avatar'],
                                                                        response.comment_data['comment_text'], response.comment_data['date_created'],
                                                                        response.comment_data['full_name'], response.comment_data['slug']));
                        newComment.hide().insertBefore(firstComment).fadeIn(2000);                                                          
 
                    }
                    else{
                        var commentContainer = $("div.comments-list"); 
                        var newComment = $(create_comment_placeholder(response.comment_data[0], response.comment_data['avatar'],
                                                                        response.comment_data['comment_text'], response.comment_data['date_created'],
                                                                        response.comment_data['full_name'], response.comment_data['slug']));
                        newComment.hide().prependTo(commentContainer).fadeIn(2000);
                    }             

                    $("#new-comment-textarea").val('');                    
                    $("#comment-number-container").text(numberOfComments + 1);

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
     *  add like to post
     */
    function add_like(postID, request){
        $.ajax({
            type: 'POST',
            url: 'ajax_functions.php',
            dataType: 'json',
            data: {
                post_id: postID,
                request: request
            },
            success: function(response, textStatus, jqXHR) {
                if(response.status == 0){
                    var likesNumber = $("#like-number-container").text();
                    var numberOfLikes = parseInt(likesNumber);
                    $("#notification-data").attr("class", "notification-container success-container");
                    $("#notification-data").text(response.message).fadeIn(2000);

                    $("#give-like-button").attr("title", "Ne sviđa mi se objava");                    
                    $("#give-like-button").attr("id", "remove-like-button");                    
                    $("#remove-like-button").html("<i class='fa fa-heart-o'></i>");
                    $("#like-number-container").text(numberOfLikes + 1);

                    setTimeout(function(){
                        $("#notification-data").attr("class", "notification-container");
                        $("#notification-data").empty();
                    }, 3000);
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
     *  remove like from post
     */
    function remove_like(postID, request){
        $.ajax({
            type: 'POST',
            url: 'ajax_functions.php',
            dataType: 'json',
            data: {
                post_id: postID,
                request: request
            },
            success: function(response, textStatus, jqXHR) {
                if(response.status == 0){
                    var likesNumber = $("#like-number-container").text();
                    var numberOfLikes = parseInt(likesNumber);
                    $("#notification-data").attr("class", "notification-container success-container");
                    $("#notification-data").text(response.message).fadeIn(2000);

                    $("#remove-like-button").attr("title", "Sviđa mi se objava");
                    $("#remove-like-button").attr("id", "give-like-button");
                    $("#give-like-button").html("<i class='fa fa-heart'></i>");
                    $("#like-number-container").text(numberOfLikes - 1);

                    setTimeout(function(){
                        $("#notification-data").attr("class", "notification-container");
                        $("#notification-data").empty();
                    }, 3000);
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

    /**
     *  new post
     */
    $("#new-post-button").click(function(e) {
        e.preventDefault();

        var data = $("#new-post-form").serialize();        
        new_post_create(data, false, "");
    });

    /**
     *  load more on newsfeed
     */
    $("#load-more-button").click(function(e) {
        e.preventDefault();


        var request = $('#load-more-request').val();
        var start = $('#start').val();
        var limit = $('#limit').val();

        post_load_more(request, start, limit);
    });

    /**
     *  new comment
     */
    $("#new-comment-button").click(function(e) {
        e.preventDefault();

        var data = $("#new-comment-form").serialize();        
        new_comment_create(data, false, "");
    });

    /**
     *  add like
     */
    $("#give-like-button").click(function(e) {
        e.preventDefault();

        var postID = $(this).attr("data-value");
        var request = $(this).attr("data-request-type");
        add_like(postID, request);
    });   

    /**
     *  remove like
     */
    $("#remove-like-button").click(function(e) {
        e.preventDefault();

        var postID = $(this).attr("data-value");
        var request = $(this).attr("data-request-type");
        remove_like(postID, request);
    });     
});