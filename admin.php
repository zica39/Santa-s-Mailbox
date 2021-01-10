<?php
session_start();

$msg = '';
	
	if(isset($_GET['logout'])){
		
		if($_GET['logout'] == 'true'){
			
			session_unset();
			
			unset($_COOKIE['login']);
			setcookie('login', '');
			
			header('Location: ./admin.php');
		}
	}
	
	if(isset($_SESSION['login']) or isset($_COOKIE['login'])){
		header('Location: ./dashboard/dashboard.php');
	}
	
	if(isset($_POST['user']) && isset($_POST['password'])){
		$admin = parse_ini_file('./config/config.ini',true);
		$user = $admin['admin']['username'];
		$pass = $admin['admin']['password'];
		if(md5($_POST['password']) == $pass && $user == $_POST['user']){
			
			$_SESSION['login'] = $pass;
			
		if($_POST['zapamti']){
				setcookie("login", $pass, time()+3600);
		}
			
			header('Location: ./dashboard/dashboard.php');
			
		}else{
			
			$msg = 'Username or Password incorrect';
		}
 
		
	}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Deda Admin</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel = 'icon' href = 'favicon.ico'>
	<!-- <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> -->
	<link href='https://fonts.googleapis.com/css?family=Open Sans' rel='stylesheet' type='text/css'>
	<link href="https://netdna.bootstrapcdn.com/font-awesome/3.0.2/css/font-awesome.css" rel="stylesheet">

	<link rel="stylesheet" type="text/css" href="style/styles.css">
</head>
<body class = 'lightBg'>
	
	<div id="content" class='container'>
    <h1 class='admin'>DEDA Admin</h1>
	
	<div <?php if($msg)echo''; else echo'hidden '; ?>class="alert error">
		<input type="checkbox" id="alert1"/>
    <label class="close" title="close" for="alert1" >
      <i class="icon-remove"></i>
    </label>
		<p class="inner">
			<strong> <?php echo $msg;?> !</strong>
		</p>
	</div>
	
    <form action="" method="post" autocomplete="on" >
        
        <p>
            <label for="user" class="icon-user"> Korisničko ime
                
            </label>
            <input type="text" name="user" id="user" required="required" placeholder="korisničko ime" />
        </p>

        <p>
            <label for="password" class="icon-key"> Lozinka
              
            </label>
            <input type="password" name="password" id="password" placeholder="lozinka" required="required" />
        </p>

	<input type="checkbox" name = 'zapamti' id="zapamti" class='sub'> <label for = 'zapamti' >Zapamti me</label>
		
        <button type="submit" name = 'login' value=" LogIn" >
			Prijava <i class="icon-signin"></i>  
		</button>

    </form>
</div>
	
	<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
	<script type="text/javascript" src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

</body>
</html>