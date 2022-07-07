<?php
	 include("functions.php");
	include("views/header.php");
	if(isset($_SESSION['id']) && !empty($_SESSION['id']))
	{
		//logged in
		if($_GET['page'] == 'timeline')
		{
			// echo "1";
			include("views/profile.php");
			// echo "2";
		}
		else if($_GET['page'] == 'messages')
		{
			// echo "3";
			include("views/messages.php");
		}
		else if($_GET['page'] == 'homePage')
		{
			include("views/home2.php");
		}
		else
		{
			// echo "4";
			include("views/home.php");
		}
		
	}
	else
	{
		// not logged in
		// echo "5";
		include("views/firstPageContent.php");
		include("views/footer.php");
		
	}
	// echo "6";
	include("views/javas.php");
	
?>