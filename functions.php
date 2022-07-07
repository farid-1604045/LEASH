<?php
	session_start();

	$link = mysqli_connect("sql311.epizy.com","epiz_23975580","CIjyp0yyygGKb4H","epiz_23975580_Leash");

	if(mysqli_connect_errno())
	{
		print_r(mysqli_connect_error());
		exit();
	}
	if($_GET['function'] == "logout")
	{

		session_unset();
		// session_destroy();
	}

	function time_since($since) 
	{
    	$chunks = array(
	        array(60 * 60 * 24 * 365 , 'year'),
	        array(60 * 60 * 24 * 30 , 'month'),
	        array(60 * 60 * 24 * 7, 'week'),
	        array(60 * 60 * 24 , 'day'),
	        array(60 * 60 , 'hour'),
	        array(60 , 'min'),
	        array(1 , 's')
    	);

	    for ($i = 0, $j = count($chunks); $i < $j; $i++) {
	        $seconds = $chunks[$i][0];
	        $name = $chunks[$i][1];
	        if (($count = floor($since / $seconds)) != 0) {
	            break;
	        }
	    }

    	$print = ($count == 1) ? '1 '.$name : "$count {$name}s";
    	if($print == '-1 years')
    	{
    		$print = 'just now';
    	}
    	else
    	{
    		$print = $print." ago";
    	}
    	return $print;
	}

	function fetch_user_last_activity($user_id)
	{
	
	 // echo $user_id;
	 global $link;
	 $query = "
	 SELECT * FROM login_details 
	 WHERE user_id = '$user_id' 
	 ORDER BY last_activity DESC 
	 LIMIT 1
	 ";
	 $result = mysqli_query($link,$query);
	 while($row =mysqli_fetch_assoc($result))
	 {
	 	return $row['last_activity'];
	 }

	 
	 // return $row['last_activity'];

	}



	function fetch_user_chat_history($from_user_id, $to_user_id)
	{

	 global $link;
	 $query = "
	 SELECT * FROM chat_message 
	 WHERE (from_user_id = '".$from_user_id."' 
	 AND to_user_id = '".$to_user_id."') 
	 OR (from_user_id = '".$to_user_id."' 
	 AND to_user_id = '".$from_user_id."') 
	 ORDER BY timestamp 
	 ";
	 $result = mysqli_query($link,$query);
	 $output = '<ul class="list-unstyled">';
	 while ($row =mysqli_fetch_assoc($result))
	 {
	  $user_name = '';
	  if($row["from_user_id"] == $from_user_id)
	  {
	  	//you
	   $output .= '
	  <li align="right" style="
		border: 1px solid black;
	    padding-right: 10px;
	    margin-top: 10px;
	    margin-left: 45px;
	    width: 82%;
	    border-radius: 15px 15px 15px;">
	   <p>'.$row["chat_message"].'
	   </p>
	  </li>
	  ';
	  }
	  else
	  {
	  	//other person
	   $output .= '
	  <li style="
		border: 1px solid #f0f0f0;
		background: #f0f0f0;
	    padding-left: 10px;
	    margin-top: 10px;
	    margin-right: 45px;
	    width: 82%;
	    border-radius: 15px 15px 15px;">
	   <p style="
	   background: #f0f0f0;
	   border: 1px solid #f0f0f0;
	    ">'.$row["chat_message"].'
	   </p>
	  </li>
	  ';
	  }
	 }
	 $output .= '</ul>';
	 $query = "
	 UPDATE chat_message 
	 SET status = '0' 
	 WHERE from_user_id = '".$to_user_id."' 
	 AND to_user_id = '".$from_user_id."' 
	 AND status = '1'
	 ";
	 mysqli_query($link,$query);
	 return $output;
	}

	function get_user_name($user_id)
	{
		global $link;
	 	$query = "SELECT * FROM users WHERE id = '$user_id'";
	 $result = mysqli_query($link,$query);
	 $row=mysqli_fetch_assoc($result);
	 return $row['username'];
	 
	}

	function count_unseen_message($from_user_id, $to_user_id)
	{
		global $link;
		 $query = "
		 SELECT * FROM chat_message 
		 WHERE from_user_id = '$from_user_id' 
		 AND to_user_id = '$to_user_id' 
		 AND status = '1'
		 ";

		 $result = mysqli_query($link,$query);
		 $count = mysqli_num_rows($result);

		 $output = '';
		 if($count > 0)
		 {
		  $output = '<span class="badge badge-success">'.$count.'</span>';
		 }
		 return $output;
	}

	function displayPosts($type)
	{
		global $link;
		 if($type == 'public')
		 {
		 	$whereClause = "";
		 }
		 else if($type == 'friendsPost')
		 {
		 	$whereClause="";
		 	
		 	$query = "SELECT * FROM connectionTable WHERE connection1= ". mysqli_real_escape_string($link, $_SESSION['id']);
			$result = mysqli_query($link,$query);

			while ($row =mysqli_fetch_assoc($result)) {
				if($whereClause=="")
				{
					$whereClause.= "WHERE";
				}
				else
				{
					$whereClause.= " OR";
				}
				$whereClause.=" userid = ".$row['connection2'];
			}
			if($whereClause=="")
			{
				$whereClause=" WHERE userid =".$_SESSION['id'];
			}
			else
			{
				$whereClause.=" OR userid =".$_SESSION['id'];	
			}
		 }
		 else if($type == 'SharedPosts')
		 {
		 	$whereClause="";
		 	if($_GET['profileid'])
		 	{
		 		$query = "SELECT * FROM shareTable WHERE whoShared= ". mysqli_real_escape_string($link, $_GET['profileid']);
		 	}
		 	else
		 	{
		 		$query = "SELECT * FROM shareTable WHERE whoShared= ". mysqli_real_escape_string($link, $_SESSION['id']);
		 	}
		 	
		
			$result = mysqli_query($link,$query);
			while ($row =mysqli_fetch_assoc($result)) {
				if($whereClause=="")
				{
					$whereClause.= "WHERE";
				}
				else
				{
					$whereClause.= " OR";
				}
				$whereClause.=" id = ".$row['postId'];
			}
			if($whereClause=="")
			{
				$whereClause=" WHERE id = 0 ";
			}	
		 }
		 else if($type=='myPosts')
		 {
		 	if($_GET['profileid'])
		 	{
		 		$whereClause = " WHERE userid=".mysqli_real_escape_string($link,$_GET['profileid']);
		 	}
		 	else
		 	{
		 		$whereClause = " WHERE userid=".mysqli_real_escape_string($link,$_SESSION['id']);
		 	}
		 	
		 }
		 // else if($type=='search')
		 // {
		 // 	echo "<p> Showing results for '".mysqli_real_escape_string($link,$_GET['q'])."' <p>";
		 // 	$whereClause = "WHERE tweet LIKE '%".mysqli_real_escape_string($link,$_GET['q'])."%'";
		 // }
		 // else if(is_numeric($type))
		 // {

		 // 	$userQuery = "SELECT * FROM users WHERE id=".mysqli_real_escape_string($link,$type)." LIMIT 1";
		 // $userResult = mysqli_query($link,$userQuery);

		 // $user = mysqli_fetch_assoc($userResult);

		 // echo "<h2>".mysqli_real_escape_string($link,$user['email'])."'s Tweets</h2>";



		 // 	$whereClause = "Where userid=".mysqli_real_escape_string($link,$type);
		 // }
		 $query = "SELECT * FROM posts ".$whereClause." ORDER BY dateTimeColName DESC LIMIT 30";
		  // echo $query;
		 $result = mysqli_query($link,$query);

		 

		 if(mysqli_num_rows($result)==0)
		 {
		 	echo "there are no posts to display";
		 }
		 else
		 {
		 	while($row = mysqli_fetch_assoc($result))
		 	{
		 		$userQuery = "SELECT * FROM users WHERE id = ".mysqli_real_escape_string($link,$row['userid'])." LIMIT 1";
		 		$userQueryResult = mysqli_query($link,$userQuery);
		 		$user = mysqli_fetch_assoc($userQueryResult); 
		 		

		 		echo "<div class = 'post'> <p><a style='color:#325896;text-decoration : none;' href='?page=timeline&profileid=".$user['id']."'>".$user['username']."</a> <span class = 'time'>".time_since(time()-strtotime($row['dateTimeColName'])).";</span></p>";
		 		echo "<p>".$row['caption']."</p>";
		 		if($row['image'])
		 		{
		 			// echo '<img src="data:image/png;base64,'.base64_encode($row['image']).'" height="400" width="628"/>';
		 			echo '<img src="data:image/png;base64,'.base64_encode($row['image']).'"/>';

		 		}
		 		
		 	// 	echo "<p><a class = 'toggleFollow' data-userId='".$row['userid']."'>";
		 	// 	$isFollowingquery = "SELECT * FROM isFollowing WHERE follower= ". mysqli_real_escape_string($link, $_SESSION['id'])." AND isFollowing=".mysqli_real_escape_string($link,$row['userid'])." LIMIT 1";

				// $isFollowingQueryResult = mysqli_query($link,$isFollowingquery);  
				// if(mysqli_num_rows($isFollowingQueryResult)>0)
				// {
				// 	echo "Unfollow";
				// }
				// else
				// {
				// 	echo "Follow";
				// }


		 	// 	echo "</a></p></div>";


		 		
		 		echo "<div class='likesLine'> <i class='fas fa-thumbs-up'></i> <span id='howManyLikes' data-postId='".$row['id']."' data-likes='".$row['numberOfLikes']."'> " ;


		 		echo $row['numberOfLikes'];


		 		echo "</span> people liked this<span id='shareSpan'><span id='howManyShares' data-shareId='".$row['id']."' data-likes='".$row['numberOfLikes']."'>";
		 		echo $row['numberOfShares']; 
		 		echo "</span> Shares</span></div>";


		 		
		 		echo '<hr class="style-one">

		 			<div class="container">
					  <div class="row">
					    <div class="col-sm likeCommentFollowClass">';

				echo "<a class = 'toggleLike' data-likes='".$row['numberOfLikes']."' data-postId='".$row['id']."'>";
		 		$isLikingQuery = "SELECT * FROM likeTable WHERE whoLiked= ". mysqli_real_escape_string($link, $_SESSION['id'])." AND postId=".mysqli_real_escape_string($link,$row['id'])." LIMIT 1";

				$isLikingQueryResult = mysqli_query($link,$isLikingQuery);
				if(mysqli_num_rows($isLikingQueryResult)>0)
				{
					echo '<i class="fas fa-thumbs-up"></i> Liked';
				}
				else
				{
					echo '<i class="far fa-thumbs-up"></i> Like';
				}


				echo '</a></div>
					    <div class="col-sm likeCommentFollowClass">';
					    //comment er div shuru
					      echo "<a class = 'toggleComment' data-commentId='".$row['id']."'>";
							
						echo '<i class="far fa-comments"></i>Comment';
					    
				echo '</a>';
					      //nicher div comment er div shesh
				echo   '</div>
					    <div class="col-sm likeCommentFollowClass">';

					      
				echo "<a class = 'toggleShare' data-shares='".$row['numberOfShares']."' data-shareId='".$row['id']."'>";
		 		$isSharedQuery = "SELECT * FROM shareTable WHERE whoShared= ". mysqli_real_escape_string($link, $_SESSION['id'])." AND postId=".mysqli_real_escape_string($link,$row['id'])." LIMIT 1";

				$isSharedQueryResult = mysqli_query($link,$isSharedQuery);
				if(mysqli_num_rows($isSharedQueryResult)>0)
				{
					echo '<i class="fas fa-share-square"></i> Shared';
				}
				else
				{
					echo '<i class="fas fa-share"></i> Share';
				}
					    
				echo '</a></div>
					 </div>
					</div>';
				echo '<div class="container wholecomment">
						<div class="row formCommentClass">
						  <form class="form-inline commentArea" method="POST" id="comment_form" data-postID='.$row["id"].'>
						  <div class="col-10">      
						  	<div class="form-group">
						    <textarea class="commentTextarea" id="'.$row["id"].'" rows="2"></textarea>
						  	</div>
					      </div>
						  <div class="col-2">
						  	<button type="submit" data-postID='.$row["id"].' class="btn btn-primary mb-2 addCommentButton">Comment</button>
						  </div>
						  </form>
						</div>';
				echo '<div class="row display_comment" data-postID="'.$row["id"].'">';
				echo '<div id="display_comment'.$row["id"].'" style="width: 100%;"></div>';
				echo '</div>'; 

				echo '</div>';//container
					 
		 		echo "</div>";
		 	}
		 }
	}

	function displayProfile($userid)
	{
		global $link;
		$profileQuery = "SELECT * FROM users WHERE id=". mysqli_real_escape_string($link, $userid)." LIMIT 1";
		$profileResult = mysqli_query($link,$profileQuery);
		
		$user=mysqli_fetch_assoc($profileResult);
		// echo $user['name'];
		
		echo '<div class="card profileDetailsClass">
				<div class="div1" >';
		// echo '<div class="CoverPictureDiv" id="myCoverPicture" data-toggle="modal" data-target="#CoverPictureModel">';
		// echo '</div>';

				
				if($_GET['profileid']==$_SESSION['id'] || !$_GET['profileid'])
				{
					if($user['CoverPhoto'])
				 	{
				 		$background_CoverPhoto = 'data:image/png;base64,'.base64_encode($user['CoverPhoto']);
						echo '<div class="CoverPictureDiv" id="myCoverPicture" data-toggle="modal" data-target="#CoverPictureModel" style="background-image: url('.$background_CoverPhoto.');">';
				 			// echo '<img  src="data:image/png;base64,'.base64_encode($user['DP']).'"/>';
					}
					else
					{
						echo '<div class="CoverPictureDiv" id="myCoverPicture" data-toggle="modal" data-target="#CoverPictureModel" style="background-image: url(images/leashProfileCover.png);">';
					}	
					echo '</div>';
						
				}
				else
				{
					if($user['CoverPhoto'])
				 	{
						 		$background_CoverPhoto = 'data:image/png;base64,'.base64_encode($user['CoverPhoto']);
						echo '<div class="CoverPictureDiv" id="myCoverPicture"  style="background-image: url('.$background_CoverPhoto.');">';
				 			// echo '<img  src="data:image/png;base64,'.base64_encode($user['DP']).'"/>';
					}
					else
					{
						echo '<div class="CoverPictureDiv" id="myCoverPicture" style="background-image: url(images/leashProfileCover.png);">';
					}	
					echo '</div>';
				}
				

					if($_GET['profileid']==$_SESSION['id'] || !$_GET['profileid'])
					{	
						//you
						if($user['DP'])
				 		{
				 			$background_DP = 'data:image/png;base64,'.base64_encode($user['DP']);
						echo '<div class="profilePicture" id="myProfilePicture" data-toggle="modal" data-target="#DPModel" style="background-image: url('.$background_DP.');">';
				 			// echo '<img  src="data:image/png;base64,'.base64_encode($user['DP']).'"/>';
						}
						else
						{
							echo '<div class="profilePicture" id="myProfilePicture" data-toggle="modal" data-target="#DPModel" style="background-image: url(images/defaultDp.jpg);">';
						}	
						echo '</div>';
						
					}
					else
					{
						//not you
						if($user['DP'])
				 		{
				 			$background_DP = 'data:image/png;base64,'.base64_encode($user['DP']);
						echo '<div class="profilePicture" id="myProfilePicture"  style="background-image: url('.$background_DP.');">';
				 			// echo '<img  src="data:image/png;base64,'.base64_encode($user['DP']).'"/>';
						}
						else
						{
							echo '<div class="profilePicture" id="myProfilePicture" style="background-image: url(images/defaultDp.jpg);">';
						}	
						echo '</div>';
					}


					
		echo '</div>
				<div class="div2">



						<nav class="navbar navbar-expand-lg navbar-light bg-white profileNav">
						  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
						    <span class="navbar-toggler-icon"></span>
						  </button>
						  <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
						    <ul class="navbar-nav">';
						    if($userid == $_SESSION['id'])
						    {
						    	echo '<li class="nav-item active">
								        <a class="nav-link btn btn-primary mb-2" id="profileEditButton" data-toggle="modal" data-target="#exampleModalLong"><i class="fas fa-edit"></i> <span class="sr-only">(current)</span></a>
								        
								      </li>';
						    }
						    if($userid != $_SESSION['id'])
						    {
						    	echo '<li class="nav-item">';
								        // <a class="nav-link btn btn-primary mb-2" id="profileConnectButton" href="#">Connect</a>
								      // echo '<a class = "nav-link btn btn-primary mb-2" id="profileConnectButton" data-profileid="'.$_GET['profileid'].'">';
								 		$isConnectedQuery = "SELECT * FROM connectionTable WHERE connection1= ". mysqli_real_escape_string($link, $_SESSION['id'])." AND connection2=".mysqli_real_escape_string($link,$_GET['profileid'])."  LIMIT 1";

										$isConnectedQueryResult = mysqli_query($link,$isConnectedQuery);
										if(mysqli_num_rows($isConnectedQueryResult)>0)
										{
											echo '<a class = "nav-link btn btn-primary mb-2" id="profileConnectButton" style="background:#4f74b0;color:white;" data-profileid="'.$_GET['profileid'].'">';
											echo 'Connected';

										}
										else
										{
											echo '<a class = "nav-link btn btn-primary mb-2" id="profileConnectButton" data-profileid="'.$_GET['profileid'].'">';
											echo 'Connect';
										}


										echo '</a>'; 
								 echo '</li>';
						    }
						      
					
						 	echo '<li class="nav-item">
						        <a class="nav-link btn btn-primary mb-2" id="profileMessageButton" href="#">Message</a>
						        
						      </li>
						      <li class="nav-item">
						        <a class="nav-link btn btn-primary mb-2" id="profileConnectionsButton" href="#">Connections</a>
						        

						      </li>
						      <li class="nav-item">
						        <a class="nav-link btn btn-primary mb-2" id="profileAboutButton" data-toggle="modal" data-target="#exampleModalLongAbout">About</a>
						        

						      </li>
						    </ul>
						  </div>
						</nav>

						<div class="modal fade" id="DPModel" tabindex="-1" role="dialog" aria-labelledby="DPModelLabel" aria-hidden="true">
						  <div class="modal-dialog" role="document">
						    <div class="modal-content">
						      <div class="modal-header">
						        <h5 class="modal-title" id="exampleModalLabel">Change Profile Picture...</h5>
						        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
						          <span aria-hidden="true">&times;</span>
						        </button>
						      </div>
						      <div class="modal-body">
								<form method="POST" enctype="multipart/form-data" action="uploadDP.php">
								  <div class="form-group">
								    <input type="file" class="form-control-file" id="DPinput" name="myDP">
								  </div>
								
						        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						        <button type="submit" id="saveProfileButton" class="btn btn-primary" name="postDP">Save changes</button>
						        </form>
						      </div>
						    </div>
						  </div>
						</div>

						

						<div class="modal fade" id="CoverPictureModel" tabindex="-1" role="dialog" aria-labelledby="CoverPicModelLabel" aria-hidden="true">
						  <div class="modal-dialog" role="document">
						    <div class="modal-content">
						      <div class="modal-header">
						        <h5 class="modal-title" id="CoverPictureModelLabel">Change Cover Picture...</h5>
						        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
						          <span aria-hidden="true">&times;</span>
						        </button>
						      </div>
						      <div class="modal-body">
								<form method="POST" enctype="multipart/form-data" action="uploadCoverPicture.php">
								  <div class="form-group">
								    <input type="file" class="form-control-file" id="CoverPictureinput" name="myCoverPicture">
								  </div>	
								

						        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						        <button type="submit" id="saveCoverButton" class="btn btn-primary" name="postCoverPicture">Save changes</button>
						        </form>
						      </div>
						    </div>
						  </div>
						</div>

						
						<div class="modal fade" id="exampleModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
						  <div class="modal-dialog modal-lg" role="document">
						    <div class="modal-content">
						      <div class="modal-header">
						        <h5 class="modal-title" id="loginMoadalTitle">About</h5>
						        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
						          <span aria-hidden="true">&times;</span>
						        </button>
						      </div>
						     	<div class="modal-body">
							        
							      	<form>
							      		
										<div class="form-group">
											<label>Name:</label>
											<input type="text" class="form-control" id="editName" value="'.$user['name'].'" placeholder="Full Name">
										</div>

										<div class="form-group">
											<label>Email:</label>
											<input type="email" class="form-control" id="editEmail" value="'.$user['email'].'" placeholder="Email Address">
										</div>
										<div class="form-group">
											<label>Date of Birth:</label>
											<input type="date" class="form-control" id="editDOB" value="'.$user['dob'].'" placeholder="Date Of Birth">
										</div>
										<div class="form-group">
											<label>Country:</label>
											<input type="text" class="form-control" id="editCountry" value="'.$user['country'].'" placeholder="Which country are you from?">
										</div>';
									if($user['occupation']=='')
									{
										echo '<div class="custom-control custom-radio custom-control-inline">
								            <input type="radio" id="studentRadio" name="occupationRadio" class="custom-control-input occupationClass" value="Student">
								            <label class="custom-control-label" for="studentRadio">Student</label>
								          </div>
								          <div class="custom-control custom-radio custom-control-inline">
								            <input type="radio" id="jobRadio" name="occupationRadio" class="custom-control-input occupationClass" value="Works">
								            <label class="custom-control-label" for="jobRadio">Job</label>
								        </div>';
									}
									else if($user['occupation']=='Student')
									{
										echo '<div class="custom-control custom-radio custom-control-inline">
								            <input type="radio" id="studentRadio" name="occupationRadio" class="custom-control-input occupationClass" value="Student" checked>
								            <label class="custom-control-label" for="studentRadio">Student</label>
								          </div>
								          <div class="custom-control custom-radio custom-control-inline">
								            <input type="radio" id="jobRadio" name="occupationRadio" class="custom-control-input occupationClass" value="Works">
								            <label class="custom-control-label" for="jobRadio">Job</label>
								        </div>';
									}
									else
									{
										echo '<div class="custom-control custom-radio custom-control-inline">
								            <input type="radio" id="studentRadio" name="occupationRadio" class="custom-control-input occupationClass" value="Student">
								            <label class="custom-control-label" for="studentRadio">Student</label>
								          </div>
								          <div class="custom-control custom-radio custom-control-inline">
								            <input type="radio" id="jobRadio" name="occupationRadio" class="custom-control-input occupationClass" value="Works" checked>
								            <label class="custom-control-label" for="jobRadio">Job</label>
								        </div>';
									}

									echo '<br><br>
								        <div class="form-group">
											<label>Institute Name:</label>
											<input type="text" class="form-control" id="editInstitute" value="'.$user['instituteName'].'" placeholder="Institute Name">
										</div>
										<div class="form-group">
											<label>Short Summary About Yourself:</label>
							    			<textarea class="form-control" id="editSummary" name="shortSummary" rows="4"  placeholder="Short Summary">'.$user['summary'].'</textarea>
						  				</div>
						  				
										<div class="form-group">
											<label>Skills/ Interests/ Hobbies:</label>
							    			<textarea class="form-control" id="editSkills" name="skills" rows="4"  placeholder="What do you like to do?">'.$user['skills'].'</textarea>
						  				</div>
									</form>
								</div>

						    	<div class="modal-footer">
							        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
							        <button type="button" id="saveButton" class="btn btn-primary">Save</button>
						      	</div>
						    </div>
						  </div>
						</div>


						<div class="modal fade" id="exampleModalLongAbout" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
						  <div class="modal-dialog modal-lg" role="document">
						    <div class="modal-content">
						      <div class="modal-header">
						        <h5 class="modal-title" id="loginMoadalTitle">About</h5>
						        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
						          <span aria-hidden="true">&times;</span>
						        </button>
						      </div>
						     	<div class="modal-body">
							        
							      	<form>
							      		
										<div class="form-group">
											<label>Name:</label>
											<input type="text" class="form-control" id="editName" value="'.$user['name'].'" placeholder="Full Name" disabled>
										</div>
										<div class="form-group">
											<label>Email:</label>
											<input type="email" class="form-control" id="editEmail" value="'.$user['email'].'" placeholder="Email Address" disabled>
										</div>
										<div class="form-group">
											<label>Date of Birth:</label>
											<input type="date" class="form-control" id="editDOB" value="'.$user['dob'].'" placeholder="Date Of Birth" disabled>
										</div>
										<div class="form-group">
											<label>Country:</label>
											<input type="text" class="form-control" id="editCountry" value="'.$user['country'].'" placeholder="Which country are you from?" disabled>
										</div>';
									if($user['occupation']=='')
									{
										echo '<div class="custom-control custom-radio custom-control-inline">
								            <input type="radio" id="studentRadio" name="occupationRadio" class="custom-control-input occupationClass" value="Student" disabled>
								            <label class="custom-control-label" for="studentRadio">Student</label>
								          </div>
								          <div class="custom-control custom-radio custom-control-inline">
								            <input type="radio" id="jobRadio" name="occupationRadio" class="custom-control-input occupationClass" value="Works" disabled>
								            <label class="custom-control-label" for="jobRadio">Job</label>
								        </div>';
									}
									else if($user['occupation']=='Student')
									{
										echo '<div class="custom-control custom-radio custom-control-inline">
								            <input type="radio" id="studentRadio" name="occupationRadio" class="custom-control-input occupationClass" value="Student" checked disabled>
								            <label class="custom-control-label" for="studentRadio">Student</label>
								          </div>
								          <div class="custom-control custom-radio custom-control-inline">
								            <input type="radio" id="jobRadio" name="occupationRadio" class="custom-control-input occupationClass" value="Works" disabled>
								            <label class="custom-control-label" for="jobRadio">Job</label>
								        </div>';
									}
									else
									{
										echo '<div class="custom-control custom-radio custom-control-inline">
								            <input type="radio" id="studentRadio" name="occupationRadio" class="custom-control-input occupationClass" value="Student" disabled>
								            <label class="custom-control-label" for="studentRadio">Student</label>
								          </div>
								          <div class="custom-control custom-radio custom-control-inline">
								            <input type="radio" id="jobRadio" name="occupationRadio" class="custom-control-input occupationClass" value="Works" checked disabled>
								            <label class="custom-control-label" for="jobRadio">Job</label>
								        </div>';
									}

									echo '<br><br>
								        <div class="form-group">
											<label>Institute Name:</label>
											<input type="text" class="form-control" id="editInstitute" value="'.$user['instituteName'].'" placeholder="Institute Name" disabled>
										</div>
										<div class="form-group">
											<label>Short Summary About Yourself:</label>
							    			<textarea class="form-control" id="editSummary" name="shortSummary" rows="4"  placeholder="Short Summary" disabled>'.$user['summary'].'</textarea>
						  				</div>
						  				
										<div class="form-group">
											<label>Skills/ Interests/ Hobbies:</label>
							    			<textarea class="form-control" id="editSkills" name="skills" rows="4"  placeholder="What do you like to do?" disabled>'.$user['skills'].'</textarea>
						  				</div>
									</form>
								</div>

						    	<div class="modal-footer">
							        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						      	</div>
						    </div>
						  </div>
						</div>';
		
		echo '<div class="profileName">'.$user['name'].'</div>';
    	echo '<div class="profileUsername">'.$user['username'].'</div>';
    	// echo '<div class="profileSmallDetails"> Student at Chittagong University of Engineering & Technology </div>';
    	// echo '<div class="profileCountry"> Bangladesh </div>';
    	echo '<div class="profileSmallDetails">';
    	if($user['occupation'] == 'Works')
    	{
    		echo ' Works ';
    	}
    	else if($user['occupation'] == 'Student')
    	{
    		echo ' Student ';
    	}
    	if($user['instituteName'])
    	{
    		echo 'at '.$user['instituteName'];
    	}
    	echo "</div>";
    	if($user['country'])
    	{
    		echo '<div class="profileCountry"> '.$user['country'].' </div>';
    	}

    	echo '</div>

				</div>';

		echo '<div class="myPostsProfileButtonsDiv">
				<button type="button" class="btn btn-primary" id="myPostsButton">My Posts</button>
				<button type="button" class="btn btn-primary" id="sharedPostsButton">Shared Posts</button>
			</div>';

		

		echo '<div class="myPostsDiv">';
			displayPosts('myPosts');
		echo '</div>';

		echo '<div class="SharedPostsDiv">';
			displayPosts('SharedPosts');
		echo '</div>';

	}
	


?>