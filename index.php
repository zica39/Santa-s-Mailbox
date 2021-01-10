<?php
	session_start();
	require './dashboard/konfiguracije.php';
	
   $sjever =  explode(',','Pljevlja,Bijelo Polje,Berane,Rožaje,Mojkovac,Plav,Kolašin,Žabljak,Plužine,Andrijevica,Šavnik');
   $centar = explode(',','Podgorica,Nikšić,Cetinje,Danilovgrad');
   $jug =  	explode(',','Bar,Herceg Novi,Budva,Ulcinj,Tivat,Kotor');
   
   $msg = '';
   $elem = [];
	
	if(isset($_SESSION['poslato'])){
		unset($_SESSION['poslato']);
		header('Location: ./index.php');
	}
	
 if(isset($_POST['pismo'])){
 
	$ime = isset($_POST['ime'])? $_POST['ime']:'';
	$prezime = isset($_POST['prezime'])? $_POST['prezime']:'';
	$grad = isset($_POST['grad'])? $_POST['grad']:'';
	$zelje = isset($_POST['zelje'])? $_POST['zelje']:'';
	$bio_dobar = isset($_POST['bio_dobar'])? $_POST['bio_dobar']:'';

	
	$ids = ['ime' => $ime,'prezime' => $prezime,'grad' => $grad,'zelje' => $zelje, 'bio_dobar' => $bio_dobar];
	
	
	if($ime and $prezime and $grad and $zelje and $bio_dobar){
		
		//Zastitna mjera u slucaju da je na klijentnoj strani mjenjan select i statisticki podatak za admina
		$regija = '';
		if(in_array($grad,$sjever))$regija = 'Sjever';
		if(in_array($grad,$centar))$regija = 'Centar';
		if(in_array($grad,$jug))$regija = 'Jug';
		
		if(empty($regija))die('Neipravni podaci, molimo ne mjenjate podatke iz select-a!!!');
		
		//latininca i cirilica
		$pattern = '/^[a-zA-ZšđćžčČĆŽĐŠАаНнБбЊњВвОоГгПпДдРрЂђСсЕеТтЖжЋћЗзУуИиФфЈјХхКкЦцЛлЧчЉљЏџМмШш]+$/';
	
		if(!preg_match($pattern, $ime)){
			$elem[] = 'ime';
			$msg = 'Neispravan unos! <br><small>(Ime i prezime moraju sadržati samo slova)</small>';
		}
		else if(!preg_match($pattern, $prezime)){
			$elem[] = 'prezime';
			$msg = 'Neispravan unos! <br><small>(Ime i prezime moraju sadržati samo slova)</small>';
		}else{
		
			date_default_timezone_set('Europe/Belgrade');
			
			$ids['ime'] = ucfirst($ids['ime']);
			$ids['prezime'] = ucfirst($ids['prezime']);
			
			$ids['datum'] = date('Y-m-d H:i');
			$ids['regija'] = $regija;
			$ids['status'] = 'Neispunjena';
			unset($ids['bio_dobar']);

			$txt = json_encode($ids, true);
			$id = uniqid();
			if(file_put_contents("./zelje_db/$id.txt",$txt)){
				
				chdir('./dashboard/');
				$config = ucitaj_konfiguracije();
				$config['notifikacije']['brojNovih'] = intval($config['notifikacije']['brojNovih'])+ 1;
				
				if($config['notifikacije']['ids'])$config['notifikacije']['ids'] .= ','.$id;
				else $config['notifikacije']['ids'] .= $id;
				
				sacuvaj_konfiguracije($config);
				$_SESSION['poslato'] = true;
				Header('Location: ./zelja_poslata.html');
			}
		}
		
	
	}else{
		
		$msg = 'Sva polja moraju biti popunjena!';
		foreach($ids as $key => $val){
			if($val == '')
				$elem[] = $key;
		}
		
		if($ids['bio_dobar'] == '' and count($elem) == 1)
			$msg = 'Morate biti dobri!😞';
		
	}
 }


?>

<!DOCTYPE html>
<html>
<head>
	<title>Pismo Deda Mrazu</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	
	<link rel = 'icon' href = 'favicon.ico'>
	<!-- <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> -->
	<link href='https://fonts.googleapis.com/css?family=Open Sans' rel='stylesheet' type='text/css'>
	<link href="https://netdna.bootstrapcdn.com/font-awesome/3.0.2/css/font-awesome.css" rel="stylesheet">

	<link rel="stylesheet" type="text/css" href="style/styles.css">
</head>
<body class = 'lightBg'>
	
	 
		<!-- <a class = 'login-right' href="./admin.php">Prijava🚪</a> -->
	 
	

	<div id="content" class='container'>
    <h1>Pismo <em>Deda Mrazu</em></h1>
	
	<div <?php if($msg)echo''; else echo'hidden '; ?>class="alert error">
		<input type="checkbox" id="alert1"/>
    <label class="close" title="close" for="alert1" >
      <i class="icon-remove"></i>
    </label>
		<p class="inner">
			<strong> <?php echo $msg;?> </strong>
		</p>
	</div>
	
    <form action="" method="post" autocomplete="on" onsubmit = 'trimAll();'>
        
            <label for="ime">🧒 Ime i Prezime
                <span class="required">*</span>
            </label>
			
			<div class = 'div-ime'>
            <input type="text" name="ime" id="ime" class = 'mr-5' value="<?php if(isset($_POST['ime'])){ echo $_POST['ime']; } ?>"  placeholder="Unesi ime" />
            <input type="text" name="prezime" id="prezime" value="<?php if(isset($_POST['prezime'])){ echo $_POST['prezime']; } ?>" placeholder="Unesi prezime"  />
			</div>
        
        <p>
            <label for="grad" >🏙️ Grad
				<span class="required">*</span>
			</label>
			<select name = 'grad' id = 'grad' >
			<?php if(isset($_POST['grad'])){ echo "<option hidden> ".$_POST['grad']." </option>"; } ?>
			<option hidden value = ''>--Odaberi grad--</option>
			</select>
        </p>

        <p>
            <label for="zelje" >🎁 Želje
                <span class="required">*</span>
            </label>
            <textarea id = 'zelje' name = 'zelje' spellcheck='false' placeholder="Dragi Deda Mraze želio/la bih... " ><?php if(isset($_POST['zelje'])){ echo $_POST['zelje']; } ?></textarea>
        </p>
		
		<p><input type="checkbox" name = 'bio_dobar' id="bio_dobar" class='sub'> <label for = 'bio_dobar' >Bio sam <u><b>dobar</b></u> prosle godine.</label></p>
		
        <input type="submit" name = 'pismo' value=" Pošalji 🖅 " />
		
		<a href = './admin.php'><small>Za Deda Mraza🚪</small></a>
    </form>
</div>
	<audio src="./audio/Jingle Bells.ogg" autoplay="autoplay" loop="loop"></audio>
	<script>
	<?php 
		foreach($elem as $val){
			
			echo "document.forms[0]['$val'].classList.add('invalid');";
		}
	?>
	</script>
	<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
	<script type="text/javascript" src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

	<script type="text/javascript" src="js/snowfall.min.js"></script>
	<script type="text/javascript" src="js/app.js"></script>
	
</body>
</html>