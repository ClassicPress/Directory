document.addEventListener('DOMContentLoaded', function() {
	'use strict'; // satisfy code inspectors
	
	document.getElementById('school').addEventListener('change', function() {
		let schoolID = document.getElementById('school').value;

		fetch(PROFS.rest_url + 'kts/v1/profs/' + schoolID, {
			method: 'GET',
			credentials: 'same-origin',
			headers: {
				'Content-Type': 'application/json',
				'X-WP-Nonce': PROFS.profs_nonce
			}
		})
		.then(response => {
			if (!response.ok) {
				return response.json()
				.then(error => {
					throw new Error(error.message);
				});
			}
			return response.json() // no errors
		})
		.then(data => {
			document.getElementById('professor').innerHTML = data;
		})
		.catch(error => {
			console.log(error.message);
		});
	});
	
});
