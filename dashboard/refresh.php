<?php
require './konfiguracije.php';
$config = ucitaj_konfiguracije();
echo $config['notifikacije']['brojNovih'];
?>