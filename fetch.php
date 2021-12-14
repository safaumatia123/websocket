<?php
include('connect.php');
require 'database/Rooms.php';



if(isset($_POST['view'])){

// $con = mysqli_connect("localhost", "root", "", "notif");

if($_POST["view"] != '')
{
    $update_query = "UPDATE comments SET comment_status = 1 WHERE comment_status=0";
    mysqli_query($con, $update_query);
}

//$query_new = "SELECT * FROM comments c WHERE c.userid IN (SELECT f.user_id FROM friends f where f.user_id = :user_id") OR c.userid IN (SELECT f.friend_id FROM friends f WHERE f.user_id = :user_id) OR c.userid = :user_id ORDER BY comment_id DESC LIMIT 5";
//$result_new = mysqli_query($con, $query_new);

$userid = $_POST["user_id"];
$to_user_id = $_POST["to_user_id"];

/*$query = "SELECT a.user_name as from_user_name, b.user_name as to_user_name, comment_subject, comment_text, timestamp, comment_status, to_user_id, userid  
      FROM comments 
    INNER JOIN user_table a 
      ON comments.userid = a.user_id 
    INNER JOIN user_table b 
      ON comments.to_user_id = b.user_id 
    WHERE (comments.userid = $userid AND comments.to_user_id = $to_user_id) 
    OR (comments.userid = $to_user_id AND comments.to_user_id = $userid)  ORDER BY comment_id DESC LIMIT 5";
*/
$query = "SELECT * FROM comments WHERE userid = $userid OR to_user_id = $userid ORDER BY comment_id DESC LIMIT 5";
$result = mysqli_query($con, $query);
$output = '';
if(mysqli_num_rows($result) > 0)
{
 while($row = mysqli_fetch_array($result))
 {
   $output .= '
   <li>
   <a href="#">
   <strong>'.$row["comment_subject"].'</strong><br />
   <small><em>'.$row["comment_text"].'</em></small>
   </a>
   </li>
   ';

 }
}
else{
     $output .= '
     <li><a href="#" class="text-bold text-italic">No Notification Found</a></li>';
}



$status_query = "SELECT * FROM comments WHERE (userid = $userid OR to_user_id = $userid) AND comment_status=0";
$result_query = mysqli_query($con, $status_query);
$count = mysqli_num_rows($result_query);
$data = array(
    'notification' => $output,
    'unseen_notification'  => $count
);

echo json_encode($data);

}

?>