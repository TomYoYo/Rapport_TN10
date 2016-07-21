/*
 Created on : 12 janv. 2014, 11:36:21
 Author     : d.briand
 */

$(document).ready(function() {
    // placeholder pour ie9
    $('input, textarea').placeholder();
    
    // Alerte si JC
    $('#od_odbundle_operation_isComplementaryDay').change(function() {
        if ($(this).val() == 1) {
            $('#helpBlockJC').show();
        } else {
            $('#helpBlockJC').hide();
        }
    });

    // Résultats par page
    $('#numberPagination').change(function() {
        $('#paginNumber').submit();
    });

    $('#listeCompte').dataTable({
        "oLanguage": {
            "sProcessing": "Traitement en cours...",
            "sSearch": "Rechercher",
            "sLengthMenu": "Afficher _MENU_ résultats",
            "sInfo": "Affichage des résultats _START_ à _END_ sur _TOTAL_ enregistrements",
            "sInfoEmpty": "Affichage des résultats 0 à 0 sur 0 enregistrements",
            "sInfoFiltered": "(filtré de _MAX_ résultats au total)",
            "sInfoPostFix": "",
            "sLoadingRecords": "Chargement en cours...",
            "sZeroRecords": "Aucun résultat à afficher",
            "sEmptyTable": "Aucune donnée disponible dans le tableau",
            "oPaginate": {
                "sFirst": "Premier",
                "sPrevious": "Précédent",
                "sNext": "Suivant",
                "sLast": "Dernier"
            },
            "oAria": {
                "sSortAscending": ": activer pour trier la colonne par ordre croissant",
                "sSortDescending": ": activer pour trier la colonne par ordre décroissant"
            }
        }
    });

    // Instanciation de la modal raccourcis
    $('#listeCompteModal').modal({show: false});
    $('#raccourciModal').modal({show: false});

    // Formattage des montants
    $('.montantDeb').autoNumeric('init', {'aSep': ' ', 'aDec': ',', 'vMax': '9999999999.99'});
    $('.montantCre').autoNumeric('init', {'aSep': ' ', 'aDec': ',', 'vMax': '9999999999.99'});
    $('#totalDeb').autoNumeric('init', {'aSep': ' ', 'aDec': ',', 'vMax': '9999999999.99'});
    $('#totalCre').autoNumeric('init', {'aSep': ' ', 'aDec': ',', 'vMax': '9999999999.99'});

    setTotalFields();

    // Vérifier les comptes à l'ouverture
    $('.accountField').each(function() {
        verifCompte($(this));
    });
    
    // Vérifier les comptes à la volée
    $('.accountField').blur(function() {
        verifCompte($(this));
    });

    // Vérifier le formulaire à l'ajout
    $('#submitNewOperation').click(function() {
        nbErrors = $('.has-error .accountField').length;
        if (!verifyNewForm() || nbErrors > 0) {
            return false;
        } else {
            return confirm('Êtes-vous sûr de vouloir enregistrer cette opération ?');
        }
    });

    // Vérifier le formulaire à l'edition
    $('#submitEditOperation').click(function() {
        nbErrors = $('.has-error .accountField').length;
        if (!verifyNewForm() || nbErrors > 0) {
            return false;
        } else {
            return confirm('Êtes-vous sûr de vouloir enregistrer les modifications sur cette opération ?')
        }
    });

    // Ajout des lignes vides à l'édition
    if ($('h1').attr('id') === "editOperations") {
        var nbLineDeb = $('.montantDeb').length;
        var nbLineCre = $('.montantCre').length;

        for (i = nbLineDeb; i < 10; i++) {
            addLine('deb');
        }
        for (i = nbLineCre; i < 10; i++) {
            addLine('cre');
        }

        setTotalFields();
    }

    // Mise à jour des champs requis
    $('.accountField').on('change', function() {
        if ($(this).val() !== '') {
            $(this).parent().parent().children('td').each(function() {
                $(this).children('input').attr('required', 'required');
            });
        } else {
            $(this).parent().parent().children('td').each(function() {
                $(this).children('input').removeAttr('required', 'required');
            });
        }
    });

    // Mise à jour du total
    $(document).on('change', '.montantDeb', function() {
        setTotalFields();
    });

    // Mise à jour du total
    $(document).on('change', '.montantCre', function() {
        setTotalFields();
    });

    // Gestion des raccourcis claviers
    // CTRL + OTHER KEY
    var isCtrl = false;

    $(document).keyup(function(e) {
        if (e.which === 17) {
            isCtrl = false;
        }
    }).keydown(function(e) {
        if (e.which === 17) {
            isCtrl = true;
        }

        if (isCtrl === true) {
            switch (e.which) {
                // Ajouter une ligne au dessus de la ligne courante (CTRL + UP)
                case 38:
                    addLine('before', $(':focus'));
                    return false;
                    break;

                    // Ajouter une ligne en dessous de la ligne courante (CTRL + DOWN)
                case 40:
                    addLine('after', $(':focus'));
                    return false;
                    break;

                    // Dupliquer la ligne courante (CTRL + BACKSPACE)
                case 32:
                    addLine('after', $(':focus'), 1);
                    setTotalFields();
                    return false;
                    break;

                    // Dupliquer le libellé sur toutes les lignes (CTRL + INSERT)
                case 45:
                    var hasFocus = $('.tableauScrollable input.libelleField').is(':focus');
                    if (hasFocus) {
                        var value = $(':focus').val();
                        $('.tableauScrollable input.libelleField').each(function() {
                            if (!$(this).val()) {
                                $(this).val(value);
                            }
                        });
                    }
                    return false;
                    break;

                    // Copier la valeur du dessus (CTRL + SHIFT)
                case 16:
                    var hasFocus = $('.tableauScrollable input.form-control').is(':focus');
                    if (hasFocus) {
                        var currentName = $(':focus').attr('name');
                        var prevValue = $(':focus').parent().parent().prev().find("input[name='" + currentName + "']").val();
                        $(':focus').val(prevValue);
                    }
                    setTotalFields();
                    return false;
                    break;

                    // Supprimer la ligne (CTRL + SUPPR)
                case 46:
                    var hasFocus = $('.tableauScrollable input.form-control').is(':focus');
                    if (hasFocus) {
                        var tr = $(':focus').parent().parent();
                        tr.siblings().first().find('input.form-control').last().focus();
                        //console.log(tr.siblings().first());
                        //console.log(tr.siblings().first().find('input.form-control').last());
                        tr.remove();
                    }
                    setTotalFields();
                    rebaseNumerotation();
                    return false;
                    break;
            }
        }
    });

    // ONLY KEY
    $(document).keyup(function(e) {
        switch (e.which) {
            case 38:
                var hasFocus = $('.tableauScrollable input.form-control').is(':focus');
                if (hasFocus) {
                    var currentName = $(':focus').attr('name');
                    $(':focus').parent().parent().prev().find("input[name='" + currentName + "']").focus();
                }
                break;
            case 40:
                var hasFocus = $('.tableauScrollable input.form-control').is(':focus');
                if (hasFocus) {
                    var currentName = $(':focus').attr('name');
                    $(':focus').parent().parent().next().find("input[name='" + currentName + "']").focus();
                }
                break;
        }
    });
});

function addLine(where, element, withContent) {
    if (typeof (element) !== 'undefined') {
        var hasFocus = $('.tableauScrollable input.form-control').is(':focus');
        if (hasFocus) {
            var currentLine = $(':focus').parent().parent();
            var html = currentLine.get(0).outerHTML;

            if (where === 'before') {
                currentLine.before(html);
                var newLine = currentLine.prev();
            } else if (where === 'after') {
                currentLine.after(html);
                var newLine = currentLine.next();
            }

            // cas de duplication
            if (typeof (withContent) !== 'undefined' && withContent === 1) {
                var values = [];

                currentLine.children().each(function() {
                    values.push($(this).children('input.form-control').val());
                });

                var i = 0;
                newLine.children().each(function() {
                    $(this).children('input.form-control').val(values[i]);
                    i++;
                });

                // Remise à 0 de l'id opération car nouvelle ligne
                newLine.find('input[name="idOpe[]"]').val('');
            } else {
                newLine.children().each(function() {
                    $(this).children('input.form-control').removeAttr('value');
                });
            }

            // Formattage des montants
            $('.montantDeb').autoNumeric('init', {'aSep': ' ', 'aDec': ',', 'vMax': '9999999999.99'});
            $('.montantCre').autoNumeric('init', {'aSep': ' ', 'aDec': ',', 'vMax': '9999999999.99'});
            $('#totalDeb').autoNumeric('init', {'aSep': ' ', 'aDec': ',', 'vMax': '9999999999.99'});
            $('#totalCre').autoNumeric('init', {'aSep': ' ', 'aDec': ',', 'vMax': '9999999999.99'});
        }
    } else if (typeof (where) !== 'undefined') {
        var id = where === "cre" ? "#montantCre" : "#montantDeb";
        var html = $(id + " table tbody > tr:last").get(0).outerHTML;
        $(id + ' table').append(html);
    }

    rebaseNumerotation();
}

function rebaseNumerotation() {
    var i = 1;
    $('.tableauScrollable input.false-field').each(function() {
        $(this).val(i++);
    });
}

function setTotalFields() {
    var totalCre = 0;
    var totalDeb = 0;

    $('.montantCre').each(function() {
        totalCre += Number($(this).val().replace(/\,/g, '.').replace(/\s/g, ''));
    });
    $('.montantDeb').each(function() {
        totalDeb += Number($(this).val().replace(/\,/g, '.').replace(/\s/g, ''));
    });

    $('#totalCre').autoNumeric('set', totalCre);

    $('#totalDeb').autoNumeric('set', totalDeb);
}

function verifyNewForm() {
    _return = true; // Sans var : globale
    var _text = "";
    var _mouvementOk = false;
    var _lineDeb = false;
    var _lineCre = false;

    // Vérification du format de la date et de son existence
    testField($('#od_odbundle_operation_dateVal'), /^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/);

    // Vérification du format de la date et de son existence
    testField($('#od_odbundle_operation_dateCpt'), /^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/);

    // Vérification du format du code opération et de son existence
    testField($('#od_odbundle_operation_codeOpe'), /^\*{1}[A-Z]{2}$/);

    // Vérification du format du code event et de son existence
    testField($('#od_odbundle_operation_codeEve'), /^\w{1,3}$/);

    // Vérification du format de la ref lett. et de son existence
    testField($('#od_odbundle_operation_refLet'), /^\w{1,7}$/);

    // Vérification du format de la ref. ana. et de son existence
    testField($('#od_odbundle_operation_refAnalytique'), /^\w{3}$/);

    // Vérification du format du tiers et de son existence
    testFieldWithAjax($('#od_odbundle_operation_tiers'), /^\w{7}$/, '/fiscalite/od/ajax/customer/');

    // Vérification qu'au moins une ligne de mouvement en débit est saisie
    $("#montantDeb input[name='compte[]']").each(function() {
        if ($(this).val() !== "") {
            _lineDeb = true;
        }
    });

    if (!_lineDeb) {
        _text += "\nVotre opération nécessite au minimun un mouvement en débit.";
        _return = false;
    }

    // Vérification qu'au moins une ligne de mouvement en crédit est saisie
    $("#montantCre input[name='compte[]']").each(function() {
        if ($(this).val() !== "") {
            _lineCre = true;
        }
    });

    if (!_lineCre) {
        _text += "\nVotre opération nécessite au minimun un mouvement en crédit.";
        _return = false;
    }

    // Vérification que les lignes de mouvement saisies le sont bien
    if (_lineDeb && _lineCre) {
        _mouvementOk = true;

        // On parcourt tout les champs compte
        $("input[name='compte[]']").each(function() {
            // On ne boucle que si un compte est renseigné
            if ($(this).val() !== "") {
                // on récupère l'élément correspondant à la ligne
                // et le numéro de ligne stocké dans l'attribut "rel"
                var line = $(this).parent().parent();
                var rel = $(this).attr('rel');

                // On parcourt tout les champs qui on le même numéro de ligne
                line.find("input[rel=" + rel + "]").each(function() {
                    // on plante une erreur si sa valeur est nulle
                    if ($(this).val() === "") {
                        $(this).addClass("error");
                        _return = false;
                        _mouvementOk = false;
                    }
                });
            }
        });
    }

    // Vérification que l'opération est équilibrée
    // (vérification seulement s'il n'y a pas de lignes de mouvemens non valides)
    if (_mouvementOk) {
        if ($('#totalDeb').val() !== $('#totalCre').val()) {
            _text += "\nVotre opération n'est pas équilibrée.";
            _return = false;
        }
    }

    if (_text !== "") {
        alert(_text);
    }

    return _return;
}

function testField(field, regex) {
    if (!regex.test(field.val())
            || field.val() === "") {
        field.parent().parent().removeClass('has-success');
        field.parent().parent().addClass('has-error');
        _return = false;
    } else {
        field.parent().parent().removeClass('has-error');
        field.parent().parent().addClass('has-success');
    }
}

function testFieldWithAjax(field, regex, path) {
    if (!regex.test(field.val()) && field.val() !== "") {
        field.parent().parent().removeClass('has-success');
        field.parent().parent().addClass('has-error');
        _return = false;
    } else {
        $.ajax({
            type: "GET",
            url: path,
            data: {data: field.val()},
            success: function(msg) {
                if (msg == 0) {
                    field.parent().parent().removeClass('has-success');
                    field.parent().parent().addClass('has-error');
                    _return = false;
                } else {
                    field.parent().parent().removeClass('has-error');
                    field.parent().parent().addClass('has-success');
                }
            }
        });
    }
}

function testFieldWithAjaxMulti(field, regex, path) {
    field.each(function() {
        var that = $(this);
        if (!regex.test(that.val()) && that.val() !== "") {
            that.parent().removeClass('has-success');
            that.parent().addClass('has-error');
            _return = false;
        } else if (that.val() == "") {

        } else {
            $.ajax({
                type: "GET",
                url: path,
                data: {data: that.val()},
                success: function(msg) {
                    if (msg == 0) {
                        that.parent().removeClass('has-success');
                        that.parent().addClass('has-error');
                        _return = false;
                    } else {
                        that.parent().removeClass('has-error');
                        that.parent().addClass('has-success');
                    }
                }
            });
        }
    });
}

function verifCompte(that) {
    regex = /[a-zA-Z0-9]{7,}$/;

    if (!regex.test(that.val()) && that.val() !== "") {
        that.parent().removeClass('has-success');
        that.parent().addClass('has-error');
    } else if (that.val() != "") {
        $.ajax({
            type: "GET",
            url: "/fiscalite/od/ajax/account/",
            data: {data: that.val()},
            success: function(msg) {
                if (msg == 0) {
                    that.parent().removeClass('has-success');
                    that.parent().addClass('has-error');
                } else {
                    that.parent().removeClass('has-error');
                    that.parent().addClass('has-success');
                }
            }
        });
    }
}