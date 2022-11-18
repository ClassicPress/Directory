/*
Bedrock JavaScript files
Author: Tim Kaye
*/
document.addEventListener('DOMContentLoaded', function() {
	'use strict'; // satisfy code inspectors

	/* DEVELOPERS LIST */
	const developers = document.getElementById('developers');
	const letters = document.querySelectorAll('.letter');
	for (let i = 0, n = letters.length; i < n; i++) {
		letters[i].addEventListener('click', function() {
			letters.forEach(letter => {
				letter.setAttribute('aria-selected', 'false');
				letter.setAttribute('tabindex', '-1'); // restore letter from which clicking to non-tabbable state
			});
			letters[i].setAttribute('aria-selected', 'true');
			letters[i].setAttribute('tabindex', '0'); // make receiving letter tabbable

			const developerPanels = document.querySelectorAll('.developer-panel');
			let id = letters[i].id;
			if (id === 'letter-all') {
				for (let i = 0, n = developerPanels.length; i < n; i++) {
					developerPanels[i].removeAttribute('hidden');
				}
			}
			else {
				for (let i = 0, n = developerPanels.length; i < n; i++) {
					developerPanels[i].setAttribute('hidden', 'hidden');
				}
				let panel = document.getElementById(id + '-panel');
				if (panel !== null) {
					panel.removeAttribute('hidden');
				}
			}
		}, false);

		letters[i].addEventListener('keydown', function(e) {
			const all = document.getElementById('letter-all');
			const z = document.getElementById('letter-z');
			let focus = document.querySelector('.letter:focus');

			if (e.key === 'ArrowRight' || e.key === 'ArrowDown') {
				e.preventDefault();
				if (this.id === 'letter-z') {
					all.click();
					all.focus();
				} else {
					focus.nextElementSibling.click();
					focus.nextElementSibling.focus();
				}
			}
			else if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') {
				e.preventDefault();
				if (this.id === 'letter-all') {
					z.click();
					z.focus();
				} else {
					focus.previousElementSibling.click();
					focus.previousElementSibling.focus();
				}
			}

			else if (e.key === 'Home') {
				e.preventDefault();
				all.click();
				all.focus();
			}

			else if (e.key === 'End') {
				e.preventDefault();
				z.click();
				z.focus();
			}
		}, false);
	}

});
