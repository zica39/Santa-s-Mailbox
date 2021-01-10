<?php
function pretrazi_niz($tabela,$upit){
	
	$nova_tabela = [];
	
	foreach($tabela as $vrsta){
		
		foreach($vrsta as $polje => $val){
			if($polje == 'id' or $polje == 'datum')continue;
			
				if (str_contains(strtolower($val), strtolower($upit))) { 
					$nova_tabela[] = $vrsta;
					break;
				}				
							
		}
			
		
	}
	
	return $nova_tabela;
}


?>