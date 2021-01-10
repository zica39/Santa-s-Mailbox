function prikazi_pismo(e){
	let msg = e.parentElement.parentElement.nextSibling;
	$('#zeljeModal').modal('show');
	$('#zelje').html(msg.innerHTML);	
}

function oznaci_procitano(e){
	let id = e.getAttribute('data-id');
	window.location.search=`id=${id}&akcija=status`	;
}

function obrisi_neprikladno(e){
	let id = e.getAttribute('data-id');
	window.location.search=`id=${id}&akcija=obrisi`	;
}
function poredak_toggle(e){
	let poredak = e.getAttribute('data-poredak');
	
	if(poredak == 'rastuci'){
		e.setAttribute('data-poredak','opadajuci');
		e.firstElementChild.classList.toggle('zmdi-hc-rotate-180');
		
	}else{
		e.setAttribute('data-poredak','rastuci');
		e.firstElementChild.classList.toggle('zmdi-hc-rotate-180');
		
	}
	if($('#sortiranje option:selected').val() != 'sve')
	sortiraj();
}

function sortiraj(){
	let poredak = $('#poredak').get(0).getAttribute('data-poredak');
	let po = $('#sortiranje option:selected').val();
	
	if(po == 'sve')window.location.href =  window.location.href.split("?")[0];
	else window.location.search=`sortiraj=${po}&poredak=${poredak}`	;
	
}

$("#pdf").on("click", function (e) {
	
	html2canvas($('#tabela_zelja')[0], {
		onrendered: function (canvas) {
			var data = canvas.toDataURL();
			var docDefinition = {
				content: [{
					image: data,
					width: 500
				}]
			};
			pdfMake.createPdf(docDefinition).download("Table.pdf");
		}
	});
	
});