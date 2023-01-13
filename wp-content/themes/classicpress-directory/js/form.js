document.addEventListener('DOMContentLoaded', function () {
	'use strict';

	// Toggle 
	const softwares = document.getElementsByName('software_type');
	const plugin = document.getElementById('plugin');
	const category = document.getElementById('category');
	const categories = document.getElementsByName('categories[]');
	const tagsDiv = document.getElementById('tags-div');
	const tags = document.getElementById('tags');
	const max = document.getElementById('max');
	const submitBtn = document.getElementById('submit-btn');
	softwares.forEach(software => {
		software.addEventListener('change', function () {
			console.log('Yes');
			if (plugin.checked) {
				category.removeAttribute('hidden');
				categories.forEach(cat => {
					cat.removeAttribute('disabled');
					cat.setAttribute('required', 'required');

					// Toggle required attribute according to whether checkbox already selected
					cat.addEventListener('change', function () {
						let flag = false;
						for (let i = 0, n = categories.length; i < n; i++) {
							if (categories[i].checked) {
								categories.forEach(cat => {
									cat.removeAttribute('required');
									flag = true;
								});
							}
						}
						if (flag === false) {
							categories.forEach(cat => {
								cat.setAttribute('required', 'required');
							});
						}
					});

				});
			}
			else {
				category.setAttribute('hidden', 'hidden');
				categories.forEach(cat => {
					cat.removeAttribute('required');
					cat.setAttribute('disabled', 'disabled');
				});
			}
			if (theme.checked) {
				tagsDiv.removeAttribute('hidden');
				tags.removeAttribute('disabled');
				tags.setAttribute('required', 'required');
				tags.addEventListener('input', function () {
					let count = 0;
					for (let i = 0, n = tags.value.length; i < n; i++) {
						if (tags.value.charAt(i) === ',') {
							count++;
						}
					}
					if (count > 2) {
						max.removeAttribute('hidden');
						submitBtn.type = 'button'; // prevents form submission
					}
					else {
						max.setAttribute('hidden', 'hidden');
						submitBtn.type = 'submit'; // re-enables form submission
					}
				});
			}
			else {
				tagsDiv.setAttribute('hidden', 'hidden');
				tags.removeAttribute('required');
				tags.setAttribute('disabled', 'disabled');
			}
		});
	});

	// Character counter
	let excerptTxt = document.getElementById('excerpt');
	let characterCounter = document.getElementById('char-count');
	const maxNumOfChars = 150;

	const countCharacters = () => {
		let numOfEnteredChars = excerptTxt.value.length;
		let counter = maxNumOfChars - numOfEnteredChars;
		characterCounter.textContent = counter + '/100';

		if (counter < 0) {
			characterCounter.style.color = 'red';
		} else if (counter < 20) {
			characterCounter.style.color = 'orange';
		} else {
			characterCounter.style.color = 'black';
		}
	};

	excerptTxt.addEventListener('input', countCharacters);

});
