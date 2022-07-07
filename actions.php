<?php
	include("functions.php");
	if($_GET['action']== 'login')
	{
		// print_r($_POST);
		$error = "";
		if(!$_POST["username"])
		{

			$error = "A username is required";
		}
		else if(!$_POST["password"])
		{
			$error = "A password is required";
		}
		
		if($error != "")
		{
			echo $error;
			exit();
		}
		$query = "SELECT * FROM users WHERE username='". mysqli_real_escape_string($link, $_POST['username'])."' LIMIT 1";
		$result = mysqli_query($link,$query);

		if(mysqli_num_rows($result) == 0)
		{
			$error = "Account not created";
			echo $error;
			exit();
		}

		while ($row=mysqli_fetch_row($result))
    	{
    		$var1 = $row[4];
    		$var2 = $_POST['password'];
    		if($var1 == $var2)
    		{
    			$_SESSION['username'] = $row[2];
    			$_SESSION['id'] = $row[0];
    			$sub_query = "
	        	INSERT INTO login_details 
	        	(user_id) 
	        	VALUES ('".$row[0]."')
	        	";
	        	mysqli_query($link,$sub_query);
    			$_SESSION['login_details_id'] =mysqli_insert_id($link);
    			echo 1;
    		}
    		else
    		{
    			$error = "password not correct";
    		}
    	}
    	if($error != "")
		{
			echo $error;
			exit();
		}	

	}

	if($_GET['action']== 'signUp')
	{
		// print_r($_POST);
		$error = "";
		if(!$_POST["email"])
		{

			$error = "An email is required";
		}
		else if(!$_POST["password"])
		{
			$error = "A password is required";
		}
		else if (filter_var($_POST["email"], FILTER_VALIDATE_EMAIL) == false) 
		{
  			$error="Enter a valid email address";
		} 
		
		if($error != "")
		{
			echo $error;
			exit();
		}
		$query = "SELECT * FROM users WHERE username='". mysqli_real_escape_string($link, $_POST['username'])."' LIMIT 1";
		$result = mysqli_query($link,$query);
		if(mysqli_num_rows($result) > 0)
		{
			 echo "Username already taken!";
			 exit();
		}
		else
		{
			$query = "INSERT INTO users(name,username,email,password,dob,gender) VALUES('".mysqli_real_escape_string($link, $_POST['name'])."' , '".mysqli_real_escape_string($link, $_POST['username'])."', '".mysqli_real_escape_string($link, $_POST['email'])."', '".mysqli_real_escape_string($link, $_POST['password'])."', '".mysqli_real_escape_string($link, $_POST['dob'])."', '".mysqli_real_escape_string($link, $_POST['gender'])."')";
			if(mysqli_query($link,$query))
			{
				$_SESSION['username'] = $_POST['username'];
				$_SESSION['id'] = mysqli_insert_id($link);
				$sub_query = "
	        	INSERT INTO login_details 
	        	(user_id) 
	        	VALUES ('".mysqli_insert_id($link)."')
	        	";
	        	mysqli_query($link,$sub_query);
				$_SESSION['login_details_id'] =mysqli_insert_id($link);
				//if inserted correctly
				// $query = "UPDATE users SET password='".md5(md5(mysqli_insert_id($link)).$_POST['password'])." ' WHERE id = ".mysqli_insert_id($link)."  LIMIT 1";

				
				echo "1";
			}
			else
			{
				echo "connection Error maybe";
			}

		}

	}


	if($_GET['action']== 'add_comment')
	{
		 // print_r($_POST);
		// $error = "";
		// if(!$_POST["email"])
		// {

		// 	$error = "An email is required";
		// }
		// else if(!$_POST["password"])
		// {
		// 	$error = "A password is required";
		// }
		// else if (filter_var($_POST["email"], FILTER_VALIDATE_EMAIL) == false) 
		// {
  // 			$error="Enter a valid email address";
		// } 
		
		// if($error != "")
		// {
		// 	echo $error;
		// 	exit();
		// }

			$query = "INSERT INTO commentTable(postId,whoCommentedId,whoCommentedUsername,commentContent) VALUES('".mysqli_real_escape_string($link, $_POST['postID'])."' , '".mysqli_real_escape_string($link, $_SESSION['id'])."',  '".mysqli_real_escape_string($link, $_SESSION['username'])."','".mysqli_real_escape_string($link, $_POST['textContent'])."')";
			if(mysqli_query($link,$query))
			{
				
				echo "1";
			}
			else
			{
				echo "connection Error maybe";
			}

	}




	if($_GET["action"]== "toggleLike")
	{
		// just checking
		// print_r($_POST);

		$query = "SELECT * FROM likeTable WHERE whoLiked= ". mysqli_real_escape_string($link, $_SESSION['id'])." AND postId=".mysqli_real_escape_string($link,$_POST['postId'])." LIMIT 1";
		
			// just checking
			// echo $query;

			$result = mysqli_query($link,$query);
			if(mysqli_num_rows($result) > 0)
			{
				//if they are already liking,delete them from the 'likeTable' table
				$row=mysqli_fetch_assoc($result);
				mysqli_query($link,"DELETE FROM likeTable WHERE id = ".mysqli_real_escape_string($link,$row['id'])." LIMIT 1");

				mysqli_query($link,"UPDATE posts SET numberOfLikes=numberOfLikes-1 WHERE id = ".mysqli_real_escape_string($link,$_POST['postId'])."  LIMIT 1");
				echo "1";

			}
			else
			{
				// if they are not liking,add them to the 'likeTable' table

				mysqli_query($link,"INSERT INTO likeTable(whoLiked,postId) VALUES('".mysqli_real_escape_string($link,$_SESSION['id'])."','".mysqli_real_escape_string($link,$_POST['postId'])."')");

				mysqli_query($link,"UPDATE posts SET numberOfLikes=numberOfLikes+1 WHERE id = ".mysqli_real_escape_string($link,$_POST['postId'])."  LIMIT 1");
				echo "2";

			}
	}

	if($_GET["action"]== "toggleShare")
	{
		// just checking
		// print_r($_POST);

		$query = "SELECT * FROM shareTable WHERE whoShared= ". mysqli_real_escape_string($link, $_SESSION['id'])." AND postId=".mysqli_real_escape_string($link,$_POST['postId'])." LIMIT 1";
		
			// just checking
			// echo $query;

			$result = mysqli_query($link,$query);
			if(mysqli_num_rows($result) > 0)
			{
				//if they are already liking,delete them from the 'likeTable' table
				$row=mysqli_fetch_assoc($result);
				mysqli_query($link,"DELETE FROM shareTable WHERE id = ".mysqli_real_escape_string($link,$row['id'])." LIMIT 1");

				mysqli_query($link,"UPDATE posts SET numberOfShares=numberOfShares-1 WHERE id = ".mysqli_real_escape_string($link,$_POST['postId'])."  LIMIT 1");
				echo "1";

			}
			else
			{
				// if they are not liking,add them to the 'likeTable' table

				mysqli_query($link,"INSERT INTO shareTable(whoshared,postId) VALUES('".mysqli_real_escape_string($link,$_SESSION['id'])."','".mysqli_real_escape_string($link,$_POST['postId'])."')");

				mysqli_query($link,"UPDATE posts SET numberOfShares=numberOfShares+1 WHERE id = ".mysqli_real_escape_string($link,$_POST['postId'])."  LIMIT 1");
				echo "2";

			}
	}


	if($_GET['action']== 'save')
	{
		// print_r($_POST);
		$error = "";
		if(!$_POST["name"])
		{

			$error = "Name is required";
		}
		else if(!$_POST["email"])
		{

			$error = "An email is required";
		}
		else if(!$_POST["dob"])
		{
			$error = "Date of Birth is required";
		}
		else if (filter_var($_POST["email"], FILTER_VALIDATE_EMAIL) == false) 
		{
  			$error="Enter a valid email address";
		} 
		
		if($error != "")
		{
			echo $error;
			exit();
		}
		


		$query = "UPDATE users SET name='".mysqli_real_escape_string($link, $_POST['name'])."', email='".mysqli_real_escape_string($link, $_POST['email'])."', dob='".mysqli_real_escape_string($link, $_POST['dob'])."', country='".mysqli_real_escape_string($link, $_POST['country'])."', occupation='".mysqli_real_escape_string($link, $_POST['occupation'])."', instituteName='".mysqli_real_escape_string($link, $_POST['instituteName'])."', summary='".mysqli_real_escape_string($link, $_POST['summary'])."', skills='".mysqli_real_escape_string($link, $_POST['skills'])."' WHERE id = ".mysqli_real_escape_string($link,$_SESSION['id'])."  LIMIT 1";

		
		if(mysqli_query($link,$query))
		{

			echo "1";
		}
		else
		{
			echo "connection Error maybe";
		}
	}


	if($_GET['action']== 'toggleConnect')
	{


		$isConnectedQuery = "SELECT * FROM connectionTable WHERE connection1= ". mysqli_real_escape_string($link, $_SESSION['id'])." AND connection2=".mysqli_real_escape_string($link,$_POST['profileId'])."  LIMIT 1";

		$isConnectedQueryResult = mysqli_query($link,$isConnectedQuery);
		if(mysqli_num_rows($isConnectedQueryResult)>0)
		{
			// Connected
			mysqli_query($link,"DELETE FROM connectionTable WHERE connection1 = ".mysqli_real_escape_string($link,$_SESSION['id'])."  AND connection2 = ".mysqli_real_escape_string($link,$_POST['profileId'])." LIMIT 1");
			echo "1";
		}
		else
		{
				mysqli_query($link,"INSERT INTO connectionTable(connection1,connection2) VALUES('".mysqli_real_escape_string($link,$_SESSION['id'])."','".mysqli_real_escape_string($link,$_POST['profileId'])."')");
				echo '2';
		}	

	}
	if($_GET['action']== 'update_last_activity')
	{
		$query = "
			UPDATE login_details 
			SET last_activity = now() 
			WHERE login_details_id = '".$_SESSION["login_details_id"]."'
			";
		mysqli_query($link,$query);
	}

	if($_GET['action']== 'fetch_user')
	{
		$whereClause="";
		 $query1 = "SELECT * FROM connectionTable WHERE connection1= ". mysqli_real_escape_string($link, $_SESSION['id']);
		
		$result = mysqli_query($link,$query1);
		while ($row =mysqli_fetch_assoc($result)) {
			if($whereClause=="")
			{
				$whereClause.= "WHERE";
			}
			else
			{
				$whereClause.= " OR";
			}
			$whereClause.=" id = ".$row['connection2'];
		}
		if($whereClause=="")
		{
			$whereClause=" WHERE id = 0 ";
		}
		$query = "SELECT * FROM users ".$whereClause."";
		   // echo $query;
		 $result = mysqli_query($link,$query);

		 

		 if(mysqli_num_rows($result)==0)
		 {
		 	echo "there are no connections to display";
		 }
		 else
		 {
		 	$output = '
			<table class="table table-bordered table-striped">
			 <tr>
			  <td>Username</td>
			  <td>Status</td>
			  <td>Action</td>
			 </tr>
			';	
		 	while($row = mysqli_fetch_assoc($result))
		 	{
		 		$status = '';
				$current_timestamp = strtotime(date("Y-m-d H:i:s") . '+ 155 second');
			 	$current_timestamp = date('Y-m-d H:i:s', $current_timestamp);

			 	$user_last_activity = fetch_user_last_activity($row['id']);
			 	 if($user_last_activity > $current_timestamp)
				 {
				  $status = '<span class="badge badge-success">Online</span>';
				 	// $status = '<span class="badge badge-success">'.$current_timestamp.'</span>';
				 }
				 else
				 {
				  $status = '<span class="badge  badge-danger">Offline</span>';
				 	// $status = '<span class="badge badge-danger">'.$current_timestamp.'</span>';
				 }

		 		 $output .= '
				 <tr>
				  <td><a style="color:#325896;text-decoration : none;" href="?page=timeline&profileid='.$row["id"].'">'.$row["username"].'</a> '.count_unseen_message($row['id'], $_SESSION['id']).'</td>
				  <td>'.$status.'</td>
				  <td><button type="button" class="btn btn-info btn-xs start_chat" data-touserid="'.$row['id'].'" data-tousername="'.$row['username'].'">Start Chat</button></td>
				 </tr>
				 ';

		 	}
		 	$output .= '</table>';

			echo $output;

		 }
	}

	if($_GET['action']== 'fetch_comment')
	{

		$query = "SELECT * FROM commentTable WHERE postId= ". mysqli_real_escape_string($link, $_POST['postID'])." ORDER BY dateTimeCol";

		$result = mysqli_query($link,$query);

		while($row = mysqli_fetch_assoc($result))
		{
			$output .= '
		 <div class="oneComment">
		  <div class="oneComment-heading"><a style="color:#325896;text-decoration : none;" href="?page=timeline&profileid='.$row['whoCommentedId'].'">'.$row['whoCommentedUsername'].'</a> : </a> <span class = "time" style="color:#8079a0">'.time_since(time()-strtotime($row['dateTimeCol'])).';</span></div>
		  <div class="oneComment-body">'.$row["commentContent"].'</div>
		 </div>
 		';
		}

		// print_r($_POST);
		
 		
		echo $output;
	}


	if($_GET['action']== 'fetch_user_chat_history')
	{
		echo fetch_user_chat_history($_SESSION['id'], $_POST['to_user_id']);
	}
	if($_GET['action']== 'insert_chat')
	{
		// print_r($_POST);


		$query = "INSERT INTO chat_message(to_user_id,from_user_id,chat_message,status) VALUES('".mysqli_real_escape_string($link, $_POST['to_user_id'])."' , '".mysqli_real_escape_string($link, $_SESSION['id'])."', '".mysqli_real_escape_string($link, $_POST['chat_message'])."', '1')";
		if(mysqli_query($link,$query))
		{
			echo fetch_user_chat_history($_SESSION['id'], $_POST['to_user_id']);
		}


	}
?>