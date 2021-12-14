<?php 
	
class Rooms
{
	private $comment_id;
	private $to_user_id;
	private $userid;
	private $comment_subject;
	private $comment_text;
	private $timestamp;
	private $comment_status;
	protected $connect;

	public function setCommentId($comment_id)
	{
		$this->comment_id = $comment_id;
	}

	function getCommentId()
	{
		return $this->comment_id;
	}

	function setToUserId($to_user_id)
	{
		$this->to_user_id = $to_user_id;
	}

	function getToUserId()
	{
		return $this->to_user_id;
	}

	public function setUserId($userid)
	{
		$this->userid = $userid;
	}

	function getUserId()
	{
		return $this->userid;
	}
	
    public function setCommentSubject($comment_subject)
	{
		$this->comment_subject = $comment_subject;
	}

	function getCommentSubject()
	{
		return $this->comment_subject;
	}
	
	public function setCommentText($comment_text)
	{
		$this->comment_text = $comment_text;
	}

	function getCommentText()
	{
		return $this->comment_text;
	}
	
	function setTimestamp($timestamp)
	{
		$this->timestamp = $timestamp;
	}

	function getTimestamp()
	{
		return $this->timestamp;
	}

	public function setCommentStatus($comment_status)
	{
		$this->comment_status = $comment_status;
	}

	function getCommentStatus()
	{
		return $this->comment_status;
	}
	
	public function __construct()
	{
		require_once("Database_connection.php");

		$database_object = new Database_connection;

		$this->connect = $database_object->connect();
	}
	
	function save_chat()
	{
		$query = "
		INSERT INTO comments
			(comment_id, to_user_id, userid, comment_subject, comment_text, timestamp, comment_status) 
			VALUES (:comment_id, :to_user_id, :userid, :comment_subject, :comment_text, :timestamp, :comment_status)
		";

		$statement = $this->connect->prepare($query);

		$statement->bindParam(':comment_id', $this->comment_id);

		$statement->bindParam(':to_user_id', $this->userid);

		$statement->bindParam(':userid', $this->userid);

		$statement->bindParam(':comment_subject', $this->comment_subject);

		$statement->bindParam(':comment_text', $this->comment_text);
		
        $statement->bindParam(':timestamp', $this->timestamp);

		$statement->bindParam(':comment_status', $this->comment_status);

		$statement->execute();

		return $this->connect->lastInsertId();
	}


	function get_all_chat_data()
	{
		$query = "SELECT * FROM comments ORDER BY comment_id DESC LIMIT 5";

		$statement = $this->connect->prepare($query);

		$statement->execute();

		return $statement->fetchAll(PDO::FETCH_ASSOC);

	}

	function get_count()
	{	
		$status_query = "SELECT * FROM comments WHERE comment_status=0";

        $statement = $this->connect->prepare($status_query);

		$statement->execute();

		return $statement->rowCount();
    }

    function update_chat_status()
	{
		$query = "
		UPDATE comments
			SET comment_status = :comment_status 
			WHERE comment_id = :comment_id
		";

		$statement = $this->connect->prepare($query);

		$statement->bindParam(':comment_status', $this->comment_status);

		$statement->bindParam(':comment_id', $this->comment_id);

		$statement->execute();
	}
}	

?>	
	