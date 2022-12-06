/*
Bedrock JavaScript files
Author: Tim Kaye
*/
document.addEventListener('DOMContentLoaded', function() {
	'use strict'; // satisfy code inspectors

	/* SHOW AND HIDE MENU AND TOGGLE BUTTONS ON MOBILE */
	if (window.matchMedia("screen and (max-width: 899px)").matches) {
		document.getElementById('menu-toggle').addEventListener('click', function() {
			this.setAttribute('hidden', 'hidden');
			document.getElementById('main-menu').style.display = 'block';
			document.getElementById('menu-toggle-close').removeAttribute('hidden');
			document.getElementById('menu-toggle-close').focus();
		}, false);
		document.getElementById('menu-toggle-close').addEventListener('click', function() {
			this.setAttribute('hidden', 'hidden');
			document.getElementById('main-menu').style.display = 'none';
			document.getElementById('menu-toggle').removeAttribute('hidden');
			document.getElementById('menu-toggle').focus();
		}, false);
	}

	/* HIDE SUB-MENUS AND RE-FOCUS WHEN PRESSING ESCAPE KEY */
	const subMenus = document.getElementsByClassName('sub-menu');
	document.addEventListener('keydown', function(e) {
		for (let i = 0, n = subMenus.length; i < n; i++) {
			if (e.key === 'Escape') {
				let size = subMenus[i].getBoundingClientRect();
				if (size.height !== 0) {
					subMenus[i].previousElementSibling.focus();
					subMenus[i].style.display = 'none';
				}
			}
			else if (e.key === 'ArrowDown') {
				e.preventDefault();
				if (subMenus[i].style.display === 'none') {
					subMenus[i].removeAttribute('style');
				}
			}
			else if (e.key === 'Tab') {
				if (subMenus[i].style.display === 'none') {
					setTimeout(function() {
						subMenus[i].removeAttribute('style');
					}, 100);
				}
			}
		}
	}, false);

	/* RELOAD ON RESIZE BEYOND MEDIA QUERY BREAKPOINT */
	var windoe = window;
	var windowWidth = window.innerWidth;

	window.addEventListener('resize', function() {
		if ((windowWidth >= 900 && window.innerWidth < 900) || (windowWidth < 900 && window.innerWidth >= 900)) {
			if (windoe.RT) {
				clearTimeout(windoe.RT);
			}
			windoe.RT = setTimeout(function() {
				this.location.reload(false); /* false to get page from cache */
			}, 100);
		}
	}, false);

	/* NAV SEARCH */
	document.querySelector('.open-search a').addEventListener('click', function(e) {
		e.preventDefault();
		document.getElementById('masthead-inner').style.display = 'none';
		document.getElementById('search-form-wrapper').removeAttribute('style');
		document.getElementById('masthead').style.background = '#fff';
		document.getElementById('ofsearch').focus();
	}, false);

	document.querySelector('.close-search a').addEventListener('click', function() {
		console.log('close');
	}, false);

});
