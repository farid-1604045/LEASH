<?php 
	include("functions.php");
	// echo "hi";
	// header("Location: http://leashtest.epizy.com/?page=timelinee");

	if(isset($_POST['postDP']))
	{
		// echo "5";
		if(!(!file_exists($_FILES['myDP']['tmp_name']) || !is_uploaded_file($_FILES['myDP']['tmp_name'])))
		{

			// echo "6";
			if (!(!file_exists($_FILES['myDP']['tmp_name']) || !is_uploaded_file($_FILES['myDP']['tmp_name']))) {
			    //not needed here
				$imagename=$_FILES["myDP"]["name"]; 
				// echo "$imagename";
				// $imageType = mysqli_real_escape_string($link,$_FILES['myDP']['type']);

				//Get the content of the image and then add slashes to it 
				$imagetmp=addslashes(file_get_contents($_FILES['myDP']['tmp_name']));
				// echo "$imagetmp";

				
			}

			if(!(!file_exists($_FILES['myDP']['tmp_name']) || !is_uploaded_file($_FILES['myDP']['tmp_name'])))
			{
				// echo "7";
				$postStatusQuery = "UPDATE users SET DP='".$imagetmp."' WHERE id=".mysqli_real_escape_string($link,$_SESSION['id'])."";
				// echo $postStatusQuery;

				// $postStatusQuery = "INSERT INTO posts(caption,image,userid,dateTimeColName) VALUES('".$captionVar."','".$imagetmp."',".mysqli_real_escape_string($link,$_SESSION['id']).",NOW())";
				mysqli_query($link,$postStatusQuery);
				 // echo "both";
				 header("Location: http://leashtest.epizy.com/?page=timeline");
			}

		}
		else
		{
			// echo "6";
			// $message = "Empty";
			// echo "<script type='text/javascript'>alert('$message');</script>";
			// flush();
			// sleep(1);
			// echo "<script type='text/javascript'>$('#uploadFail').html('Empty').show();</script>";
			 header("Location: http://leashtest.epizy.com/?page=timeline");
			
			
		}
		
		
	}

	

?>