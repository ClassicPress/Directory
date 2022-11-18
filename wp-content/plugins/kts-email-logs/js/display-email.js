document.addEventListener("DOMContentLoaded", function() {
	
	const shows = document.querySelectorAll('.email-show');
	for ( let i = 0, n = shows.length; i < n; i++ ) {
		shows[i].addEventListener('click', function(e) {
			e.preventDefault();
			let emailTR = shows[i].closest('tr');

			document.getElementById('modal-1-title').innerHTML = emailTR.querySelector('.subject').innerHTML;
			document.getElementById('modal-1-content').innerHTML = emailTR.querySelector('.message .hidden').innerText;
			//document.getElementById('modal-1-headers').innerHTML = 'Additional Headers: <pre>' + emailTR.headers + '</pre>';
		});
	}
	
	try {
		
		MicroModal.init({
			onClose: modal => {
				document.getElementById('modal-1-title').innerHTML = '';
				document.getElementById('modal-1-content').innerHTML = '';				
				document.getElementById('modal-1-headers').innerHTML = '';
			},
			disableScroll: true,
			awaitOpenAnimation: true
		});
		
	}

	catch(e) {
		console.log('Micromodal error: ', e);
	}
	
});
