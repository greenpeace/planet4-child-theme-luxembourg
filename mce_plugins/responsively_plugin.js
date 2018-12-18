(function() {
    tinymce.PluginManager.add('gplux_responsively_mce_button', function(editor, url) {

        var sh_tag = 'responsively';

        //helper functions
        function getAttr(s, n) {
            n = new RegExp(n + '=\"([^\"]+)\"', 'g').exec(s);
            return n ?  window.decodeURIComponent(n[1]) : '';
        };

        function html( cls, data ,con) {
            var placeholder = url + '/iframe.png';
            console.log(placeholder);
            data = window.encodeURIComponent( data );
            content = window.encodeURIComponent( con );

            return '<p><img src="' + placeholder + '" class="mceItem ' + cls + '" ' + 'data-sh-type="iframe" data-sh-attr="' + data + '"  data-sh-content="'+ con+'" data-mce-resize="false" data-mce-placeholder="1" /></p>';
        }

        function replaceShortcodes( content ) {
            console.log('replaceShortcodes');
            return content.replace( /\[responsively([^\]]*)\]([^\]]*)\[\/responsively\]/g, function( all,attr,con) {
                return html( 'wp_responsively', attr , con);
            });
        }

        function restoreShortcodesRes( content ) {
            console.log('restoreShortcodesREs');
            //match any image tag with our class and replace it with the shortcode's content and attributes
            return content.replace( /(?:<p(?: [^>]+)?>)*(<img [^>]+>)(?:<\/p>)*/g, function( match, image ) {
                var data = getAttr( image, 'data-sh-attr' );
                var con = getAttr( image, 'data-sh-content' );
                var type = getAttr( image, 'data-sh-type' );

                if ( data && type == "iframe") {
                    return '[' + sh_tag + data + ']' + con + '[/'+sh_tag+']';
                }
                return match;
            });
        }


        //replace from shortcode to an image placeholder
        editor.on('BeforeSetcontent', function(event){
            event.content = replaceShortcodes( event.content );
        });

        //replace from image placeholder to shortcode
        editor.on('GetContent', function(event){
            event.content = restoreShortcodesRes(event.content);
        });

        editor.addCommand('responsively_popup', function(ui, v) {

            var RESSrc = '';
            if (v.RESSrc)
                RESSrc = v.RESSrc;


            editor.windowManager.open({
                    title: 'Insert Iframe',
                    body: [{
                        type: 'textbox',
                        name: 'RESSrc',
                        label: 'Iframe source',
                        value: RESSrc
                    }
                    ],
                    onsubmit: function(e) {
                        var src_url = e.data.RESSrc.replace("http:", "https:");
                        src_url  = src_url .replace("watch?v=", "embed/");
                        src_url = src_url.replace("youtu.be", "www.youtube.com\/embed");
                        src_url = src_url.replace("vimeo.com", "player.vimeo.com\/video");
                        editor.insertContent(
                            '[responsively src="' +
                            src_url +
                            '"]' +
                            '[/responsively]'
                        );
                    }
                });

        });
        //open popup on placeholder double click
        editor.on('DblClick',function(e) {
            var cls  = e.target.className.indexOf('wp_responsively');
            if ( e.target.nodeName == 'IMG' && e.target.className.indexOf('wp_responsively') > -1 ) {
                var title = e.target.attributes['data-sh-attr'].value;
                title = window.decodeURIComponent(title);
                var content = e.target.attributes['data-sh-content'].value;
                editor.execCommand('responsively_popup','',{
                    RESSrc : getAttr(title,'src'),
                    content: content
                });
            }
        });


        editor.addButton('gplux_responsively_mce_button', {
            text: 'IFRAME',
            icon: false,
            onclick: function() {
                editor.execCommand('responsively_popup','',{
                    RESSrc : '',
                    content: ''
                });
            }
        });
    });
})();
