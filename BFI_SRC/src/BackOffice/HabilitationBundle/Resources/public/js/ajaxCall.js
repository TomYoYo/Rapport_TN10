$(function(){
   $('#form_agences').change(function(){
       var path = $('#form_agences').attr("data-path")
       id_select = $('#form_agences').val();

       $.ajax({
           url: 'services/',
           method: "post",
           data: {'id': id_select},
           dataType: 'json',
           success: function(json){ // quand la réponse de la requete arrive
               $('#form_servicess_services').html('');
               $.each(json, function(index, value) {
                   $('#form_servicess_services').append('<option value="'+ value.id +'">'+ value.name +'</option>');
               });
               id_agence = $('#form_agences').val();
               id_service = $('#form_servicess_services').val();
               $('#form_service').text($('#form_servicess_services option:selected').text());
               $('#form_service').val(id_service);
               $.ajax({
                   url: 'sous-services/',
                   method: "post",
                   data: {'id_agence': id_agence,'id_service' : id_service},
                   dataType: 'json',
                   success: function(json){ // quand la réponse de la requete arrive
                       $('#form_servicess_sous_services').html('');
                       $.each(json, function(index, value) {
                           $('#form_servicess_sous_services').append('<option value="'+ value.id +'">'+ value.name +'</option>');
                       });
                       $('#form_sservice').val($('#form_servicess_sous_services').val());
                       $('#form_sservice').text($('#form_servicess_sous_services option:selected').text());
                   },
                   error: function(resultat, statut, erreur) {
                       console.log(resultat,statut,erreur);
                   }
               })
           },
           error: function(resultat, statut, erreur) {
                console.log(resultat,statut,erreur);
           }
       })

   })


});

$(function(){
    $('#form_servicess_services').change(function(){
        id_agence = $('#form_agences').val();
        id_service = $('#form_servicess_services').val();
        $('#form_service').val(id_service);
        $('#form_service').text($('#form_servicess_services option:selected').text());

        $.ajax({
            url: 'sous-services/',
            method: "post",
            data: {'id_agence': id_agence,'id_service' : id_service},
            dataType: 'json',
            success: function(json){ // quand la réponse de la requete arrive
                $('#form_servicess_sous_services').html('');
                $.each(json, function(index, value) {
                    $('#form_servicess_sous_services').append('<option value="'+ value.id +'">'+ value.name +'</option>');
                });
                $('#form_sservice').val($('#form_servicess_sous_services').val());
                $('#form_sservice').text($('#form_servicess_sous_services option:selected').text());

            },
            error: function(resultat, statut, erreur) {
                console.log(resultat,statut,erreur);
            }
        })
    })
});

$('#form_servicess_sous_services').change(function(){
    $('#form_sservice').val($('#form_servicess_sous_services').val());
    $('#form_sservice').text($('#form_servicess_sous_services option:selected').text());

})


$('#recherche').autocomplete({
    source : function(request, response){
        $.ajax({
            url : 'users/',
            dataType : 'json',
            method: "post",
            data : {
                name : $('#recherche').val()
            },
            success : function(json){
               response(json);
            },
            error: function(resultat, statut, erreur) {
                console.log(resultat,statut,erreur);
            }
        })
    }
});

var subCount = 0;

$("#ajoutSup").on('click', function(){
    var login = $('#searchSup').val();
    if(login != '') {
        var same = false
        subCount = 0;
        $("#tableSup > tr > td:first-child").each(function(){
            subCount ++;

            if(this.innerHTML == login)
            {
                same = true
            }
        });
        if(!same)
        {
            $.ajax({
                url : 'check/',
                dataType : 'json',
                method: "post",
                data : {
                    name : login
                },
                success : function(json){
                    console.log(json);
                    if(json)
                    {
            console.log(subCount)
            var subList = jQuery('#sub-fields-list');
            // parcourt le template prototype
            var newWidget = subList.attr('data-prototype');
            // remplace les "__name__" utilisés dans l'id et le nom du prototype
            // par un nombre unique pour chaque email
            // le nom de l'attribut final ressemblera à name="contact[emails][2]"
            newWidget = newWidget.replace(/__name__/g, subCount);
            // créer une nouvelle liste d'éléments et l'ajoute à notre liste
            var newLi = jQuery('<li></li>').html(newWidget);
            newLi.appendTo(jQuery('#sub-fields-list'));

            $("#form_sub_" + subCount ).val(login);
            var html = "<tr id='tableSup"+subCount+"'><td>"+login+"</td><td><a href='javascript:void(0);' onClick='supSub("+subCount+")'>Supprimer</a></td></tr>"
            $('#tableSup').append(html);
            $('#searchSup').val('');
                    }
                },
                error: function(resultat, statut, erreur) {
                    console.log(resultat,statut,erreur);
                }
            })
        }

    }
});

$('#recherche2').autocomplete({
    source : function(request, response){
        $.ajax({
            url : 'users/',
            dataType : 'json',
            method: "post",
            data : {
                name : $('#recherche2').val()
            },
            success : function(json){
                response(json);
            },
            error: function(resultat, statut, erreur) {
                console.log(resultat,statut,erreur);
            }
        })
    }
});

$(document).ready(function(){
    if($('#form_servicess_services').val() == '')
    {
    var path = $('#form_agences').attr("data-path")
    id_select = $('#form_agences').val();

    $.ajax({
        url: 'services/',
        method: "post",
        data: {'id': id_select},
        dataType: 'json',
        success: function(json){ // quand la réponse de la requete arrive
            $('#form_servicess_services').html('');
            $.each(json, function(index, value) {
                $('#form_servicess_services').append('<option value="'+ value.id +'">'+ value.name +'</option>');
            });
            id_agence = $('#form_agences').val();
            id_service = $('#form_servicess_services').val();
            $('#form_service').text($('#form_servicess_services option:selected').text());
            $('#form_service').val(id_service);
            $.ajax({
                url: 'sous-services/',
                method: "post",
                data: {'id_agence': id_agence,'id_service' : id_service},
                dataType: 'json',
                success: function(json){ // quand la réponse de la requete arrive
                    $('#form_servicess_sous_services').html('');
                    $.each(json, function(index, value) {
                        $('#form_servicess_sous_services').append('<option value="'+ value.id +'">'+ value.name +'</option>');
                    });
                    $('#form_sservice').val($('#form_servicess_sous_services').val());
                    $('#form_sservice').text($('#form_servicess_sous_services option:selected').text());
                },
                error: function(resultat, statut, erreur) {
                    console.log(resultat,statut,erreur);
                }
            })
        },
        error: function(resultat, statut, erreur) {
            console.log(resultat,statut,erreur);
        }
    })}
})

$('#form_oldSup').change(function(){
    $.ajax({
        url : 'subordonne/',
        dataType : 'json',
        method: "post",
        data : {
            name : $('#form_oldSup option:selected').text()
        },
        success : function(json){
            $('#tableSup').html("")
            $('#sub-fields-list').html("")
            console.log(json);
            var subCount = 0;
            $.each(json, function(index, value) {
              $('#tableSup').append("<tr id='tableSup"+subCount+"'><td>"+value.BAS006011+"</td><td><a href='javascript:void(0);' onclick='supSub("+subCount+")'>supprimer</a></td></tr>")
                var subList = jQuery('#sub-fields-list');

                // grab the prototype template
                var newWidget = subList.attr('data-prototype');
                // replace the "__name__" used in the id and name of the prototype
                // with a number that's unique to your emails
                // end name attribute looks like name="contact[emails][2]"
                newWidget = newWidget.replace(/__name__/g, subCount);


                // create a new list element and add it to the list
                var newLi = jQuery('<li></li>').html(newWidget);
                newLi.appendTo(subList);
                $('#form_sub_'+subCount).val(value.BAS006011)
                subCount++;
            })
        },
        error: function(resultat, statut, erreur) {
            console.log(resultat,statut,erreur);
        }
    })
});

function supSub(entry){
    $('#tableSup'+entry).remove();
    $('#form_sub_'+entry).parent().remove();
}