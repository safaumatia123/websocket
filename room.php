<?php
session_start();

require('database/Rooms.php');

$noti_object = new Rooms;

$noti_data = $noti_object->get_all_chat_data(); 

$noti_count = $noti_object->get_count();

?>

<!DOCTYPE html>
<html>
 <head>
  <title>Notification using PHP Websockets Bootstrap</title>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
 </head>
 <body>
  <br /><br />
  <div class="container">
   <nav class="navbar navbar-inverse">
    <div class="container-fluid">
     <div class="navbar-header">
      <a class="navbar-brand" href="#">PHP Notification System</a>
     </div>
     <ul class="nav navbar-nav navbar-right">
      <li class="dropdown">
       <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="label label-pill label-danger count" style="border-radius:10px;"><?php echo "$noti_count"; ?></span> <span class="glyphicon glyphicon-bell" style="font-size:18px;"></span></a>
       <ul class="dropdown-menu">
         <?php
         foreach($noti_data as $noti) {
           echo '
           <li>
           <a href="#">
           <strong>'.$noti["comment_subject"].'</strong>
           <br>
           <small><em>'.$noti["comment_text"].'</em></small>
           </a>
           </li>
           ';
         }
         ?>
       </ul>
      </li>
     </ul>
    </div>
   </nav>
   <br />
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
     <input type="submit" name="post" id="post" class="btn btn -info" value="Post" />
    </div>
   </form>
   
  </div>
 </body>
</html>

<script>
$(document).ready(function(){

  var conn = new WebSocket('ws://localhost:8080');
  conn.onopen = function(e){
    console.log("Connection established");
  };

  conn.onmessage = function(e){
    console.log(e.data);
    var data = JSON.parse(e.data);   
  };


  $('#comment_form').on('submit', function(event){

      event.preventDefault();

      if($('#comment_form'))
      {
        var subject = $('#comment_subject').val();
        var comment = $('#comment_text').val();

        var data = {
          comment_subject : subject,
          comment_text : comment,
          comment_status : 0
        };

        conn.send(JSON.stringify(data));
        
      };

    });
});

$(document).on('click', '.dropdown-toggle', function(){
  $('.count').html('');
  $('.dropdown-menu').html(data.notification);
    if(data.unseen_notification > 0)
    {
     $('.count').html(data.unseen_notification);
    }
 });


</script>