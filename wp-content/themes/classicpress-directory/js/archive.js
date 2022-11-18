document.addEventListener('DOMContentLoaded', function() {
	'use strict';
	
	const infoButtons = document.getElementsByClassName('info-button');
	const modal = document.getElementById('my-dialog');
	const title = document.getElementById('my-dialog-title');
	const closeButtons = modal.querySelectorAll('button');
	const topClose = document.getElementById('top-close');
	const bottomClose = document.getElementById('bottom-close');
	for (let i = 0, n = infoButtons.length; i < n; i++) {
		infoButtons[i].addEventListener('click', function() {
			let infoTrigger = this;
			let info = document.getElementById(infoButtons[i].dataset.info);
			let article = info.closest('article');
			let heading = article.querySelector( 'header a');
			let modalValue = modal.getAttribute('aria-hidden');
			let content = document.getElementById('my-dialog-description');
			if (modalValue === 'true') {
				modal.setAttribute('aria-hidden', 'false');
				title.innerText = heading.innerText;
				content.innerHTML = info.innerHTML;
				setTimeout(function() {
					topClose.focus();
				}, 100);
			}
			else {
				modal.setAttribute('aria-hidden', 'true');	
				title.innerText = '';		
				content.innerHTML = '';
				infoButtons[i].focus();
			}

			for (let i = 0, n = closeButtons.length; i < n; i++) {
				closeButtons[i].addEventListener('click', function() {
					modal.setAttribute('aria-hidden', 'true');			
					content.innerHTML = '';
					infoTrigger.focus();
				}, false);

				closeButtons[i].addEventListener('keydown', function(e) {
					if (e.key === 'Tab') {
						e.preventDefault();
						if (this === topClose ) {
							bottomClose.focus();
						}
						else if (this === bottomClose ) {
							topClose.focus();
						}
					}	
				}, false);

				document.querySelector('.dialog-overlay').addEventListener('click', function() {
					modal.setAttribute('aria-hidden', 'true');			
					content.innerHTML = '';
					infoTrigger.focus();
				}, false);

				document.addEventListener('keyup', function(e) {
					if (e.key === 'Escape') {
						modal.setAttribute('aria-hidden', 'true');			
						content.innerHTML = '';
						infoTrigger.focus();
					}
				}, false);
			}
		});
	}

});
