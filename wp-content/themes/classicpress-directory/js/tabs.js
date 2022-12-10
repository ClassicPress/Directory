document.addEventListener('DOMContentLoaded', function() {
	'use strict';

	const anchors = document.querySelectorAll('.ui-button');
	const tabs = document.querySelectorAll('.ui-panel');
	const firstTab = document.getElementById('ui-id-1');
	const lastTab = document.getElementById('ui-id-2');

	for (let i = 0, n = anchors.length; i < n; i++) {
		anchors[i].addEventListener('click', function() {
			let activeButton = document.querySelector('.ui-state-active');
			activeButton.setAttribute('aria-selected', 'false');
			activeButton.setAttribute('tabindex', '-1');
			activeButton.classList.remove('ui-state-active');

			anchors[i].setAttribute('aria-selected', 'true');
			anchors[i].classList.add('ui-state-active');
			anchors[i].setAttribute('tabindex', '0');

			for (let j = 0, l = tabs.length; j < l; j++) {
				tabs[j].setAttribute('hidden', 'hidden');
			}

			let panel = anchors[i].getAttribute('aria-controls');
			document.getElementById(panel).removeAttribute('hidden');
		});

		anchors[i].addEventListener('keydown', function(e) {
			if (e.key === 'ArrowRight' || e.key === 'ArrowDown') {
				e.preventDefault();
				if (anchors[i].id === 'ui-id-3') {	
					firstTab.focus();
					firstTab.click();
				} else {
					anchors[i].nextElementSibling.focus();
					anchors[i].nextElementSibling.click();
				}
			}

			else if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') {
				e.preventDefault();
				if (anchors[i].id === 'ui-id-1') {
					lastTab.focus();
					lastTab.click();
				} else {
					anchors[i].previousElementSibling.focus();
					anchors[i].previousElementSibling.click();
				}
			}

			else if (e.key === 'Home') {
				e.preventDefault();
				firstTab.focus();
				firstTab.click();
			}

			else if (e.key === 'End') {
				e.preventDefault();
				lastTab.focus();
				lastTab.click();
			}
		});
	}

});
