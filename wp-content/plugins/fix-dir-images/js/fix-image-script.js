document.addEventListener('DOMContentLoaded', function() {
	const images = document.querySelectorAll('img');
	images.forEach(img => {
		img.addEventListener('error', function handleError() {
			// Add the GitHub path to the relative broken one
			img.src = github_data.prefix + img.getAttribute('src');
			// Stop if even that is not working
			this.removeEventListener('error', arguments.callee, false);
		});
	});
});
