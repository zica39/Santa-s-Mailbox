const audioPath = './audio/';
const audios = ['We Wish You A Merry Christmas.ogg', 'Jingle Bells.ogg'];

//var pozadinska_muzika = new Audio(audioPath + random_sound());
const gradoviCG = 'Podgorica,Nikšić,Pljevlja,Bijelo Polje,Cetinje,Bar,Herceg Novi,Berane,Budva,Ulcinj,Tivat,Rožaje,Kotor,Danilovgrad,Mojkovac,Plav,Kolašin,Žabljak,Plužine,Andrijevica,Šavnik';


$(document).ready(() => {

    pokreni_snijeg_u_pozadini();
    //pokreni_pozadinsku_muziku();
    popuni_gradove_select();
    //postavi_primarni_mehanizam_zastite();

});

function popuni_gradove_select() {

    gradoviCG.split(',').forEach(e => {
        $('#grad').append(`<option>${e}</option>`);

    });


}

function pokreni_pozadinsku_muziku() {

    document.body.onclick = function(e) {
        pozadinska_muzika.play();
        this.onmousemove = null;
    }

}

function pokreni_snijeg_u_pozadini() {
    snowFall.snow(document.body, {
        round: true,
        minSize: 5,
        maxSize: 8
    });
}

function zaustavi_snijeg() {
    snowFall.snow(document.body, "clear");
}

function random_sound() {
    var rnd = Math.floor(Math.random() * 3);
    return audios[rnd];

}

function postavi_primarni_mehanizam_zastite() {
    document.getElementsByName('ime')[0].setAttribute('required', 'required');
    document.getElementsByName('prezime')[0].setAttribute('required', 'required');
    document.getElementsByName('grad')[0].setAttribute('required', 'required');
    document.getElementsByName('zelja')[0].setAttribute('required', 'required');
    document.getElementsByName('bio_dobar')[0].setAttribute('required', 'required');
}

$('input').on('input', function(e) {
    event.target.classList.remove('invalid');
});

$('textarea').on('input', function(e) {
    event.target.classList.remove('invalid');
});

$('select').on('change', function(e) {
    event.target.classList.remove('invalid');
});

function trimAll(){
	document.getElementsByName('ime')[0].value = document.getElementsByName('ime')[0].value.trim();
	document.getElementsByName('prezime')[0].value = document.getElementsByName('prezime')[0].value.trim();
}