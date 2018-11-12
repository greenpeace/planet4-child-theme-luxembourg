(function() {
    tinymce.PluginManager.add('gplux_cta_mce_button', function(editor, url) {

        var sh_tag = 'cta';

        //helper functions
        function getAttr(s, n) {
            n = new RegExp(n + '=\"([^\"]+)\"', 'g').exec(s);
            return n ?  window.decodeURIComponent(n[1]) : '';
        };

        function html( cls, data ,con) {
            var placeholder = url + '/dist/' + getAttr(data,'type') + '.png';
            data = window.encodeURIComponent( data );
            content = window.encodeURIComponent( con );

            return '<img src="' + placeholder + '" class="mceItem ' + cls + '" ' + 'data-sh-attr="' + data + '" data-sh-content="'+ con+'" data-mce-resize="false" data-mce-placeholder="1" />';
        }

        function replaceShortcodes( content ) {
            return content.replace( /\[cta([^\]]*)\]([^\]]*)\[\/cta\]/g, function( all,attr,con) {
                return html( 'wp_cta', attr , con);
            });
        }

        function restoreShortcodes( content ) {
            //match any image tag with our class and replace it with the shortcode's content and attributes
            return content.replace( /(?:<p(?: [^>]+)?>)*(<img [^>]+>)(?:<\/p>)*/g, function( match, image ) {
                var data = getAttr( image, 'data-sh-attr' );
                var con = getAttr( image, 'data-sh-content' );

                if ( data ) {
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
            event.content = restoreShortcodes(event.content);
        });

        editor.addCommand('cta_popup', function(ui, v) {

            var CTAText = '';
            if (v.CTAText)
                CTAText = v.CTAText;
            var CTALink = '';
            if (v.CTALink)
                CTALink = v.CTALink;
            var CTAType = 'action';
            if (v.CTAType)
                CTAType = v.CTAType;
             var CTAHeight = 'normal';
            if (v.CTAHeight)
                CTAHeight = v.CTAHeight;
            var CTAWidth = 'auto';
            if (v.CTAWidth)
                CTAWidth = v.CTAWidth;
            var CTATarget = '';
            if (v.CTATarget)
                CTATarget = v.CTATarget;

            editor.windowManager.open({
                    title: 'Insert CTA',
                    body: [{
                        type: 'textbox',
                        name: 'CTAText',
                        label: 'Button text',
                        value: CTAText
                    },
                    {
                        type: 'textbox',
                        name: 'CTALink',
                        label: 'Button link',
                        value: CTALink
                    },
                    {
                        type: 'listbox',
                        name: 'CTAType',
                        label: 'CTA Type',
                        value: CTAType,
                        values: [{
                            text: 'Donate',
                            value: 'donate'
                        }, {
                            text: 'Action',
                            value: 'action'
                        }, {
                            text: 'Normal',
                            value: 'normal'
                        }]
                    },
                    {
                        type: 'listbox',
                        name: 'CTAHeight',
                        label: 'CTA height',
                        value: CTAHeight,
                        values: [{
                            text: 'Normal',
                            value: 'btn-medium'
                        }, {
                            text: 'Big',
                            value: 'btn-large'
                        }]
                    },
                    {
                        type: 'listbox',
                        name: 'CTAWidth',
                        label: 'CTA width',
                        value: CTAWidth,
                        values: [{
                            text: 'Auto',
                            value: 'auto'
                        },{
                            text: '100px',
                            value: 'xsmall'
                        }, {
                            text: '150px',
                            value: 'small'
                        }, {
                            text: '200px',
                            value: 'medium'
                        }, {
                            text: '250px',
                            value: 'large'
                        }, {
                            text: '300px',
                            value: 'xlarge'
                        }, {
                            text: '350px',
                            value: 'xxlarge'
                        }, {
                            text: 'Full width',
                            value: 'fullwidth'
                        }]
                    },
                    {
                        type: 'listbox',
                        name: 'CTATarget',
                        label: 'Link Target',
                        value: CTATarget,
                        values: [{
                            text: 'new tab',
                            value: '_blank'
                        }, {
                            text: 'same tab',
                            value: ''
                        }]
                    } ],
                    onsubmit: function(e) {
                        editor.insertContent(
                            '[cta target="' +
                            e.data.CTATarget +
                            '" link="' +
                            e.data.CTALink +
                            '" text="' +
                            e.data.CTAText +
                            '" width="' +
                            e.data.CTAHeight +
                            '" height="' +
                            e.data.CTAWidth +
                            '" type="' +
                            e.data.CTAType +
                            '"]' +
                            '[/cta]'
                        );
                    }
                });

        });
        //open popup on placeholder double click
        editor.on('DblClick',function(e) {
            var cls  = e.target.className.indexOf('wp_cta');
            if ( e.target.nodeName == 'IMG' && e.target.className.indexOf('wp_cta') > -1 ) {
                var title = e.target.attributes['data-sh-attr'].value;
                title = window.decodeURIComponent(title);
                var content = e.target.attributes['data-sh-content'].value;
                editor.execCommand('cta_popup','',{
                    CTAText : getAttr(title,'text'),
                    CTALink : getAttr(title,'link'),
                    CTATarget   : getAttr(title,'target'),
                    CTAHeight   : getAttr(title,'height'),
                    CTAWidth   : getAttr(title,'width'),
                    CTAType   : getAttr(title,'type'),
                    content: content
                });
            }
        });


        editor.addButton('gplux_cta_mce_button', {
            text: 'CTA',
            icon: false,
            onclick: function() {
                editor.execCommand('cta_popup','',{
                    CTAText : '',
                    CTALink : '',
                    CTATarget   : '',
                    CTAHeight   : 'normal',
                    CTAWidth   : 'auto',
                    CTAType   : 'action',
                    content: ''
                });
            }
        });
    });
})();
