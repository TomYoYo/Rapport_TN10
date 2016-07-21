$(document).ready(function() {
    $('.click-add').click(function() {
        return verifFormAdd($(this));
    });

    $('.click-edit').click(function() {
        return verifFormEdit($(this));
    });

    function verifFormAdd(that) {
        nodeAttrId = that.attr('id');
        valueSab = nodeAttrId.substr(4);
        valueCB = $("#value-"+valueSab).val();
        ref = that.attr('ref');

        if (valueCB) {
            if (ref == "nature" && valueCB != 'D' && valueCB != 'R' && valueCB != 'd' && valueCB != 'r') {
                alert('La nature renseignée n\'est pas correcte. D ou R doit être saisie.');

                return false;
            }


            if (ref == "nature") {
                valueCB = valueCB.toUpperCase();
            }

            $.ajax({
                type: "GET",
                url: "/web/app_dev.php/controle-budgetaire/settings/ajax/add-setting",
                data: {valueSab: valueSab, valueCB: valueCB, type: ref},
                success: function (msg) {
                    alert('Ajout effectué avec succès.');
                    that.parent().parent().remove();
                    $('.body-list').append(
                        "<tr><td>" + valueSab + "</td><td>" + valueCB + "</td><td>/</td></tr>"
                    );
                },
                error: function (msg) {
                    alert("Une erreur est survenue. Merci de rééssayer ultérieurement. Si le problème persiste, contactez un administrateur.");
                }
            });
        } else {
            alert('Le champs n\'est pas renseigné. Saisissez une valeur.')

            return false;
        }

        return true;
    }

    function verifFormEdit(that) {
        valueCB = $("#value-cb").val();
        ref = $("#value-cb").attr('ref');

        if (valueCB) {
            if (ref == "NATURE" && valueCB != 'D' && valueCB != 'R' && valueCB != 'd' && valueCB != 'r') {
                alert('La nature renseignée n\'est pas correcte. D ou R doit être saisie.');

                return false;
            }
        } else {
            return confirm('Attention. Le champs n\'est pas renseigné. Voulez-vous supprimer cet enregistrement ?')
        }

        return true;
    }
});