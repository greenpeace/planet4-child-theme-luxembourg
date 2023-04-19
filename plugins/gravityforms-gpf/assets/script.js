
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
						const value = e.target.value.trim()
						if (value === "") {
							field.classList.remove('field-activated')
							return
						}


						switch (e.target.type) {
							case 'email':
								if ( ! value.match(/^[a-z0-9_\.\+-]*[a-z0-9]@([a-z0-9-]+\.)+[a-z0-9]+$/)) {
									field.classList.add('field-invalid')
								}
							break;

							case 'tel':
								if ( "intlTelInput" in jQuery.fn && ! jQuery(e.target).intlTelInput('isValidNumber')) {
									field.classList.add('field-invalid')
								}
							break;
						}
					})

					input.addEventListener('input', function(e) {
						field.classList.add('field-activated')
						field.classList.remove('field-valid')
						field.classList.remove('field-invalid')
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

