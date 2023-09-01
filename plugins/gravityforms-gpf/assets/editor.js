

tinymce.PluginManager.add( 'gpfgf', function( editor ) {

	var formCache = {}



    function getTitle(id) {

		$.post(gpfgf.getForm,
			{
				id: id
			},
			function(data, status) {
				if (status !== 'success') {
					formCache[id] = '<i>formulaire introuvable</i>'
					return
				}

				if ( ! data.success) {
					formCache[id] = '<i>formulaire introuvable</i>'
					return
				}

				if ( ! data.form.title) {
					formCache[id] = '<i>formulaire sans nom</i>'
					return
				}


				formCache[id] = data.form.title
				updateShortcode(id, data.form.title);
			},
			'json');
    }


    function updateShortcode(id, title) {
        for (var i in tinymce.editors) {
			$(tinymce.editors[i].getBody()).find('[data-gf-rel="' + id + '"]').html(title);
        }
    }


    window.wp.mce.views.register( 'gravityform', {
        edit: function(text, update) {
            var id = this.shortcode.attrs.named.id;
			window.open('/wp-admin/admin.php?page=gf_edit_forms&id=' + id)
        },

        initialize: function() {
            this.old_render = this.render;

            this.render = function(content, force) {
                this.old_render.apply(this, arguments);
                this.onRender();
            };


            var id = this.shortcode.attrs.named.id;

            this.render('<div data-gf-id="'+id+'" class="btn-mce" style="background-color: #00f">'
                      + '<span>Gravity Form '+id+' : </span>'
                      +'<span data-gf-rel="'+id+'"></span></span>'
                      + '</div>');
				},

        onRender: function() {

			var id = this.shortcode.attrs.named.id


			if (typeof formCache[id] === "undefined") {
				formCache[id] = ""
				getTitle(id)
				return
			}

			updateShortcode(id, formCache[id])

        }

    } );








});

