<?php

require './sortiranje.php';
require './pretraga.php';
require './konfiguracije.php';

session_start();
$db_path = '../zelje_db/';
$trenutna_godina = date("Y");
$limit = 5;
$page = 0;

$config = ucitaj_konfiguracije();
$username = $config['admin']['username'];
$ime = $config['account']['name'];
$photoPath = $config['account']['photoPath'];
$photo = $config['account']['photo'];
$br = $config['notifikacije']['brojNovih'];

if(isset($_SESSION['login']) or isset($_COOKIE['login'])){
	
	$po = 'sve';
	$poredak = 'rastuci';
	
	$podaci_niz = [];
	$podaci = scandir ($db_path);
	foreach($podaci as $podatak){
		$file = $db_path.$podatak;
		if(is_file($file)){
			$podat = json_decode(file_get_contents($file),true);
			if(is_array($podat))$podat['id'] = $podatak;
			
			if(isset($_GET['akcija']) && isset($_GET['id'])){
				if($_GET['akcija'] == 'status'){
					if($_GET['id'] == $podat['id']){
						$podat['status'] = 'Ispunjena';
						
						$id = $_GET['id'];
						$txt = json_encode($podat);
						if(file_put_contents($db_path.$id,$txt)){
							Header('Location: ../dashboard/sve_zelje.php');
						}
						
					}
				}else if($_GET['akcija'] == 'obrisi'){
					if($_GET['id'] == $podat['id']){
						$id = $_GET['id'];
						
						if(unlink($db_path.$id)){
							$config = ucitaj_konfiguracije();
							$config['statistika']['obrisane'] = intval($config['statistika']['obrisane'])+ 1;
							sacuvaj_konfiguracije($config);
							Header('Location: ../dashboard/sve_zelje.php');
						}
						
					}
				}
			}
			
			$podaci_niz[] = $podat;
		}
	}
	
	if(isset($_GET['sortiraj']) and isset($_GET['poredak'])){
		
		if($_GET['sortiraj'] != 'sve'){
		$podaci_niz = sortiraj_niz($podaci_niz,$_GET['sortiraj'],$_GET['poredak']);
		$po = $_GET['sortiraj'];
		$poredak = $_GET['poredak'];
		}else{
			
			if($_GET['poredak'] == 'opadajuci')rsort($podaci_niz);
			$po = $_GET['sortiraj'];
			$poredak = $_GET['poredak'];
		}
		
	}
	
	if(!empty($_GET['upit'])){
		
		$podaci_niz = pretrazi_niz($podaci_niz,$_GET['upit']);	
	}
	
	if(isset($_GET['page'])){
		
		$page = $_GET['page'];
			
	}
	
	if(isset($_GET['nove'])){
			
		if($br>0){
			$limit = $br;
			$nove_niz = [];
			$niz_id = explode(',',$config['notifikacije']['ids']);
			
			foreach($podaci_niz as $n){
				if(in_array(str_replace('.txt','',$n['id']),$niz_id)){
					$nove_niz[] = $n;
				}
			}
			$podaci_niz = $nove_niz;
			$br = 0;
		}else{
			$podaci_niz = [];
		}
		
		$config1 = ucitaj_konfiguracije();
		$config1['notifikacije']['brojNovih'] = 0;
		$config1['notifikacije']['ids'] = '';
		sacuvaj_konfiguracije($config1);
	}
	
	if(isset($_GET['limit'])){
		$limit = $_GET['limit'];
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
    <title>Sve zelje</title>

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
	<link rel = 'icon' href = 'favicon.ico'>
    <link href="css/theme.css" rel="stylesheet" media="all">

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
                                <a href="#">
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
                            <a class="js-arrow" href="./dashboard.php">
                                <i class="fas fa-tachometer-alt"></i>Dashboard</a>
                        </li>
                        <li>
                            <a href="#">
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
                                        <li class="list-inline-item">Tabela zelja</li>
                                    </ul>
                                </div>
                                <form class="au-form-icon--sm" action="" method="get">
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

            <!-- DATA TABLE-->
            <section class="p-t-20">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <h3 class="title-5 m-b-35">Tabela zelja</h3>
                            <div class="table-data__tool">
                                <div class="table-data__tool-left">
                                    <div class="rs-select2--light rs-select2--md">
                                        <select onchange='sortiraj(this)' class="js-select2" id = 'sortiranje' name="sortiranje" >
                                             <option selected="selected" value='sve' hidden>Sortiraj po</option>  
											 <option value = 'ime'>Ime</option>
											 <option value = 'prezime'>Prezime</option>
											 <option value = 'grad'>Grad</option>
											 <option value = 'regija'>Regija</option>
											 <option value = 'datum'>Datumu</option>
											 <option value = 'status'>Statusu</option> 
                                        </select>					
                                        <div class="dropDownSelect2"></div>
                                    </div>
                                   
                                    <button data-poredak='<?php echo $poredak; ?>' id = 'poredak' onclick = 'poredak_toggle(this)' class="au-btn-filter">
                                        <i class="zmdi zmdi-filter-list <?php echo ($poredak=='rastuci')?'zmdi-hc-rotate-180':''; ?>" ></i>Filter
									</button>
									

									<label class = 'ml-2'>Prikazi:</label>
									<select onchange='limit(this)' class="custom-select w-auto" id = 'limit' name="limit" >
                                             <option selected="selected" value='5' hidden>Prikazi</option>  
											 <option value = '5'>5 pisama</option>
											 <option value = '10'>10 pisama</option>
											 <option value = '25'>25 pisama</option>
									</select>
									
                                </div>
                                <div class="table-data__tool-right">                                  
                                    
									<button onclick = 'window.location.reload();' type="button" class="btn btn-outline-secondary">
                                            <i class="fa fa-refresh"></i>&nbsp; Osvezi tabelu
									</button>
									
									<div class="rs-select2--dark rs-select2--sm rs-select2--dark2">
									<button id = 'pdf' type="button" class="btn btn-outline-secondary">
                                            <i class="fa fa-print"></i>&nbsp; PDF
									</button>
                                    </div>
									
                                </div>
                            </div>
							
                            <div class="table-responsive table-responsive-data2">
                                <table id ='tabela_zelja' class="table table-data2" style = 'text-align:center'>
                                    <thead>
                                        <tr>
                                            <th>Ime</th>
											<th>Prezime</th>
                                            <th>Grad</th>
											<th>Regija</th>
                                            <th>Datum</th>
                                            <th>Status</th>
											<th>Akcije</th>
                                            <th>&nbsp;</th>
                                        </tr>
                                    </thead>
                                    <tbody>
									<?php
									$ne = 'status--denied';
									$da = 'status--process';
																			
										//foreach($podaci_niz as $pod){
											$page = intval($page);
											$start = $page*$limit;
										for ($i = $start; $i < count($podaci_niz); $i++) {
										 $pod = $podaci_niz[$i];
										 
										$ispunjeno  = ($pod['status'] == ('Ispunjena'))?$da:$ne;
										$disabled  = ($pod['status'] == ('Ispunjena'))?"disabled":"";
										
                                        echo "<tr class='tr-shadow'>".
                                            "<td>".$pod['ime']."</td>".
											"<td>".$pod['prezime']."</td>".
											"<td>".$pod['grad']."</td>".
											"<td>".$pod['regija']."</td>".
											"<td>".$pod['datum']."</td>".
                                            "<td><span class='".$ispunjeno."'>".$pod['status']."</span></td>".
                                            
                                            "<td>".
                                                "<div class='table-data-feature'>".
                                                    "<button class='btn btn-white' data-toggle='tooltip' onclick = 'prikazi_pismo(this)' data-id = '".$pod['id']."' data-placement='top' title='Otvori pismo'>".
                                                        "<i class='fa fa-envelope'></i>".
                                                    "</button>".
                                                    "<button class='btn btn-white' ".$disabled." data-toggle='tooltip' onclick = 'oznaci_procitano(this)' data-id = '".$pod['id']."' data-placement='top' title='Oznaci kao ispunjeno'>".
                                                        "<i class='fa fa-check'></i>".
                                                    "</button>".  
													 "<button class='btn btn-white' ".$disabled." data-toggle='tooltip' onclick = 'obrisi_neprikladno(this)' data-id = '".$pod['id']."' data-placement='top' title='Obrisi neprikladno'>".
                                                        "<i class='fa fa-trash'></i>".
                                                    "</button>". 
                                                "</div>".
                                            "</td>".
											"<td hidden>".$pod['zelje']."</td>".
											"<td>&nbsp;</td>".
                                        "</tr>".
										"<tr class='spacer'></tr>";
										
										if($limit>0)if($i - $start>=$limit-1)break;
										}
										?>
                                        
                                       
                                    </tbody>
                                </table>
								
								<?php
								if(isset($_GET['nove']) and count($podaci_niz) == 0){?>
								<p class = 'text-center'><b>Nema novih pisama</b></p>	
								<?php }?>
							
								
								<nav aria-label="Page navigation" class='mt-3'>
									  <ul class="pagination justify-content-end">
										<?php 
											$query = $_SERVER['QUERY_STRING'];
											$arr = explode('&',$query);
											$novi_query = '';
											foreach($arr as $i => $a){
												if(str_contains($a,'page'))unset($arr[$i]);
											}
											$novi_query = implode('&',$arr);
											
											if(strlen($novi_query))$novi_query = '&'.$novi_query;
											//echo $novi_query;
											
										?>
										<li class="page-item <?php echo $page>0?'':'disabled';?>"><a class="page-link" href="?page=<?php echo $page-1,$novi_query;?>">Previous</a></li>
										<?php
										$no_page = ceil(count($podaci_niz)/$limit);
										for($i=0; $i<$no_page; $i++){?>
										<li class="page-item <?php echo $page==$i?'disabled':'';?>"><a class="page-link" href="?page=<?php echo $i,$novi_query;?>"><?php echo $i+1;?></a></li>
										<?php }?>
										
										<li class="page-item <?php echo $page<$no_page-1?'':'disabled';?>"><a class="page-link" href="?page=<?php echo $page+1,$novi_query;?>">Next</a></li>
									  </ul>
								</nav>
								
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- END DATA TABLE-->
			
			<!-- modal medium -->
			<div class="modal fade" id="zeljeModal" tabindex="-1" role="dialog" aria-labelledby="mediumModalLabel" aria-hidden="true">
				<div class="modal-dialog modal-lg" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title" id="mediumModalLabel">Zelje</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body letter">
							<p id = 'zelje'>
								
							</p>
						</div>
						<div class="modal-footer">
							<button type="button" data-dismiss="modal" class="btn btn-primary">Zatvori</button>
						</div>
					</div>
				</div>
			</div>
			<!-- end modal medium -->
			
            <!-- COPYRIGHT-->
            <section class="p-t-60 p-b-20">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="copyright">
                                <p>Copyright © <?php echo $trenutna_godina ?> DEDA admin</p>
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
	
	<script>
		$('#sortiranje option[selected="selected"]').removeAttr('selected');
		$('#sortiranje option[value=<?php echo $po ?>]').attr('selected','selected');
		let opt = $('#sortiranje option[value=<?php echo $po ?>]').html();
		$('#select2-sortiranje-container').html(opt);
		
		$('#limit option[selected="selected"]').removeAttr('selected');
		$('#limit option[value=<?php echo $limit ?>]').attr('selected','selected');
	</script>
	<!-- Pluginovi -->
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.22/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script>
  
</body>

</html>
<!-- end document-->
