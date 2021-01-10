const TIMER_REFRESH = 10000;

$(document).ready(e => { setTimeout(osvezi_notifikacije,TIMER_REFRESH);	});


function osvezi_notifikacije(){
	
	 $.ajax({url: "../dashboard/refresh.php", success: function(result){
		 
		if(parseInt(result)>0){
			popuni_notifikaciju(result);
			old_number = parseInt(result);
			
		}else{
			setTimeout(osvezi_notifikacije,TIMER_REFRESH);
		}
	}});
	
}

function popuni_notifikaciju(n){
	var broj = $('.notifi__title .content');
	
var html = `<div class="notifi__title">
				<p>Imate nove notifikacije</p>
			</div>
			<div onclick = 'prikazi_nove();' class="notifi__item">
				<div class="bg-c1 img-cir img-40">
					<i class="zmdi zmdi-email-open"></i>
				</div>
				<div class="content">
					<p>Dobili ste ${n} nova pisma!</p>
				</div>
			</div>
		   
			<div class="notifi__footer">
				<a onclick = 'ocisti_notifikacje(this)' href="#">Clear notifications</a>
			</div>`
			
		$('.notifi-dropdown').html(html);
		$('.header-button-item:first-child').addClass('has-noti');
		
		if(n != old_number){
				new Audio('../audio/new.mp3').play();
		}
		
		setTimeout(osvezi_notifikacije,TIMER_REFRESH);
}

function prikazi_nove(){
	if(old_number)
	window.location.href = './sve_zelje.php?nove=true';
}

function ocisti_notifikacje(e){
	
	if(old_number>0){
		$('div.has-noti').removeClass('has-noti');
		$('div.notifi__title').remove();
		$('div.notifi__item p').html('Nema novih notifikacija');
		$(e).addClass('disabled');
		
		 $.ajax({url: "../dashboard/notifikacije.php", success: function(result){
			old_number = 0;
		}});
	}
}

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
	if(confirm('Da li ste sigurni?')){
		let id = e.getAttribute('data-id');
		window.location.search=`id=${id}&akcija=obrisi`;
	}
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
	
	sortiraj();
}

function sortiraj(){
	
	let poredak = $('#poredak').get(0).getAttribute('data-poredak');
	let po = $('#sortiranje option:selected').val();
	
	
	var search = location.search.substring(1);
	var obj = {};
	
	if(search)
		obj = JSON.parse('{"' + search.replace(/&/g, '","').replace(/=/g,'":"') + '"}', function(key, value) { return key===""?value:decodeURIComponent(value) })
	
	
	obj.sortiraj = po;
	obj.poredak = poredak;
	
	window.location.search=$.param(obj);
}

function limit(e){
	
	
	let limit = $('#limit option:selected').val();
	
	
	var search = location.search.substring(1);
	var obj = {};
	
	if(search)
		obj = JSON.parse('{"' + search.replace(/&/g, '","').replace(/=/g,'":"') + '"}', function(key, value) { return key===""?value:decodeURIComponent(value) })
	
	
	obj.limit = limit;
	
	window.location.search=$.param(obj);
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

  try {
    //WidgetChart 5
    var ctx = document.getElementById("widgetChart5");
    if (ctx) {
      ctx.height = 220;
      var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: ['Januar', 'Februar', 'Mart', 'April', 'Maj', 'Jun', 'Jul', 'Avgust', 'Septembar', 'Oktobar', 'Novembar', 'Decembar'],
          datasets: [
            {
              label: "Pisma po mjesecima",
              data: mjeseci_niz,
              borderColor: "transparent",
              borderWidth: "0",
              backgroundColor: "#ccc",
            }
          ]
        },
        options: {
          maintainAspectRatio: true,
          legend: {
            display: false
          },
          scales: {
            xAxes: [{
              display: false,
              categoryPercentage: 1,
              barPercentage: 0.65
            }],
            yAxes: [{
              display: false
            }]
          }
        }
      });
    }

  } catch (error) {
    console.log(error);
  }

try {

    // Percent Chart 2
    var ctx = document.getElementById("percent-chart2");
    if (ctx) {
      ctx.height = 209;
      var myChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
          datasets: [
            {
              label: "Pisma po regijama",
              data: [sjever,jug,centar],
              backgroundColor: [
                'red',
                'green',
				'blue'
              ],
              hoverBackgroundColor: [
                'red',
                'green',
				'blue'
              ],
              borderWidth: [
                0, 0, 0
              ],
              hoverBorderColor: [
                'transparent',
                'transparent',
				'transparent'
              ]
            }
          ],
          labels: [
            'Sjever',
            'Centar',
			'Jug'
          ]
        },
        options: {
          maintainAspectRatio: false,
          responsive: true,
          cutoutPercentage: 87,
          animation: {
            animateScale: true,
            animateRotate: true
          },
          legend: {
            display: false,
            position: 'bottom',
            labels: {
              fontSize: 14,
              fontFamily: "Poppins,sans-serif"
            }

          },
          tooltips: {
            titleFontFamily: "Poppins",
            xPadding: 15,
            yPadding: 10,
            caretPadding: 0,
            bodyFontSize: 16,
          }
        }
      });
    }

  } catch (error) {
    console.log(error);
  }
  
/////////////////////////////////////////////////////////////////////////////		


(function ($) {
    // USE STRICT
    "use strict";
    var navbars = ['header', 'aside'];
    var hrefSelector = 'a:not([target="_blank"]):not([href^="#"]):not([class^="chosen-single"])';
    var linkElement = navbars.map(element => element + ' ' + hrefSelector).join(', ');
    $(".animsition").animsition({
      inClass: 'fade-in',
      outClass: 'fade-out',
      inDuration: 900,
      outDuration: 900,
      linkElement: linkElement,
      loading: true,
      loadingParentElement: 'html',
      loadingClass: 'page-loader',
      loadingInner: '<div class="page-loader__spin"></div>',
      timeout: false,
      timeoutCountdown: 5000,
      onLoadEvent: true,
      browser: ['animation-duration', '-webkit-animation-duration'],
      overlay: false,
      overlayClass: 'animsition-overlay-slide',
      overlayParentElement: 'html',
      transition: function (url) {
        window.location.href = url;
      }
    });
  
  
  })(jQuery);

(function ($) {
  // Use Strict
  "use strict";
  try {
    var progressbarSimple = $('.js-progressbar-simple');
    progressbarSimple.each(function () {
      var that = $(this);
      var executed = false;
      $(window).on('load', function () {

        that.waypoint(function () {
          if (!executed) {
            executed = true;
            /*progress bar*/
            that.progressbar({
              update: function (current_percentage, $this) {
                $this.find('.js-value').html(current_percentage + '%');
              }
            });
          }
        }, {
            offset: 'bottom-in-view'
          });

      });
    });
  } catch (err) {
    console.log(err);
  }
})(jQuery);
(function ($) {
  // USE STRICT
  "use strict";

  // Scroll Bar
  try {
    var jscr1 = $('.js-scrollbar1');
    if(jscr1[0]) {
      const ps1 = new PerfectScrollbar('.js-scrollbar1');      
    }

    var jscr2 = $('.js-scrollbar2');
    if (jscr2[0]) {
      const ps2 = new PerfectScrollbar('.js-scrollbar2');

    }

  } catch (error) {
    console.log(error);
  }

})(jQuery);
(function ($) {
  // USE STRICT
  "use strict";

  // Select 2
  try {

    $(".js-select2").each(function () {
      $(this).select2({
        minimumResultsForSearch: 20,
        dropdownParent: $(this).next('.dropDownSelect2')
      });
    });

  } catch (error) {
    console.log(error);
  }


})(jQuery);
(function ($) {
  // USE STRICT
  "use strict";

  // Dropdown 
  try {
    var menu = $('.js-item-menu');
    var sub_menu_is_showed = -1;

    for (var i = 0; i < menu.length; i++) {
      $(menu[i]).on('click', function (e) {
        e.preventDefault();
        $('.js-right-sidebar').removeClass("show-sidebar");        
        if (jQuery.inArray(this, menu) == sub_menu_is_showed) {
          $(this).toggleClass('show-dropdown');
          sub_menu_is_showed = -1;
        }
        else {
          for (var i = 0; i < menu.length; i++) {
            $(menu[i]).removeClass("show-dropdown");
          }
          $(this).toggleClass('show-dropdown');
          sub_menu_is_showed = jQuery.inArray(this, menu);
        }
      });
    }
    $(".js-item-menu, .js-dropdown").click(function (event) {
      event.stopPropagation();
    });

    $("body,html").on("click", function () {
      for (var i = 0; i < menu.length; i++) {
        menu[i].classList.remove("show-dropdown");
      }
      sub_menu_is_showed = -1;
    });

  } catch (error) {
    console.log(error);
  }

  var wW = $(window).width();
    // Right Sidebar
    var right_sidebar = $('.js-right-sidebar');
    var sidebar_btn = $('.js-sidebar-btn');

    sidebar_btn.on('click', function (e) {
      e.preventDefault();
      for (var i = 0; i < menu.length; i++) {
        menu[i].classList.remove("show-dropdown");
      }
      sub_menu_is_showed = -1;
      right_sidebar.toggleClass("show-sidebar");
    });

    $(".js-right-sidebar, .js-sidebar-btn").click(function (event) {
      event.stopPropagation();
    });

    $("body,html").on("click", function () {
      right_sidebar.removeClass("show-sidebar");

    });
 

  // Sublist Sidebar
  try {
    var arrow = $('.js-arrow');
    arrow.each(function () {
      var that = $(this);
      that.on('click', function (e) {
        e.preventDefault();
        that.find(".arrow").toggleClass("up");
        that.toggleClass("open");
        that.parent().find('.js-sub-list').slideToggle("250");
      });
    });

  } catch (error) {
    console.log(error);
  }


  try {
    // Hamburger Menu
    $('.hamburger').on('click', function () {
      $(this).toggleClass('is-active');
      $('.navbar-mobile').slideToggle('500');
    });
    $('.navbar-mobile__list li.has-dropdown > a').on('click', function () {
      var dropdown = $(this).siblings('ul.navbar-mobile__dropdown');
      $(this).toggleClass('active');
      $(dropdown).slideToggle('500');
      return false;
    });
  } catch (error) {
    console.log(error);
  }
})(jQuery);
(function ($) {
  // USE STRICT
  "use strict";

  // Load more
  try {
    var list_load = $('.js-list-load');
    if (list_load[0]) {
      list_load.each(function () {
        var that = $(this);
        that.find('.js-load-item').hide();
        var load_btn = that.find('.js-load-btn');
        load_btn.on('click', function (e) {
          $(this).text("Loading...").delay(1500).queue(function (next) {
            $(this).hide();
            that.find(".js-load-item").fadeToggle("slow", 'swing');
          });
          e.preventDefault();
        });
      })

    }
  } catch (error) {
    console.log(error);
  }

})(jQuery);
(function ($) {
  // USE STRICT
  "use strict";

  try {
    
    $('[data-toggle="tooltip"]').tooltip();

  } catch (error) {
    console.log(error);
  }

  // Chatbox
  try {
    var inbox_wrap = $('.js-inbox');
    var message = $('.au-message__item');
    message.each(function(){
      var that = $(this);

      that.on('click', function(){
        $(this).parent().parent().parent().toggleClass('show-chat-box');
      });
    });
    

  } catch (error) {
    console.log(error);
  }

})(jQuery);