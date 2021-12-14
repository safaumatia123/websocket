<?php
session_start();

if(!isset($_SESSION['user_data']))
{
  header('location:login.php');
}

require('database/User.php');

$user_object = new User;

$user_data = $user_object->get_user_all_data();


if(isset($_POST["subject"]))
{
    //session_start();

    if(isset($_SESSION['subject']))
    {
        header('location:index.php');
    }

    require_once('database/Rooms.php');

    //$noti_object = new Rooms;

    //$noti_object->setCommentId($_POST['comment_id']);

    //$noti_object->setCommentSubject($_POST['comment_subject']);

    //$noti_object->setCommentText($_POST['comment_text']);

    //$noti_object->setCommentStatus($_POST['comment_status']);
}    

?>

<!DOCTYPE html>
<html>
 <head>
  <title>Notification using PHP</title>
  <link href="vendor-front/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
 </head>
 <body>
  <br /><br />
  <div class="container">
   <nav class="navbar navbar-inverse" style="background-color: #4c66a4;">
    <div class="container-fluid">
     <div class="navbar-header">
      <a class="navbar-brand" href="#" style="color: white;">Facebook Notification System</a>
     </div>
     <ul class="nav navbar-nav navbar-right">
      <li class="dropdown">
       <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="label label-pill label-danger count" style="border-radius:10px;"></span> <span class="glyphicon glyphicon-bell" style="font-size:18px; color: white;"></span></a>
       <ul class="dropdown-menu"></ul>
      </li>
     </ul>
    </div>
   </nav>
   <br />
   <div class="col-lg-8">
   <!-- <form method="post" id="comment_form">
    <div class="form-group">
     <label>Enter Subject</label>
     <input type="text" name="comment_subject" id="comment_subject" class="form-control">
    </div>
    <div class="form-group">
      <label>Enter Comment</label>
     <textarea name="comment_text" id="comment_text" class="form-control" rows="5"></textarea>
    </div>
    <div class="form-group">
     <input type="submit" name="post" id="post" style="background-color: #4c66a4; color: white;" class="btn btn-info" value="Post" />
    </div>
   </form> -->
   <div id="comment_area"></div>
 </div>

    <div class="col-lg-4">
        <?php

        $login_user_id = '';

        $token = '';

        foreach($_SESSION['user_data'] as $key => $value)
        {
          $login_user_id = $value['id'];

          $token = $value['token'];

        ?>
        <input type="hidden" name="login_user_id" id="login_user_id" value="<?php echo $login_user_id; ?>" />

        <input type="hidden" name="is_active_chat" id="is_active_chat" value="No" />
        
        <div class="mt-3 mb-3 text-center">
          <img src="<?php echo $value['profile']; ?>" width="100" class="img-fluid rounded-circle img-thumbnail" />
          <h3 class="mt-2"><?php echo $value['name']; ?></h3>
          <a href="profile.php" style="background-color: #4c66a4; color: white;" class="btn btn-info">Edit</a>
          <input type="button" class="btn btn-primary mt-2 mb-2" name="logout" id="logout" value="Logout" />
        </div>
        <?php
        }
        ?>


        <?php

        $user_object = new User;

        $user_object->setUserId($login_user_id);

        $user_data = $user_object->get_user_all_data_with_status_count();

        ?>
        <div class="list-group" style=" max-height: 100vh; margin-bottom: 10px; overflow-y:scroll; -webkit-overflow-scrolling: touch;">
          <br>
          <?php
          
          foreach($user_data as $key => $user)
          {
            $icon = '<i class="fa fa-circle text-danger"></i>';

            if($user['user_login_status'] == 'Login')
            {
              $icon = '<i class="fa fa-circle text-success"></i>';
            }

            if($user['user_id'] != $login_user_id)
            {
              if($user['count_status'] > 0)
              {
                $total_unread_message = '<span class="badge badge-danger badge-pill">' . $user['count_status'] . '</span>';
              }
              else
              {
                $total_unread_message = '';
              }

              echo "
              <a class='list-group-item list-group-item-action select_user' style='cursor:pointer' data-userid = '".$user['user_id']."'>
                <img src='".$user["user_profile"]."' class='img-fluid rounded-circle img-thumbnail' width='50' />
                <span class='ml-1'>
                  <strong>
                    <span id='list_user_name_".$user["user_id"]."'>".$user['user_name']."</span>
                    <span id='userid_".$user['user_id']."'>".$total_unread_message."</span>
                  </strong>
                </span>
                <span class='mt-2 float-right' id='userstatus_".$user['user_id']."'>".$icon."</span>
              </a>
              ";
            }
          }


          ?>
        </div>
      </div>

      </div>  

  </div>
 </body>
</html>

<script type="text/javascript">
$(document).ready(function(){

  var receiver_userid = '';

  var conn = new WebSocket('ws://localhost:8080?token=<?php echo $token; ?>');

  //var conn = new WebSocket('ws://localhost:8080');
  conn.onopen = function(e){
    console.log("Connection established");
  };

  conn.onmessage = function(e){
    console.log(e.data);
    var data = JSON.parse(e.data);

    load_unseen_notification(); 

   /*var user_id = $('#login_user_id').val();
   $.ajax({
   url:"fetch.php",
   method:"POST",
   data:{view:'', user_id:user_id, to_user_id:receiver_userid},
   dataType:"json",
   success:function(data)
   {
    $('.dropdown-menu').html(data.notification);
    if(data.unseen_notification > 0)
    {
     $('.count').html(data.unseen_notification);
    }
   }
  });*/

    var count_chat = $('#userid'+data.userid).text();

          if(count_chat == '')
          {
            count_chat = 0;
          }

          count_chat++;

          $('#userid_'+data.userid).html('<span class="badge badge-danger badge-pill">'+count_chat+'</span>');  
  };

  conn.onclose = function(e){
    console.log("Connection Close");
  };


  //$('#comment_form').on('submit', function(event)
  $(document).on('submit', '#comment_form', function(event){

      event.preventDefault();

      if($('#comment_form'))
      {
        var subject = $('#comment_subject').val();
        var comment = $('#comment_text').val();
        var user_id = parseInt($('#login_user_id').val());
        //var user_id = $('#login_user_id').val();

        var data = {
          to_user_id:receiver_userid,
          userid : user_id,
          comment_subject : subject,
          comment_text : comment,
          comment_status : 0,
          command: 'private'
        };

        conn.send(JSON.stringify(data));
      };
        $('#comment_form')[0].reset();
    });

  function make_chat_area(user_name)
    {
      var html = `
      <div class="card">
        <div class="card-header">
          <div class="row">
            <div class="col col-sm-6">
              <b>Send Notification To <span class="text-danger" id="chat_user_name">`+user_name+`</span></b>
            </div>
            <div class="col col-sm-6 text-right">
              <button type="button" class="close" id="close_comment_area" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
          </div>
        </div>
      </div>
      <br>
      <form method="post" id="comment_form">
       <div class="form-group">
        <label>Enter Subject</label>
         <input type="text" name="comment_subject" id="comment_subject" class="form-control">
       </div>
       <div class="form-group">
        <label>Enter Comment</label>
          <textarea name="comment_text" id="comment_text" class="form-control" rows="5"></textarea>
       </div>
       <div class="form-group">
        <input type="submit" name="post" id="post" style="background-color: #4c66a4; color: white;" class="btn btn-info" value="Post" />
       </div>
      </form>
      `;

      $('#comment_area').html(html);
    }

  $(document).on('click', '.select_user', function(){

      receiver_userid = $(this).data('userid');

      var from_user_id = $('#login_user_id').val();

      var receiver_user_name = $('#list_user_name_'+receiver_userid).text();

      $('.select_user.active').removeClass('active');

      $(this).addClass('active');

      make_chat_area(receiver_user_name);

      $('#is_active_chat').val('Yes');
  })

  $(document).on('click', '#close_comment_area', function(){

      $('#comment_area').html('');

      $('.select_user.active').removeClass('active');

      $('#is_active_chat').val('No');

      receiver_userid = '';

    });
  
 function load_unseen_notification(view = '')
 {
  var user_id = $('#login_user_id').val();
  $.ajax({
   url:"fetch.php",
   method:"POST",
   data:{view:view, user_id:user_id, to_user_id:receiver_userid},
   dataType:"json",
   success:function(data)
   {
    $('.dropdown-menu').html(data.notification);
    if(data.unseen_notification > 0)
    {
     $('.count').html(data.unseen_notification);
    }
   }
  });
 }
 
 load_unseen_notification();
 
 
 $(document).on('click', '.dropdown-toggle', function(){
  $('.count').html('');
  load_unseen_notification('yes');
 });
 
 /*setInterval(function(){ 
  load_unseen_notification();; 
 }, 5000);*/

$('#logout').click(function(){

      user_id = $('#login_user_id').val();

      $.ajax({
        url:"action.php",
        method:"POST",
        data:{user_id:user_id, action:'leave'},
        success:function(data)
        {
          var response = JSON.parse(data);
    
          if(response.status == 1)
          {
            conn.close();
            location = 'login.php';
          }
        }
      })

    });
 
});

</script>
