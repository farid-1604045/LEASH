<?php 
	include("functions.php");
	$captionVar = "";
	if(isset($_POST['postStatus']))
	{
		if(strlen(trim($_POST['captionName'])) or !(!file_exists($_FILES['myimage']['tmp_name']) || !is_uploaded_file($_FILES['myimage']['tmp_name'])))
		{
			if (strlen(trim($_POST['captionName']))) {
			    // Escape any html characters
			    $captionVar = htmlentities($_POST['captionName']);
			}


			if (!(!file_exists($_FILES['myimage']['tmp_name']) || !is_uploaded_file($_FILES['myimage']['tmp_name']))) {
			    //not needed here
				$imagename=$_FILES["myimage"]["name"]; 
				// echo "$imagename";
				// $imageType = mysqli_real_escape_string($link,$_FILES['myimage']['type']);

				//Get the content of the image and then add slashes to it 
				$imagetmp=addslashes(file_get_contents($_FILES['myimage']['tmp_name']));
				// echo "$imagetmp";

				
			}

			if(strlen(trim($_POST['captionName'])) and !(!file_exists($_FILES['myimage']['tmp_name']) || !is_uploaded_file($_FILES['myimage']['tmp_name'])))
			{
				$postStatusQuery = "INSERT INTO posts(caption,image,userid,dateTimeColName) VALUES('".$captionVar."','".$imagetmp."',".mysqli_real_escape_string($link,$_SESSION['id']).",NOW())";
				mysqli_query($link,$postStatusQuery);
				// echo "both";
				header("Location: http://leashtest.epizy.com/");
			}
			else if(strlen(trim($_POST['captionName'])))
			{
				$postStatusQuery = "INSERT INTO posts(caption,userid,dateTimeColName) VALUES('".$captionVar."',".mysqli_real_escape_string($link,$_SESSION['id']).",NOW())";
				mysqli_query($link,$postStatusQuery);
				echo "caption";
				header("Location: http://leashtest.epizy.com/");
			}
			else
			{
				$postStatusQuery = "INSERT INTO posts(image,userid,dateTimeColName) VALUES('".$imagetmp."',".mysqli_real_escape_string($link,$_SESSION['id']).",NOW())";
				mysqli_query($link,$postStatusQuery);
				echo "file";
				header("Location: http://leashtest.epizy.com/");
			}


		}
		else
		{
			// $message = "Empty";
			// echo "<script type='text/javascript'>alert('$message');</script>";
			// flush();
			// sleep(1);
			// echo "<script type='text/javascript'>$('#uploadFail').html('Empty').show();</script>";
			header("Location: http://leashtest.epizy.com/");
			
			
		}
		
		
	}

	

?>