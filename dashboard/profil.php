<?php
session_start();

require './konfiguracije.php';

$db_path = '../zelje_db/';
$trenutna_godina = date("Y");

if(isset($_SESSION['login']) or isset($_COOKIE['login'])){
		
	$config = ucitaj_konfiguracije();
	$username = $config['admin']['username'];
	$ime = $config['account']['name'];
	$photoPath = $config['account']['photoPath'];
	$photo = $config['account']['photo'];
	$br = $config['notifikacije']['brojNovih'];
	
	if(isset($_POST['edit'])){
		
		$flag = '';
		$stara = $photoPath.$photo;
		$nova = $photoPath.$_FILES["slika"]["name"];
		if($nova == $stara)$flag = '1';
		$nova .= $flag;
		
		if(move_uploaded_file($_FILES["slika"]["tmp_name"], $nova)){
			if(unlink($stara)){
				$config['account']['photo'] = $_FILES["slika"]["name"].$flag;
				sacuvaj_konfiguracije($config);
				header('Location: ./profil.php');
			}
		}else{
			//bekapuj staru
		}
	}

}else{
	header('Location: ../admin.php');	
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Title Page-->
    <title>Profil</title>

    <!-- Fontfaces CSS-->
    <link href="css/font-face.css" rel="stylesheet" media="all">
    <link href="vendor/font-awesome-4.7/css/font-awesome.min.css" rel="stylesheet" media="all">
    <link href="vendor/font-awesome-5/css/fontawesome-all.min.css" rel="stylesheet" media="all">
    <link href="vendor/mdi-font/css/material-design-iconic-font.min.css" rel="stylesheet" media="all">

    <!-- Bootstrap CSS-->
    <link href="vendor/bootstrap-4.1/bootstrap.min.css" rel="stylesheet" media="all">

    <!-- Vendor CSS-->
    <link href="vendor/animsition/animsition.min.css" rel="stylesheet" media="all">
    <link href="vendor/bootstrap-progressbar/bootstrap-progressbar-3.3.4.min.css" rel="stylesheet" media="all">
    <link href="vendor/wow/animate.css" rel="stylesheet" media="all">
    <link href="vendor/css-hamburgers/hamburgers.min.css" rel="stylesheet" media="all">
    <link href="vendor/slick/slick.css" rel="stylesheet" media="all">
    <link href="vendor/select2/select2.min.css" rel="stylesheet" media="all">
    <link href="vendor/perfect-scrollbar/perfect-scrollbar.css" rel="stylesheet" media="all">

    <!-- Main CSS-->
    <link href="css/theme.css" rel="stylesheet" media="all">
	<link rel = 'icon' href = 'favicon.ico'>

</head>

<body class="animsition">
    <div class="page-wrapper">
        <!-- HEADER DESKTOP-->
        <header class="header-desktop3 d-none d-lg-block">
            <div class="section__content section__content--p35">
                <div class="header3-wrap">
                    <div class="header__logo">
                        <a href="#">
                            <img src="images/logo-white.png" alt="CoolAdmin" />
                        </a>
                    </div>
                    <div class="header__navbar" style='left:35%;'>
                        <ul class="list-unstyled">
                            <li class="has-sub">
                                <a href="./dashboard.php">
                                    <i class="fas fa-tachometer-alt"></i>Dashboard
                                    <span class="bot-line"></span>
                                </a>
                            </li>
                            <li>
                                <a href="./sve_zelje.php">
                                    <i class="fas fa-table"></i>
                                    <span class="bot-line"></span>Tabela zelja</a>
                            </li>
                            <li>
                                <a href="#">
                                    <i class="fas fa-user"></i>
                                    <span class="bot-line"></span>Profil</a>
                            </li>
                        </ul>
                    </div>
                    <div class="header__tool">
                        <div class="header-button-item <?php echo $br?'has-noti':''?> js-item-menu">
							<i class="zmdi zmdi-notifications"></i>
							<div class="notifi-dropdown notifi-dropdown--no-bor js-dropdown">
								<div <?php echo $br?'':' hidden'?> class="notifi__title">
									<p>Imate nove notifikacije</p>
								</div>
								<div onclick = 'prikazi_nove();' class="notifi__item">
									<div class="bg-c1 img-cir img-40">
										<i class="zmdi zmdi-email-open"></i>
									</div>
									<div class="content">
									<?php if($br):?>
										<p>Dobili ste <?php echo $br?> nova pisma!</p>
									<?php else:?>
									<p>Nema novih notifikacija</p>
									<?php endif;?>	
									</div>
								</div>
							   
								<div class="notifi__footer">
									<a onclick = 'ocisti_notifikacje(this)' href="#">Clear notifications</a>
								</div>
							</div>
						</div>
                        <div onclick = 'location.href="./podesavanja.php";' class="header-button-item js-item-menu">
							<i class="zmdi zmdi-settings"></i>  
						</div>
                        <div class="account-wrap">
                            <div class="account-item account-item--style2 clearfix js-item-menu">
                                <div class="image">
                                    <img src="<?php echo $photoPath.$photo; ?>" alt="<?php echo $ime; ?>" />
                                </div>
                                <div class="content">
                                    <a class="js-acc-btn" href="#"><?php echo $ime; ?></a>
                                </div>
                                <div class="account-dropdown js-dropdown">
                                    <div class="info clearfix">
                                        <div class="image">
                                            <a href="#">
                                                <img src="<?php echo $photoPath.$photo; ?>" alt="<?php echo $ime; ?>" />
                                            </a>
                                        </div>
                                        <div class="content">
                                            <h5 class="name">
                                                <a href="#"><?php echo $ime; ?></a>
                                            </h5>
                                            <span class="email"><?php echo $username; ?></span>
                                        </div>
                                    </div>
                                    <div class="account-dropdown__body">
                                        <div class="account-dropdown__item">
                                            <a href="#">
                                                <i class="zmdi zmdi-account"></i>Profil</a>
                                        </div>
                                        <div class="account-dropdown__item">
                                            <a href="./podesavanja.php">
                                                <i class="zmdi zmdi-settings"></i>Podesavanja</a>
                                        </div>                                     
                                    </div>
                                    <div class="account-dropdown__footer">
                                        <a href="../admin.php?logout=true">
                                            <i class="zmdi zmdi-power"></i>Odjava</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <!-- END HEADER DESKTOP-->

        <!-- HEADER MOBILE-->
        <header class="header-mobile header-mobile-2 d-block d-lg-none">
            <div class="header-mobile__bar">
                <div class="container-fluid">
                    <div class="header-mobile-inner">
                        <a class="logo" href="index.html">
                            <img src="images/logo-white.png" alt="CoolAdmin" />
                        </a>
                        <button class="hamburger hamburger--slider" type="button">
                            <span class="hamburger-box">
                                <span class="hamburger-inner"></span>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
            <nav class="navbar-mobile">
                <div class="container-fluid">
                    <ul class="navbar-mobile__list list-unstyled">
                        <li class="has-sub">
                            <a class="js-arrow" href="./dashboard.php">
                                <i class="fas fa-tachometer-alt"></i>Dashboard</a>
                        </li>
                        <li>
                            <a href="./sve_zelje.php">
                                <i class="fas fa-table"></i>Tabela zelja</a>
                        </li>
                        <li>
                            <a href="#">
                                <i class="fas fa-user"></i>Profil</a>
                        </li>
                        
                        
                    </ul>
                </div>
            </nav>
        </header>
        <div class="sub-header-mobile-2 d-block d-lg-none">
            <div class="header__tool">
                <div class="header-button-item <?php echo $br?'has-noti':''?> js-item-menu">
                    <i class="zmdi zmdi-notifications"></i>
                    <div class="notifi-dropdown notifi-dropdown--no-bor js-dropdown">
                        <div <?php echo $br?'':' hidden'?> class="notifi__title">
                            <p>Imate nove notifikacije</p>
                        </div>
                        <div onclick = 'prikazi_nove();' class="notifi__item">
                            <div class="bg-c1 img-cir img-40">
                                <i class="zmdi zmdi-email-open"></i>
                            </div>
                            <div class="content">
                                <p>Dobili ste <?php echo $br?> nova pisma!</p>
                                
                            </div>
                        </div>
                       
                        <div class="notifi__footer">
                            <a href="#">Clear notifications</a>
                        </div>
                    </div>
                </div>
                <div onclick = 'location.href="./podesavanja.php";' class="header-button-item js-item-menu">
					<i class="zmdi zmdi-settings"></i>  
                </div>
                <div class="account-wrap">
                    <div class="account-item account-item--style2 clearfix js-item-menu">
                        <div class="image">
                            <img src="<?php echo $photoPath.$photo; ?>" alt="<?php echo $ime; ?>" />
                        </div>
                        <div class="content">
                            <a class="js-acc-btn" href="#"><?php echo $ime; ?></a>
                        </div>
                        <div class="account-dropdown js-dropdown">
                            <div class="info clearfix">
                                <div class="image">
                                    <a href="#">
                                        <img src="<?php echo $photoPath.$photo; ?>" alt="<?php echo $ime; ?>" />
                                    </a>
                                </div>
                                <div class="content">
                                    <h5 class="name">
                                        <a href="#"><?php echo $ime; ?></a>
                                    </h5>
                                    <span class="email"><?php echo $username; ?></span>
                                </div>
                            </div>
                            <div class="account-dropdown__body">
                                <div class="account-dropdown__item">
                                    <a href="#">
                                        <i class="zmdi zmdi-account"></i>Profil</a>
                                </div>
                                <div class="account-dropdown__item">
                                    <a href="./podesavanja.php">
                                        <i class="zmdi zmdi-settings"></i>Podesavanja</a>
                                </div>
                            </div>
                            <div class="account-dropdown__footer">
                                <a href="../admin.php?logout=true">
                                    <i class="zmdi zmdi-power"></i>Odjava</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END HEADER MOBILE -->

        <!-- PAGE CONTENT-->
        <div class="page-content--bgf7">
            <!-- BREADCRUMB-->
            <section class="au-breadcrumb2">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="au-breadcrumb-content">
                                <div class="au-breadcrumb-left">
                                    <span class="au-breadcrumb-span">Trenutna lokacija:</span>
                                    <ul class="list-unstyled list-inline au-breadcrumb__list">
                                        <li class="list-inline-item active">
                                            <a href="./dashboard.php">Dashboard</a>
                                        </li>
                                        <li class="list-inline-item seprate">
                                            <span>/</span>
                                        </li>
											<li class="list-inline-item">Profil</li>
                                    </ul>
                                </div>
                                <form class="au-form-icon--sm" action="./sve_zelje.php" method="get">
                                    <input name = 'upit' class="au-input--w300 au-input--style2" type="text" placeholder="Pretrazi tabelu...">
                                    <button class="au-btn--submit2" type="submit">
                                        <i class="zmdi zmdi-search"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- END BREADCRUMB-->

     
            <!-- END WELCOME-->
			
		<div class="container emp-profile">
            <form method="post" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-4">
                        <div class="profile-img">
                            <img src="<?php echo $photoPath.$photo; ?>" alt="<?php echo $ime; ?>"/>
                            <div class="file btn btn-lg btn-primary">
                                Izmeni sliku
                                <input onchange = '$("#edit").removeAttr("disabled");$("#edit").addClass("btn-success");' name= 'slika' type="file" accept = 'image/*'/>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="profile-head">
                                    <h3>
                                       <?php echo $ime; ?>
                                    </h3>
                                    <h4>
                                        #<?php  echo $username; ?>
                                    </h4>
                                    <p class="proile-rating">Admin</p>

                        </div>
                    </div>
                    <div class="col-md-2">
                        <input disabled type="submit" class="btn" id = 'edit' name="edit" value="🖊️Edit"/>
                    </div>
                </div>
            
            </form>           
        </div>
            <!-- COPYRIGHT-->
            <section class="p-t-60 p-b-20">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="copyright">
                                <p>Copyright © <?php echo $trenutna_godina;?> DEDA admin</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- END COPYRIGHT-->
        </div>

    </div>
	
	<script>var old_number = <?php echo $br;?>;</script>
    <!-- Jquery JS-->
    <script src="vendor/jquery-3.2.1.min.js"></script>
    <!-- Bootstrap JS-->
    <script src="vendor/bootstrap-4.1/popper.min.js"></script>
    <script src="vendor/bootstrap-4.1/bootstrap.min.js"></script>
    <!-- Vendor JS       -->
    <script src="vendor/slick/slick.min.js">
    </script>
    <script src="vendor/wow/wow.min.js"></script>
    <script src="vendor/animsition/animsition.min.js"></script>
    <script src="vendor/bootstrap-progressbar/bootstrap-progressbar.min.js">
    </script>
    <script src="vendor/counter-up/jquery.waypoints.min.js"></script>
    <script src="vendor/counter-up/jquery.counterup.min.js">
    </script>
    <script src="vendor/circle-progress/circle-progress.min.js"></script>
    <script src="vendor/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="vendor/chartjs/Chart.bundle.min.js"></script>
    <script src="vendor/select2/select2.min.js">
    </script>

    <!-- Main JS-->
    <script src="js/main.js"></script>

</body>

</html>
<!-- end document-->
