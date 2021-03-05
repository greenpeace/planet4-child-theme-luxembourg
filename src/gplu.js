;(function($, window, document, location, gp_data, undefined) {
    var $window = $(window),
        match,
        pl     = /\+/g,
        search = /([^&=]+)=?([^&]*)/g,
        decode = function (s) { return decodeURIComponent(s.replace(pl, " ")); },
        query  = window.location.search.substring(1),
        urlParams = {},
        querying = false,
        loaded = false,
        form_var = {},
        form_id,
        confirm_text = 'success',
        fail_text = 'failed',
        maskPatterns = {
            date: {
                J: {pattern: /[0-3]/},
                M: {pattern: /[01]/},
                A: {pattern: /[12]/},
                B: {pattern: /[90]/}
            },
            phone: {
                F: {pattern: /0/},
                M: {pattern: /[67]/}
            },
            iban: {
                Z: {
                    pattern: /[0-9a-zA-Z]/,
                    optional: true
                }
            },
            bic: {
                Z: {
                    pattern: /[0-9a-zA-Z]/,
                    optional: true
                }
            }
        };
/*
        enform_options = {
            mandatory: {
                email: 'Votre adresse e-mail est obligatoire'
            },
            validation: {
                email: {
                    regex: /^[A-Za-z0-9_\.\+-]+[^\.]@([A-Za-z0-9-]+\.)+[a-z0-9]+?$/,
                    message: 'Adresse e-mail invalide'
                },
                phone_number: {
                    regex: /^0[1-9][0-9 ]{8,12}$/,
                    message: "Merci d'indiquer un numéro de téléphone en France, à 10 chiffres"
                }
            },
            error: function(msg) {
                if ("object" != typeof msg)
                    msg = [msg];
                displayMessage('<div><strong>Une erreur est survenue :</strong></div><div><p>'+msg.join('</p><p>')+'</p></div>');
            }

        };

*/

    while (match = search.exec(query))
        urlParams[decode(match[1])] = decode(match[2]);

    function maskFields( $context ) {
        $context.find('[data-mask]').each(function() {
            var $this = $(this),
                parse = JSON.parse,
                conf = {},
                translation = $this.data('mask-translation'),
                has_conf = false;

            if (translation && maskPatterns[translation]) {
                conf.translation = maskPatterns[translation];
                has_conf = true;
            }


            var m = $this.data('mask')
            args = [];

            if ('string' === typeof m) {
                args = [m];
            }
            else if ('object' === typeof m) {
                $this.unmask();

                args = [$this.attr('data-mask')];
            }

            if (has_conf)
                args.push(conf);

            if (args.length){

                $this.mask.apply(this, args);


            }

        });
    }


    function unbindButton(e) {
        e.preventDefault();
    }



    function init($context) {
        if ($context.length == 0)
            return;


        var defer_form = $.Deferred();
        var defer_confirm = $.Deferred();



        $context.data('defer_form', defer_form);
        $context.data('defer_confirm', defer_confirm);


        /*
         * Interception des résolutions des différents traitement (formulaire, EN)
         */
        $.when(defer_form)
         .done(function(form) {
             sendToWebsite($context, form);
         })
         .fail(function() {
             defer_confirm.reject(fail_text);
         });


        /*
         * Envoi vers le site Ok, on affiche le message de confirmation
         */
        $.when(defer_confirm)
         .done(function(msg) {
             $context[0].reset();
             $context.find('.form-error').remove();
             $context.find('input.valid').removeClass('valid');
             $context.find('input.error').removeClass('error');
             $context.trigger('gp.done_form');

             displayMessage(msg);

             var dataLayer = window.dataLayer||[];
             dataLayer.push({
                 'FormId':form_id,
                 'event':'ContactFormComplete'
             });


         })
         .fail(function(err) {
             $context[0].reset();
             $context.trigger('gp.done_form');
             displayMessage(err);
         });





        /*
         * validation du form
         */
        $context.validate({
            focusInvalid: false,
            errorClass: "error",
            validClass: "valid",
            submitHandler:function(form, e) {
                onSubmit($context);
            },
            invalidHandler: function (form, validator) {
                if (!validator.numberOfInvalids())
                    return;

                var html = '<div class="form-error">Le formulaire est incomplet, merci de le corriger.</div>',
                    $form = $(form.target),
                    $title = $form.find('h1,h2,h3,h4,h5,h6').first();

                $form.find('.form-error').remove();

                if ($title.length) {
                    $title.after(html);
                }
                else {
                    $form.prepend(html);
                }

//              alert(msg);
            },
            success: "valid"
        });


        /*
        $context.find('select')
                .select2({
                    minimumResultsForSearch: Infinity
                });
        */

        $context.each(function() {
            var $this = $(this),
                id = $this.attr('id').split('-')[1],
                enurly = $this.data('enurly'),
                enurln = $this.data('enurln');

            //$this.data('defer_en').resolve();

            if (window.init_form[id])
                window.init_form[id]($this);
        });

        $context
            .on('gp.submit_form', function() {
                var $this = $(this),
                    $submit = $this.find('button[type="submit"]'),
                    loading = $submit.data('loading');

                if (loading) {
                    $submit.data('original_value',  $submit.text());
                    $submit.text(loading);
                    $submit.on('click', unbindButton);
                }

            })
            .on('gp.done_form', function() {
                var $this = $(this),
                    $submit = $this.find('button[type="submit"]'),
                    original_value = $submit.data('original_value');

                if (original_value) {
                    $submit.data('original_value', null);
                    $submit.text(original_value);
                    $submit.off('click', unbindButton);
                }

            });

    }







    function displayMessage(msg) {
        $('#main-form').fadeOut();
        if(msg == 'success')
            $('#on-form-submit-success').fadeIn();
        else
            $('#on-form-submit-failed').fadeIn();
    }





    function sendToWebsite($context, form) {
        form.action = "form_submit";


        var post = $.ajax({
            data: form,
            dataType: 'jsonp',
            method: 'POST',
            url: adminAjaxUrl
        })
                    .done(function(data, status) {
                        if (data.success) {
                            $context.data('defer_confirm').resolve(confirm_text);
                        }
                        else{
                            $context.data('defer_confirm').reject(confirm_text);
                        }
                    })
                    .fail(function(a, b, c) {
                        $context.data('defer_confirm').reject(fail_text);
                    });



    }



    function onSubmit($form) {
        // on offre la possibilité de mettre à jour le formulaire
        $form.trigger('gp.pre_submit_form');

        var serialized = $form.serializeArray();

        for (var i = 0, l = serialized.length; i < l; i++) {
            var j = serialized[i],
                key = j.name,
                value = j.value;

            switch (key) {
                case 'phone':
                    value = value.replace(/ /g, '');
                    break;
            }

            form_var[key] = $.trim(value);
        }

        $form.trigger('gp.submit_form');

        $window.trigger('gp.submit_form');

        // si tout est bon dans le formulaire, on resolve pour passer à l'envoi vers le site.
            $form.data('defer_form').resolve(form_var);
    }




    /*
     * Quelques règles maison pour le formulaire
     */
    $.validator.addMethod('phone', function (value, element) {
        var matched = false;

        value = $.trim(value);

        if (value === '') {
            matched = true;
        }
        else {
            value = value.replace(/[^0-9]/g, '');
            value = value.replace(/^33/, '');

            value = parseInt(value);

            if (value > 100000000 && value < 999999999) {
                matched = true;
            }
        }

        return this.optional(element) || matched;
    });



    $.validator.addMethod('email', function (value, element) {
        var match = value.match(/^(.*<)?([A-Za-z0-9_\.\+-]+[^\.]@([A-Za-z0-9-]+\.)+[a-z0-9]+)>?$/),
            matched;

        if (!match || !match[2] || match[2].length == 0) {
            matched = false;
        }
        else {
            $(element).val(match[2]);
            matched = true;
        }

        return this.optional(element) || matched;
    });



    $.validator.addMethod('date', function (value, element) {
        value = $.trim(value);
        return this.optional(element) || /^[0-3][0-9]\/[0-1][0-9]\/[12][90][0-9][0-9]$/.test(value);
    });


    $.validator.addMethod( "iban", function( value, element ) {

        // Some quick simple tests to prevent needless work
        if ( this.optional( element ) ) {
            return true;
        }

        // Remove spaces and to upper case
        var iban = value.replace( / /g, "" ).toUpperCase(),
            ibancheckdigits = "",
            leadingZeroes = true,
            cRest = "",
            cOperator = "",
            countrycode, ibancheck, charAt, cChar, bbanpattern, bbancountrypatterns, ibanregexp, i, p;

        // Check for IBAN code length.
            // It contains:
                        // country code ISO 3166-1 - two letters,
            // two check digits,
            // Basic Bank Account Number (BBAN) - up to 30 chars
        var minimalIBANlength = 5;
        if ( iban.length < minimalIBANlength ) {
            return false;
        }

        // Check the country code and find the country specific format
        countrycode = iban.substring( 0, 2 );
        bbancountrypatterns = {
            "AL": "\\d{8}[\\dA-Z]{16}",
            "AD": "\\d{8}[\\dA-Z]{12}",
            "AT": "\\d{16}",
            "AZ": "[\\dA-Z]{4}\\d{20}",
            "BE": "\\d{12}",
            "BH": "[A-Z]{4}[\\dA-Z]{14}",
            "BA": "\\d{16}",
            "BR": "\\d{23}[A-Z][\\dA-Z]",
            "BG": "[A-Z]{4}\\d{6}[\\dA-Z]{8}",
            "CR": "\\d{17}",
            "HR": "\\d{17}",
            "CY": "\\d{8}[\\dA-Z]{16}",
            "CZ": "\\d{20}",
            "DK": "\\d{14}",
            "DO": "[A-Z]{4}\\d{20}",
            "EE": "\\d{16}",
            "FO": "\\d{14}",
            "FI": "\\d{14}",
            "FR": "\\d{10}[\\dA-Z]{11}\\d{2}",
            "GE": "[\\dA-Z]{2}\\d{16}",
            "DE": "\\d{18}",
            "GI": "[A-Z]{4}[\\dA-Z]{15}",
            "GR": "\\d{7}[\\dA-Z]{16}",
            "GL": "\\d{14}",
            "GT": "[\\dA-Z]{4}[\\dA-Z]{20}",
            "HU": "\\d{24}",
            "IS": "\\d{22}",
            "IE": "[\\dA-Z]{4}\\d{14}",
            "IL": "\\d{19}",
            "IT": "[A-Z]\\d{10}[\\dA-Z]{12}",
            "KZ": "\\d{3}[\\dA-Z]{13}",
            "KW": "[A-Z]{4}[\\dA-Z]{22}",
            "LV": "[A-Z]{4}[\\dA-Z]{13}",
            "LB": "\\d{4}[\\dA-Z]{20}",
            "LI": "\\d{5}[\\dA-Z]{12}",
            "LT": "\\d{16}",
            "LU": "\\d{3}[\\dA-Z]{13}",
            "MK": "\\d{3}[\\dA-Z]{10}\\d{2}",
            "MT": "[A-Z]{4}\\d{5}[\\dA-Z]{18}",
            "MR": "\\d{23}",
            "MU": "[A-Z]{4}\\d{19}[A-Z]{3}",
            "MC": "\\d{10}[\\dA-Z]{11}\\d{2}",
            "MD": "[\\dA-Z]{2}\\d{18}",
            "ME": "\\d{18}",
            "NL": "[A-Z]{4}\\d{10}",
            "NO": "\\d{11}",
            "PK": "[\\dA-Z]{4}\\d{16}",
            "PS": "[\\dA-Z]{4}\\d{21}",
            "PL": "\\d{24}",
            "PT": "\\d{21}",
            "RO": "[A-Z]{4}[\\dA-Z]{16}",
            "SM": "[A-Z]\\d{10}[\\dA-Z]{12}",
            "SA": "\\d{2}[\\dA-Z]{18}",
            "RS": "\\d{18}",
            "SK": "\\d{20}",
            "SI": "\\d{15}",
            "ES": "\\d{20}",
            "SE": "\\d{20}",
            "CH": "\\d{5}[\\dA-Z]{12}",
            "TN": "\\d{20}",
            "TR": "\\d{5}[\\dA-Z]{17}",
            "AE": "\\d{3}\\d{16}",
            "GB": "[A-Z]{4}\\d{14}",
            "VG": "[\\dA-Z]{4}\\d{16}"
        };

        bbanpattern = bbancountrypatterns[ countrycode ];

        // As new countries will start using IBAN in the
        // future, we only check if the countrycode is known.
            // This prevents false negatives, while almost all
        // false positives introduced by this, will be caught
        // by the checksum validation below anyway.
            // Strict checking should return FALSE for unknown
        // countries.
            if ( typeof bbanpattern !== "undefined" ) {
                ibanregexp = new RegExp( "^[A-Z]{2}\\d{2}" + bbanpattern + "$", "" );
                if ( !( ibanregexp.test( iban ) ) ) {
                    return false; // Invalid country specific format
                }
            }

        // Now check the checksum, first convert to digits
        ibancheck = iban.substring( 4, iban.length ) + iban.substring( 0, 4 );
        for ( i = 0; i < ibancheck.length; i++ ) {
            charAt = ibancheck.charAt( i );
            if ( charAt !== "0" ) {
                leadingZeroes = false;
            }
            if ( !leadingZeroes ) {
                ibancheckdigits += "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ".indexOf( charAt );
            }
        }

        // Calculate the result of: ibancheckdigits % 97
        for ( p = 0; p < ibancheckdigits.length; p++ ) {
            cChar = ibancheckdigits.charAt( p );
            cOperator = "" + cRest + "" + cChar;
            cRest = cOperator % 97;
        }
        return cRest === 1;
    }, "Votre IBAN est invalide" );



    $(document).ready(function() {

        maskFields( $('body') );
        init( $('.gp-form') );


        // on se colle sur tous les cas possibles
/*
        // les formulaire uniquement EN dans les pages
        $('form.en-form').each(function() {
            var $this = $(this),
                success_msg = $this.data('success-msg'),
                id = $this.attr('id');

            enform_options.success = (function(id, msg, dataLayer) {
                return function() {
                    displayMessage(msg);
                    dataLayer.push({
                        'FormId':id,
                        'event':'FormComplete'
                    });
                };
            })(id, success_msg||'Merci !', window.dataLayer||[]);


            $this.enform(enform_options);

        });
*/
        if (adminAjaxUrl) {

            // les boutons qui ouvrent un CPT Form
            $(this).on('click', '[data-action="contact_form"]', function(e) {
                e.preventDefault();
                e.stopPropagation();

                var $this = $(e.target),
                    data;

                var id = $this.data('form');

                data = {
                    form: id,
                    action: 'contact_form'
                };


                if (querying)
                    return;

                querying = true;

                if (urlParams.sfu && 1 === parseInt(urlParams.sfu)) {
                    data.sfu = 1;
                }

                // récupération des données du formulaire
                $.get(adminAjaxUrl,
                      data,
                      function(data, status) {
                          querying = false;

                          var d = '<div class="contacts-form"><form><strong>Erreur.</strong> Le formulaire n\'a pas pu être chargé.</form></div>';

                          if ('success' == status && data.form) {
                              d = $(data.form);
                              var $f = d.find('form');
                              maskFields( $f );
                              init($f);
                              form_id = $f.find('#form_id').val();
                          }
                          else {
                              form_id = id;
                          }

                          loaded = true;

                          displayForm(d);
                      },
                      'jsonp');
            });

            if (typeof urlParams.contact != "undefined") {
                $('[data-action="contact_form"]').trigger('click');
            }
        }
    });


})(jQuery, window, document, location, window.gp_data);
;(function(window, undefined) {

    var $description,
        $adherent_oui,
        $adherent_non,
        $nom,
        $prenom,
        $email,
        $phone,
        $old_email,
        $new_email,

        $old_adresse,
        $new_adresse,

        $old_phone,
        $new_phone,

        $old_phone_mobile,
        $new_phone_mobile,

        $numadherent,

        $message,
        //          $sujet,
        $beneficiaire,
        $bic,
        $iban,
        $type,
        $subtype,
        $reason,
        $name,
        $inputs = $(),
        $part_adherent = $(),
        $part_iban = $(),
        $names = $(),
        $submit =$(),
        $part_message,
        $parts;




    function updateDescription() {
        var content = [
            'Prénom : ' + $prenom.val(),
            'Nom : ' + $nom.val(),
            '',
            'Adhérent : ' + ($adherent_oui.is(':checked') ? 'oui, n° ' + $numadherent.val() : 'non'),
            ''
        ];

        if ('' != $email.val()) {
            content.push(
                'E-mail : ',
                $email.val(),
                '');
        }

        if ('' != $phone.val()) {
            content.push(
                'Téléphone : ',
                $phone.val(),
                '');
        }

        if ('Changement de coordonnées personnelles' == $subtype.val()) {
            content.push('Changement de coordonnées', '');

            if ('' != $old_adresse.val()) {
                content.push(
                    'Ancienne adresse postale : ',
                    $old_adresse.val(),
                    '');
            }

            if ('' != $new_adresse.val()) {
                content.push(
                    'Nouvelle adresse postale : ',
                    $new_adresse.val(),
                    '');
            }

            if ('' != $old_email.val()) {
                content.push(
                    'Ancien e-mail :',
                    $old_email.val(),
                    '');
            }

            if ('' != $new_email.val()) {
                content.push(
                    'Nouvel e-mail :',
                    $new_email.val(),
                    '');
            }

            if ('' != $old_phone.val()) {
                content.push(
                    'Ancien téléphone :',
                    $old_phone.val(),
                    '');
            }

            if ('' != $new_phone.val()) {
                content.push(
                    'Nouveau téléphone :',
                    $new_phone.val(),
                    '');
            }

            if ('' != $old_phone_mobile.val()) {
                content.push(
                    'Ancien téléphone mobile :',
                    $old_phone_mobile.val(),
                    '');
            }

            if ('' != $new_phone_mobile.val()) {
                content.push(
                    'Nouveau téléphone mobile :',
                    $new_phone_mobile.val(),
                    '');
            }

        }


        if ('Changement de coordonnées bancaires' == $subtype.val()) {
            content.push(
                '*Modification de coordonnées bancaires* :',
                'Bénéficiaire : ',
                $beneficiaire.val(),
                'IBAN : ',
                $iban.val(),
                'BIC : ',
                $bic.val(),
                ''
            );
        }

        if ($message.val() != "") {
            content.push('', 'Message : ', $message.val(), '');
        }

        $description.val(content.join("\n"));
    }



    function init_form_contact($form) {
        $('#on-form-submit-success').hide();
        $('#on-form-submit-failed').hide();
        $submit = $submit.add($form.find('#form-submit'));
        $submit = $submit.add($form.find('#nl-part'));

        $part_message = $form.find('#message-part');

        $description = $form.find('#description');

        $adherent_oui = $form.find('#adherent-oui');
        $adherent_non = $form.find('#adherent-non');

        $nom = $form.find('#00N7E000000nl0Q');
        $prenom = $form.find('#00N7E000000nl0L');
        $email = $form.find('#email');
        $phone = $form.find('#phone');

        $old_email = $form.find('#old_email');
        $old_phone = $form.find('#old_phone');
        $new_phone = $form.find('#new_phone');
        $new_email = $form.find('#new_email');

        $old_phone_mobile = $form.find('#old_phone_mobile');
        $new_phone_mobile = $form.find('#new_phone_mobile');

        $old_adresse = $form.find('#old_adresse');
        $new_adresse = $form.find('#new_adresse');

        $numadherent = $form.find('#numadherent');

        $message = $form.find('#message');

        $beneficiaire = $form.find('#beneficiaire');
        $bic = $form.find('#bic');
        $iban = $form.find('#iban');

        $type = $form.find('#type');
        $subtype = $form.find('#subtype');
        $reason = $form.find('#reason');

        $name = $form.find('#name');


        $names = $names.add($nom);
        $names = $names.add($prenom);


        $inputs = $inputs.add($adherent_oui);
        $inputs = $inputs.add($adherent_non);
        $inputs = $inputs.add($nom);
        $inputs = $inputs.add($prenom);
        $inputs = $inputs.add($numadherent);
        $inputs = $inputs.add($email);
        $inputs = $inputs.add($phone);

        $inputs = $inputs.add($old_email);
        $inputs = $inputs.add($new_email);

        $inputs = $inputs.add($old_phone);
        $inputs = $inputs.add($new_phone);

        $inputs = $inputs.add($old_phone_mobile);
        $inputs = $inputs.add($new_phone_mobile);

        $inputs = $inputs.add($message);

        $inputs = $inputs.add($new_adresse);
        $inputs = $inputs.add($old_adresse);

        $inputs = $inputs.add($iban);
        $inputs = $inputs.add($bic);
        $inputs = $inputs.add($beneficiaire);


        $part_adherent = $part_adherent.add($adherent_non);
        $part_adherent = $part_adherent.add($adherent_oui);

        $part_iban = $part_iban.add($iban);
        $part_iban = $part_iban.add($bic);
        $part_iban = $part_iban.add($beneficiaire);

        $parts = $form.find('.form-part');

        $submit.hide();

        $names.on('change', function() {
            $name = $prenom.val() + ' ' + $nom.val();
        });



        $email.on('input', function() {
            $new_email.val($(this).val());
        });

        $phone.on('input', function() {
            var v = $(this).val();

            if (v.match(/^0[67]/))
                $new_phone_mobile.val(v);
            else
                $new_phone.val(v);
        });

        $inputs.on('change', updateDescription);

        $form.on('gp.pre_submit_form', updateDescription);

        $part_adherent.on('change', function() {

            if ($adherent_oui.is(':checked')) {
                $('#numadherent-part').show().focus();
            }
            else {
                $('#numadherent-part').hide();
            }
        });



        function searchSite(query) {
            location.href = "/" + lang + "/?s=" + encodeURIComponent(query);
        }

        $form.find('.open-search-form').on('click', function() {
            var $this = $(this),
                $search = $('<div class="filter-form"><input type="text" autofocus="" id="s" name="s" value="" placeholder="Recherche"><button type="button">ок</button></div>');

            $search.find('button').on('click', function() {
                searchSite($search.find('input').val());
            });

            $this.replaceWith($search);


            $search.find('input').on('keypress', function(e) {
                var keyCode = e.keyCode || e.which;

                if (keyCode === 13) {
                    e.preventDefault();
                    e.stopPropagation();

                    searchSite($(this).val());
                    return false;
                }
            }).focus();


        });



        $form.find('select').on('change', function(e) {
            var $this = $(this),
                $option = $this.find('option:selected'),
                action = $option.data('action')

            $parts.hide();
            $reason.val('');

            $part_iban.each(function() {
                $(this).rules('remove');
            });


            switch (action) {
                case 'redirect':
                    alert('On redirige ' + $option.html()  + ' vers ' + $option.val());
                    break;

                case 'hide':
                    $('.form-part').hide();
                    break;


                case 'show-iban':
                    $('#iban-part').show();
                    $submit.show();
//                  $part_message.hide();
                    $iban.rules('add', {
                        required: true,
                        maxlength: 42,
                        minlength: 33,
                        iban: true
                    });
                    $beneficiaire.rules('add', {required: true});
                    $bic.rules('add', {
                        required: true,
                        maxlength: 11,
                        minlength: 8
                    });
                    break;


                case 'show-fiscal':
                    $('#fiscal-part').show();
                    $('#fiscal2-part').show();
                    $submit.show();
                    $part_message.show();
                    break;

                case 'show-coords':
                    $('#coords-part').show();
                    $submit.show();
                    //$part_message.hide();
                    break;

                case 'show-campagne':
                    $('#campagne-part').show();
                    $submit.show();
                    $part_message.show();
                    break;

                case 'show-autre':
                    $('#autre-part').show();
                    $submit.show();
                    $part_message.show();
                    break;

                case 'show-benevolat':
                    $('#benevolat-part').show();
                    $submit.hide();
                    $part_message.hide();
                    break;

                case 'show-travail':
                    $('#travail-part').show();
                    $submit.hide();
                    $part_message.hide();
                    break;

                case 'show-stage':
                    $('#stage-part').show();
                    $submit.hide();
                    $part_message.hide();
                    break;

                case 'show-greenpeace':
                    $('#greenpeace-part').show();
                    $submit.show();
                    $part_message.show();
                    break;

                case 'show-etudes':
                    $('#etudes-part').show();
                    $submit.hide();
                    $part_message.hide();
                    break;

                case 'show-presse':
                    $('#presse-part').show();
                    $submit.hide();
                    $part_message.hide();
                    break;


                case 'show-shop':
                    $('#shop-part').show();
                    $submit.hide();
                    $part_message.hide();
                    break;

                default:
                    var reason = $option.data('reason');
                    if (reason) {
                        $reason.val(reason);
                    }
                    break;
            }

            $optgroup = $option.parent('optgroup');
            $type.val( $optgroup.data('type') );
            $subtype.val( $option.val() )
        });


    }

    (!window.init_form) && (window.init_form = {});

    window.init_form.contact = init_form_contact;

})(window);
