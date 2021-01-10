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
	
	$ispunjena = 0;
	$neispunjena = 0;
	$obrisana = $config['statistika']['obrisane'];
	$sva_pisma = $obrisana;
	
	$sjever = 0;
	$centar = 0;
	$jug = 0;
	$gradovi = [];
	$mjeseci = [];
	
	$podaci_niz = [];
	$podaci = scandir ($db_path);
	
	foreach($podaci as $podatak){
		$file = $db_path.$podatak;
		if(is_file($file)){
			$podat = json_decode(file_get_contents($file),true);
			if(is_array($podat)){
				$sva_pisma += 1;
				if($podat['status'] == 'Ispunjena')$ispunjena+=1;
				else $neispunjena+=1;
				
				if($podat['regija'] == 'Sjever')$sjever+=1;
				else if($podat['regija'] == 'Jug')$jug+=1;
				else $centar+=1;
				
				if(isset($gradovi[$podat['grad']]))
				$gradovi[$podat['grad']] +=1;
				else
				$gradovi[$podat['grad']] = 1;
				
				$datum = $podat['datum'];
				$datum = explode('-',$datum);
				if($datum[0] == $trenutna_godina){
					$mjesec = $datum[1];
					if(isset($mjeseci[$mjesec]))
					 $mjeseci[$mjesec] +=1;
					else
					 $mjeseci[$mjesec] = 1;
						
				}
			}
		}
	}
	
	if(count($gradovi))arsort($gradovi);
	

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
    <title>Dashboard</title>

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
                                <a href="#">
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
                                <a href="./profil.php">
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
                                            <a href="./profil.php">
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
                            <a class="js-arrow" href="#">
                                <i class="fas fa-tachometer-alt"></i>Dashboard</a>
                        </li>
                        <li>
                            <a href="./sve_zelje.php">
                                <i class="fas fa-table"></i>Tabela zelja</a>
                        </li>
                        <li>
                            <a href="./profil.php">
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
                                    <a href="./profil.php">
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
                                            <a href="#">Dashboard</a>
                                        </li>
                                        <li class="list-inline-item seprate">
                                            <span>/</span>
                                        </li>
                                        <!--<li class="list-inline-item">Dashboard</li>-->
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

            <!-- WELCOME-->
            <section class="welcome p-t-10">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <h1 class="title-4">Welcome back
                                <span>Deda Mraze!</span>
                            </h1>
                            <hr class="line-seprate">
                        </div>
                    </div>
                </div>
            </section>
            <!-- END WELCOME-->

            <!-- STATISTIC-->
            <section class="statistic statistic2">
                <div class="container">
                    <div class="row">
                        <div class="col-md-6 col-lg-3">
                            <div class="statistic__item statistic__item--green">
                                <h2 class="number"><?php echo $sva_pisma;?></h2>
                                <span class="desc">Poslatiih pisama</span>
                                <div class="icon">
                                    <i class="zmdi zmdi-account-o"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="statistic__item statistic__item--blue">
                                <h2 class="number"><?php echo $ispunjena;?></h2>
                                <span class="desc">Ispunjenih zelja</span>
                                <div class="icon">
                                    <i class="zmdi zmdi-calendar-check"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="statistic__item statistic__item--orange">
                                <h2 class="number"><?php echo $neispunjena;?></h2>
                                <span class="desc">Neispunjenih zelja</span>
                                <div class="icon">
                                    <i class="zmdi zmdi-calendar-note"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="statistic__item statistic__item--red">
                                <h2 class="number"><?php echo $obrisana;?></h2>
                                <span class="desc">Neprikladnih zelja</span>
                                <div class="icon">
                                    <i class="zmdi zmdi-delete"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- END STATISTIC-->

            <!-- STATISTIC CHART-->
            <section class="statistic-chart">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <h3 class="title-5 m-b-35">Statistika</h3>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-lg-4">
                            <!-- CHART-->
                            <div class="statistic-chart-1">
                                <h3 class="title-3 m-b-30">Pisma po mjesecima</h3>
                                <div class="chart-wrap">
                                    <canvas id="widgetChart5"></canvas>
                                </div>
                                <div class="statistic-chart-1-note">
                                    <span class="big">Ovog mjeseca</span>
                                    <span>/ <?php if(count($mjeseci))echo $mjeseci[date('m')];?> </span>
                                </div>
                            </div>
                            <!-- END CHART-->
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <!-- TOP CAMPAIGN-->
                            <div class="top-campaign">
                                <h3 class="title-3 m-b-30">Po gradovima</h3>
                                <div class="table-responsive">
                                    <table class="table table-top-campaign">
                                        <tbody>
										<?php 
										$index = 1;
										foreach($gradovi as $grad => $val){
											echo "<tr>".
											"<td>$index. $grad</td>".
											"<td>$val</td>".
											"</tr>";
											$index+=1;
											
										}
										?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- END TOP CAMPAIGN-->
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <!-- CHART PERCENT-->
                            <div class="chart-percent-2">
                                <h3 class="title-3 m-b-30">Po regijama </h3>
                                <div class="chart-wrap">
                                    <canvas id="percent-chart2"></canvas>
                                    <div id="chartjs-tooltip">
                                        <table></table>
                                    </div>
                                </div>
                                <div class="chart-info">
                                    <div class="chart-note">
                                        <span class="dot dot--red"></span>
                                        <span>sjever</span>
                                    </div>
									<div class="chart-note">
                                        <span class="dot dot--green"></span>
                                        <span>centar</span>
                                    </div>
                                    <div class="chart-note">
                                        <span class="dot dot--blue"></span>
                                        <span>jug</span>
                                    </div>
                                </div>
                            </div>
                            <!-- END CHART PERCENT-->
                        </div>
                    </div>
                </div>
            </section>
            <!-- END STATISTIC CHART-->
			<script>
			var old_number = <?php echo $br;?>;
			
			var sjever = <?php echo $sjever;?>;
			var jug = <?php echo $jug;?>;
			var centar = <?php echo $centar;?>;
			var mjeseci_niz = [];
			<?php 
				foreach($mjeseci as $val){?>
					mjeseci_niz.push(<?php echo $val;?>);
			<?php 		
				}
			?>
			</script>
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
