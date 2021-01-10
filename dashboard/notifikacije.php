<?php
require './konfiguracije.php';
$config = ucitaj_konfiguracije();
$config['notifikacije']['brojNovih'] = 0;
$config['notifikacije']['ids'] = '';
sacuvaj_konfiguracije($config);
?>