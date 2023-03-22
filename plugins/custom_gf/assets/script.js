
(function() {

	jQuery(document).on('gform_post_render', function(event, form_id, current_page) {

		const form = document.getElementById('gform_' + form_id);

		var gFields = form.querySelectorAll('.gfield')

		if (gFields) {

			gFields.forEach( function(field) {


				var input = field.querySelector('input[type="text"], input[type="email"], input[type="tel"], input[type="number"], textarea')
				var label = field.querySelector('.gfield_label')

				if (input) {


					if (input.value !== "") {
						field.classList.add('field-activated')
					}

					input.addEventListener('focus', function(e) {
						field.classList.add('field-activated')
					})


					input.addEventListener('blur', function(e) {
						if (e.target.value.trim() === "") {
							field.classList.remove('field-activated')
						}
					})

					input.addEventListener('input', function(e) {
						field.classList.add('field-activated')
					})


					input.addEventListener("countrychange", function() {
						label.style.paddingLeft = input.style.paddingLeft
					});

					if (input.getAttribute('type') === 'tel') {
						setTimeout(function() {
							label.style.paddingLeft = input.style.paddingLeft
						})

					}
				}
			})
		}




	})

})()

