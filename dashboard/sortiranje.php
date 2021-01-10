<?php

function sortiraj_niz($tabela,$kljuc,$poredak){
	
	$pomocna_tabela = [];
	$sortirani_niz = [];
	
	foreach($tabela as $vrsta){	
		$pomocna_tabela[$vrsta['id']] = $vrsta[$kljuc];	
	}
	
	
	if($poredak == 'rastuci'){	
		asort($pomocna_tabela);
	}else{
		arsort($pomocna_tabela);
	}
	
	
	foreach($pomocna_tabela as $id => $val){	
		$sortirani_niz[] = vrati_vrstu($tabela,$id);
	}
	
	return $sortirani_niz;
}

function vrati_vrstu($tabela,$id){
	
	foreach($tabela as $vrsta){	
		if($vrsta['id'] == $id)
			return $vrsta;
	}
	
	return null;
}

?>