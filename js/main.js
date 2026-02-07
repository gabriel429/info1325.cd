/* =====================================
Template Name: 	Mediplus.
Author Name: Naimur Rahman
Website: http://wpthemesgrid.com/
Description: Mediplus - Doctor HTML Template.
Version:	1.1
========================================*/   
/*=======================================
[Start Activation Code]
=========================================
* Sticky Header JS
* Search JS
* Mobile Menu JS
* Hero Slider JS
* Testimonial Slider JS
* Portfolio Slider JS
* Clients Slider JS
* Single Portfolio Slider JS
* Accordion JS
* Nice Select JS
* Date Picker JS
* Counter Up JS
* Checkbox JS
* Right Bar JS
* Video Popup JS
* Wow JS
* Scroll Up JS
* Animate Scroll JS
* Stellar JS
* Google Maps JS
* Preloader JS
=========================================
[End Activation Code]
=========================================*/ 
(function($) {
    "use strict";
     $(document).on('ready', function() {
	
        jQuery(window).on('scroll', function() {
			if ($(this).scrollTop() > 200) {
				$('#header .header-inner').addClass("sticky");
			} else {
				$('#header .header-inner').removeClass("sticky");
			}
		});
		
		/*====================================
			Sticky Header JS
		======================================*/ 
		jQuery(window).on('scroll', function() {
			if ($(this).scrollTop() > 100) {
				$('.header').addClass("sticky");
			} else {
				$('.header').removeClass("sticky");
			}
		});
		
		$('.pro-features .get-pro').on( "click", function(){
			$('.pro-features').toggleClass('active');
		});
		
		/*====================================
			Search JS
		======================================*/ 
		$('.search a').on( "click", function(){
			$('.search-top').toggleClass('active');
		});
		
		/*====================================
			Mobile Menu
		======================================*/ 	
		$('.menu').slicknav({
			prependTo:".mobile-nav",
			duration: 300,
			closeOnClick:true,
		});
		
		/*===============================
			Hero Slider JS
		=================================*/ 
			if (typeof $.fn.owlCarousel === 'function') {
				$(".hero-slider").owlCarousel({
					loop: true,
					autoplay: true,
					smartSpeed: 600,
					autoplayTimeout: 4500,
					autoplayHoverPause: true,
					items: 1,
					nav: true,
					navText: ['<i class="fa fa-angle-left" aria-hidden="true"></i>', '<i class="fa fa-angle-right" aria-hidden="true"></i>'],
					dots: false,
					autoHeight: true,
					responsive: {
						0: { items: 1, autoHeight: true },
						576: { items: 1, autoHeight: true },
						992: { items: 1, autoHeight: true }
					}
				});
			} else {
			console.warn('Owl Carousel not loaded: .hero-slider will remain static.');
		}

		/*===============================
			Testimonial Slider JS
		=================================*/ 
		$('.testimonial-slider').owlCarousel({
			items:3,
			autoplay:true,
			autoplayTimeout:4500,
			smartSpeed:300,
			autoplayHoverPause:true,
			loop:true,
			merge:true,
			nav:false,
			dots:true,
			responsive:{
				1: {
					items:1,
				},
				300: {
					items:1,
				},
				480: {
					items:1,
				},
				768: {
					items:2,
				},
				1170: {
					items:3,
				},
			}
		});
		
		/*===============================
			Portfolio Slider JS
		=================================*/ 
		$('.portfolio-slider').owlCarousel({
			autoplay:true,
			autoplayTimeout:4000,
			margin:15,
			smartSpeed:300,
			autoplayHoverPause:true,
			loop:true,
			nav:true,
			dots:false,
			responsive:{
				300: {
					items:1,
				},
				480: {
					items:2,
				},
				768: {
					items:2,
				},
				1170: {
					items:4,
				},
			}
		});
		/*===============================
			Article image gallery carousel
		===============================*/
		if (typeof $.fn.owlCarousel === 'function') {
			$('.image-gallery-carousel').owlCarousel({
				items:1,
				loop:true,
				margin:0,
				stagePadding:0,
				autoplay:false,
				smartSpeed:600,
				animateIn: 'fadeIn',
				animateOut: 'fadeOut',
				nav:true,
				dots:true,
				navText: ['<i class="fa fa-angle-left"></i>','<i class="fa fa-angle-right"></i>'],
				responsive: { 480: { items:1 }, 768: { items:1 }, 992: { items:1 } }
			});
		} else {
			console.warn('Owl Carousel not loaded: .image-gallery-carousel will remain static.');
		}

/* Magnific Popup for article gallery slides */
(function(){
	'use strict';
	function initGalleryLightbox(){
		if (typeof jQuery === 'undefined' || typeof jQuery.fn.magnificPopup === 'undefined') return;
		jQuery(document).ready(function($){
			$('.image-gallery-carousel').magnificPopup({
				delegate: 'a.gallery-link',
				type: 'image',
				gallery: { enabled: true },
				image: { titleSrc: function(item){ return item.el.attr('title') || ''; } }
			});
		});
	}
	if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initGalleryLightbox);
	else initGalleryLightbox();
})();
		
		/*=====================================
			Counter Up JS
		======================================*/
		$('.counter').counterUp({
			delay:20,
			time:2000
		});
		
		/*===============================
			Clients Slider JS
		=================================*/ 
		if (typeof $.fn.owlCarousel === 'function') {
			$('.clients-slider').owlCarousel({
				items:5,
				autoplay:true,
				autoplayTimeout:3500,
				margin:15,
				smartSpeed: 400,
				autoplayHoverPause:true,
				loop:true,
				nav:false,
				dots:false,
				responsive:{
					300: { items:1 },
					480: { items:2 },
					768: { items:3 },
					1170: { items:5 },
				}
			});

			// Partners slider (horizontal band of logos)
			$('.partners-slider').owlCarousel({
				items:6,
				autoplay:true,
				autoplayTimeout:2500,
				margin:10,
				smartSpeed: 600,
				autoplayHoverPause:true,
				loop:true,
				nav:false,
				dots:false,
				responsive:{
					300: { items:2 },
					480: { items:3 },
					768: { items:4 },
					1170: { items:6 },
				}
			});
		} else {
			console.warn('Owl Carousel not loaded: .clients-slider will remain static.');
		}
		
		/*====================================
			Single Portfolio Slider JS
		======================================*/ 
		$('.pf-details-slider').owlCarousel({
			items:1,
			autoplay:false,
			autoplayTimeout:5000,
			smartSpeed: 400,
			autoplayHoverPause:true,
			loop:true,
			merge:true,
			nav:true,
			dots:false,
			navText: ['<i class="icofont-rounded-left"></i>', '<i class="icofont-rounded-right"></i>'],
		});
		
		/*===================
			Accordion JS
		=====================*/ 
		$('.accordion > li:eq(0) a').addClass('active').next().slideDown();
		$('.accordion a').on('click', function(j) {
			var dropDown = $(this).closest('li').find('p');
			$(this).closest('.accordion').find('p').not(dropDown).slideUp(300);
			if ($(this).hasClass('active')) {
				$(this).removeClass('active');
			} else {
				$(this).closest('.accordion').find('a.active').removeClass('active');
				$(this).addClass('active');
			}
			dropDown.stop(false, true).slideToggle(300);
			j.preventDefault();
		});
		
		/*====================================
			Nice Select JS
		======================================*/ 	
		$('select').niceSelect();
		
		/*=====================================
			Date Picker JS
		======================================*/ 
		$( function() {
			$( "#datepicker" ).datepicker();
		} );
		
		
		
		/*===============================
			Checkbox JS
		=================================*/  
		$('input[type="checkbox"]').change(function(){
			if($(this).is(':checked')){
				$(this).parent("label").addClass("checked");
			} else {
				$(this).parent("label").removeClass("checked");
			}
		});
		
		/*===============================
			Right Bar JS
		=================================*/ 
		$('.right-bar .bar').on( "click", function(){
			$('.sidebar-menu').addClass('active');
		});
		$('.sidebar-menu .cross').on( "click", function(){
			$('.sidebar-menu').removeClass('active');
		});
		
		/*=====================
			Video Popup JS
		=======================*/ 
		$('.video-popup').magnificPopup({
			type: 'video',	
		});
		
		/*================
			Wow JS
		==================*/		
		var window_width = $(window).width();   
			if(window_width > 767){
            new WOW().init();
		}
	
		/*===================
			Scroll Up JS
		=====================*/
		$.scrollUp({
			scrollText: '<span><i class="fa fa-angle-up"></i></span>',
			easingType: 'easeInOutExpo',
			scrollSpeed: 900,
			animation: 'fade'
		}); 

		/*=======================
			Animate Scroll JS
		=========================*/
		$('.scroll').on("click", function (e) {
			var anchor = $(this);
				$('html, body').stop().animate({
					scrollTop: $(anchor.attr('href')).offset().top - 100
				}, 1000);
			e.preventDefault();
		});
		
		/*=======================
			Stellar JS
		=========================*/
		$.stellar({
		  horizontalOffset: 0,
		  verticalOffset: 0
		});

		/*====================
			Google Maps JS
		========================*/
		// Create map only when the target element exists to avoid "No element defined." errors
		if (typeof GMaps !== 'undefined' && $('#map').length) {
			var map = new GMaps({
				el: '#map',
				lat: 23.011245,
				lng: 90.884780,
				scrollwheel: false,
			});
			map.addMarker({
				lat: 23.011245,
				lng: 90.884780,
				title: 'Marker with InfoWindow',
				infoWindow: {
					content: '<p>welcome to Medipro</p>'
				}
			});
		}
	});
	
	/*====================
		Preloader JS
	======================*/
	$(window).on('load', function() {
		$('.preloader').addClass('preloader-deactivate');
	});
	
	
})(jQuery);

// Progressive reveal for 'Charger plus' in actualites sidebar
(function(){
	'use strict';
	function initLoadMore() {
		var btn = document.getElementById('loadMoreNews');
		if (!btn) return;
		var batch = parseInt(btn.dataset.batch || '5', 10);
		btn.addEventListener('click', function(){
			var hidden = document.querySelectorAll('.extra-news.d-none');
			for (var i=0;i<batch && i<hidden.length;i++){
				hidden[i].classList.remove('d-none');
			}
			if (document.querySelectorAll('.extra-news.d-none').length === 0) btn.style.display = 'none';
		});
	}
	if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initLoadMore);
	else initLoadMore();
})();

// Magnific Popup: documentation thumbnails lightbox
(function(){
	'use strict';
	function initDocLightbox(){
		if (typeof jQuery === 'undefined' || typeof jQuery.fn.magnificPopup === 'undefined') return;
		jQuery(document).ready(function($){
			$('.doc-thumb-link').magnificPopup({
				type: 'image',
				gallery: { enabled: true },
				image: { titleSrc: 'title' }
			});
		});
	}
	if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initDocLightbox);
	else initDocLightbox();
})();

// Transforme les sections longues en cartes / aperçus cliquables
(function(){
	'use strict';
	function makeExcerpt(text, wordLimit){
		var words = text.replace(/\s+/g,' ').trim().split(' ');
		if (words.length <= wordLimit) return text.trim();
		return words.slice(0, wordLimit).join(' ') + '…';
	}

	function initPreviewCards(){
		// For document sections marked with data-doc-section
		document.querySelectorAll('[data-doc-section]').forEach(function(section){
			// skip if already transformed
			if (section.dataset.previewAttached) return;
			var titleEl = section.querySelector('.doc-section-title');
			var para = section.querySelector('.doc-paragraph');
			if (!titleEl || !para) return;
			var excerptText = makeExcerpt(para.textContent || '', 40);

			var card = document.createElement('div');
			card.className = 'preview-card doc-preview-card';
			card.innerHTML = '<h3 class="preview-title">' + titleEl.textContent.trim() + '</h3>' +
											 '<p class="preview-excerpt">' + excerptText + '</p>' +
											 '<div class="preview-cta"><button class="btn btn-primary">Lire la suite</button></div>';

			section.parentNode.insertBefore(card, section);
			// hide original content initially
			section.style.display = 'none';
			section.dataset.previewAttached = '1';

			function openSection(){
				card.style.display = 'none';
				section.style.display = '';
				// add a small collapse control if not present
				if (!section.querySelector('.preview-collapse')){
					var cbtn = document.createElement('div');
					cbtn.className = 'preview-collapse text-right';
					cbtn.innerHTML = '<button class="btn btn-outline-primary">Réduire</button>';
					cbtn.addEventListener('click', function(e){ e.preventDefault(); section.style.display = 'none'; card.style.display = ''; window.scrollTo({top: card.getBoundingClientRect().top + window.pageYOffset - 120, behavior:'smooth'}); });
					section.insertBefore(cbtn, section.firstChild);
				}
				// scroll into view
				section.scrollIntoView({behavior:'smooth', block:'start'});
			}

			card.addEventListener('click', openSection);
			var btn = card.querySelector('button');
			if (btn) btn.addEventListener('click', function(e){ e.preventDefault(); openSection(); });
		});

		// For news article excerpts (.news-excerpt) — create inline preview if long
		document.querySelectorAll('.news-card .news-excerpt').forEach(function(excerpt){
			if (excerpt.dataset.previewAttached) return;
			// count words roughly
			var text = excerpt.textContent || '';
			var words = text.trim().split(/\s+/).filter(Boolean);
			if (words.length <= 40) return; // not long
			var excerptText = makeExcerpt(text, 50);
			var card = document.createElement('div');
			card.className = 'preview-card news-preview-card';
			card.innerHTML = '<p class="preview-excerpt">' + excerptText + '</p>' +
											 '<div class="preview-cta"><button class="btn btn-primary">Lire la suite</button></div>';

			excerpt.parentNode.insertBefore(card, excerpt);
			excerpt.style.display = 'none';
			excerpt.dataset.previewAttached = '1';

			var btn = card.querySelector('button');
			btn.addEventListener('click', function(e){
				e.preventDefault();
				card.style.display = 'none';
				excerpt.style.display = '';
				excerpt.scrollIntoView({behavior:'smooth', block:'start'});
			});

			// For generic content blocks often used on portfolio/secretariat pages
			document.querySelectorAll('.body-text').forEach(function(block){
				if (block.dataset.previewAttached) return;
				// build a title from the first heading inside, or fallback
				var titleEl = block.querySelector('h3, h2, h4, h5');
				var titleText = titleEl ? titleEl.textContent.trim() : 'Présentation';
				// find first paragraph or list to build an excerpt
				var para = block.querySelector('p');
				var text = para ? (para.textContent || '') : (block.textContent || '');
				var words = (text || '').trim().split(/\s+/).filter(Boolean);
				if (words.length <= 30) continueBlock();

				var excerptText = makeExcerpt(text, 45);
				var card = document.createElement('div');
				card.className = 'preview-card body-preview-card';
				card.innerHTML = '<h3 class="preview-title">' + titleText + '</h3>' +
												 '<p class="preview-excerpt">' + excerptText + '</p>' +
												 '<div class="preview-cta"><button class="btn btn-primary">Lire la suite</button></div>';

				block.parentNode.insertBefore(card, block);
				block.style.display = 'none';
				block.dataset.previewAttached = '1';

				function openBlock(){
					card.style.display = 'none';
					block.style.display = '';
					if (!block.querySelector('.preview-collapse')){
						var cbtn = document.createElement('div');
						cbtn.className = 'preview-collapse text-right';
						cbtn.innerHTML = '<button class="btn btn-outline-primary">Réduire</button>';
						cbtn.addEventListener('click', function(e){ e.preventDefault(); block.style.display = 'none'; card.style.display = ''; window.scrollTo({top: card.getBoundingClientRect().top + window.pageYOffset - 120, behavior:'smooth'}); });
						block.insertBefore(cbtn, block.firstChild);
					}
					block.scrollIntoView({behavior:'smooth', block:'start'});
				}

				card.addEventListener('click', openBlock);
				var btn = card.querySelector('button');
				if (btn) btn.addEventListener('click', function(e){ e.preventDefault(); openBlock(); });

				function continueBlock(){
					// do nothing if not long enough — leave original block visible
					block.style.display = '';
					block.dataset.previewAttached = '0';
					if (card && card.parentNode) card.parentNode.removeChild(card);
				}
			});
		});
	}

	if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initPreviewCards);
	else initPreviewCards();

})();
