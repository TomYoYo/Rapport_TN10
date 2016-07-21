/**
 * Created by t.pueyo on 10/03/2016.
 */
var collaterauxCount = 0;

$("#ajout").on('click', function(){
    var login = $('#recherche').val();
    var date = $('#endDate').val();
    if(login != '') {
        var same = false
        collaterauxCount = 0;
        $("#tableBody > tr > td:first-child").each(function(){
            collaterauxCount ++;
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
                        if(json)
                        {
                            var collaterauxList = jQuery('#collateraux-fields-list');
                            // parcourt le template prototype
                            var newWidget = collaterauxList.attr('data-prototype');
                            // remplace les "__name__" utilisés dans l'id et le nom du prototype
                            // par un nombre unique pour chaque email
                            // le nom de l'attribut final ressemblera à name="contact[emails][2]"
                            newWidget = newWidget.replace(/__name__/g, collaterauxCount);
                            // créer une nouvelle liste d'éléments et l'ajoute à notre liste
                            var newLi = jQuery('<li></li>').html(newWidget);
                            newLi.appendTo(jQuery('#collateraux-fields-list'));

                            $("#form_collateraux_" + collaterauxCount + "_user").val(login);
                            $("#form_collateraux_" + collaterauxCount + "_Date").val(date);
                            var html = "<tr id='collateral"+collaterauxCount+"'><td>"+json.BAS006011+"</td><td>"+date+"</td><td><a href='javascript:void(0);' onClick='deleteEntry("+collaterauxCount+")'>Supprimer</a></td></tr>"
                            $('#tableBody').append(html);
                            $('#recherche').val('');
                            $('#endDate').val('');
                        }
                    },
                    error: function(resultat, statut, erreur) {
                        console.log(resultat,statut,erreur);
                    }
                })

            }

    }
});
var collaterauxotherCount = 0;
$("#ajout2").on('click', function(){
    var login = $('#recherche2').val();
    var date = $('#endDate2').val();
    if(login != '') {
        collaterauxotherCount=0;
        var same = false
        $("#tableBody2 > tr > td:first-child").each(function(){
            collaterauxotherCount++;
            if(this.innerHTML == login)
            {
                same = true;
            }
        })
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
            var collaterauxList = jQuery('#collaterauxother-fields-list');
                        // parcourt le template prototype
            var newWidget = collaterauxList.attr('data-prototype');
                        // remplace les "__name__" utilisés dans l'id et le nom du prototype
                        // par un nombre unique pour chaque email
                        // le nom de l'attribut final ressemblera à name="contact[emails][2]"
            newWidget = newWidget.replace(/__name__/g, collaterauxotherCount);
                        // créer une nouvelle liste d'éléments et l'ajoute à notre liste
            var newLi = jQuery('<li></li>').html(newWidget);
            newLi.appendTo(jQuery('#collaterauxother-fields-list'));
            $("#form_collaterauxother_" + collaterauxotherCount + "_user").val(login);
            $("#form_collaterauxother_" + collaterauxotherCount + "_Date").val(date);
            var html = "<tr id='collateralother"+collaterauxotherCount+"'><td>"+login+"</td><td>"+date+"</td><td><a href='javascript:void(0);' onClick='deleteEntryOther("+collaterauxotherCount+")'>Supprimer</a></td></tr>"
            $('#tableBody2').append(html);
            $('#recherche2').val('');
            $('#endDate2').val('');
                    }
                },
                error: function(resultat, statut, erreur) {
                    console.log(resultat,statut,erreur);
                }
            })
        }

    }
    });

function deleteEntry(entry){
   $('#collateral'+entry).remove();
    $('#form_collateraux_'+entry).parent().remove();
}

function deleteEntryOther(entry)
{
    $('#collateralother'+entry).remove();
    $('#form_collaterauxother_'+entry).parent().remove();
}

function duplicate(way)
{
    var cpt = 0;
    if(way)
    {
        $("#collaterauxother-fields-list > li").each(function(){
            this.remove();
        })
        $("#tableBody2 > tr").each(function(){
            this.remove();
        })
        $("#tableBody > tr").each(function(){
            first = $(this).children().eq(0);
            console.log(first[0]);
            second = $(this).children().eq(1);
            console.log(second);
            name = first[0].innerHTML;
            date = second[0].innerHTML;
            var html = "<tr id='collateralother"+cpt+"'><td>"+name+"</td><td>"+date+"</td><td><a href='javascript:void(0);' onClick='deleteEntryOther("+cpt+")'>Supprimer</a></td></tr>";
            $('#tableBody2').append(html);
            var collaterauxList = jQuery('#collaterauxother-fields-list');
            // parcourt le template prototype
            var newWidget = collaterauxList.attr('data-prototype');
            // remplace les "__name__" utilisés dans l'id et le nom du prototype
            // par un nombre unique pour chaque email
            // le nom de l'attribut final ressemblera à name="contact[emails][2]"
            newWidget = newWidget.replace(/__name__/g, cpt);
            // créer une nouvelle liste d'éléments et l'ajoute à notre liste
            var newLi = jQuery('<li></li>').html(newWidget);
            newLi.appendTo(jQuery('#collaterauxother-fields-list'));
            $("#form_collaterauxother_" + cpt + "_user").val(name);
            $("#form_collaterauxother_" + cpt + "_Date").val(date);
            cpt++;
        })
    }
    else
    {
        $("#collateraux-fields-list > li").each(function(){
            this.remove();
        })
        $("#tableBody > tr").each(function(){
            this.remove();
        })
        $("#tableBody2 > tr").each(function(){
            first = $(this).children().eq(0);
            console.log(first[0]);
            second = $(this).children().eq(1);
            console.log(second);
            name = first[0].innerHTML;
            date = second[0].innerHTML;
            var html = "<tr id='collateral"+cpt+"'><td>"+name+"</td><td>"+date+"</td><td><a href='javascript:void(0);' onClick='deleteEntry("+cpt+")'>Supprimer</a></td></tr>";
            $('#tableBody').append(html);
            var collaterauxList = jQuery('#collateraux-fields-list');
            // parcourt le template prototype
            var newWidget = collaterauxList.attr('data-prototype');
            // remplace les "__name__" utilisés dans l'id et le nom du prototype
            // par un nombre unique pour chaque email
            // le nom de l'attribut final ressemblera à name="contact[emails][2]"
            newWidget = newWidget.replace(/__name__/g, cpt);
            // créer une nouvelle liste d'éléments et l'ajoute à notre liste
            var newLi = jQuery('<li></li>').html(newWidget);
            newLi.appendTo(jQuery('#collateraux-fields-list'));
            $("#form_collateraux_" + cpt + "_user").val(name);
            $("#form_collateraux_" + cpt + "_Date").val(date);
            cpt++;
        })
    }
}


/*$('#form_codeRes').on('change', function(){
    if($('#form_codeRes').val() == 0)
    {
        $('#superieur').hide();
        $('#form_superieur').prop('required',false);
        $('#form_superieur').removeClass('required');
        $('#form_superieur').val('');
        $('#dossier').hide();
        $('#dossier').prop('required',false);
        $('#dossier').removeClass('required');
        $('#dossier').val('');
        $('#contentieux').hide();
        $('#form_contentieux').prop('required',false);
        $('#form_contentieux').removeClass('required');
        $('#col').hide();
    }
    else
    {
        $('#superieur').show();
        $('#form_superieur').prop('required',true);
        $('#form_superieur').addClass('required');
        $('#dossier').show();
        $('#dossier').prop('required',true);
        $('#dossier').addClass('required');
        $('#contentieux').show();
        $('#form_contentieux').prop('required',true);
        $('#form_contentieux').addClass('required');
        $('#col').show();
    }
})*/

$('#codeResponsable').on('click', function(){
    $('#col').show();
    $('#codeResponsable').hide();
    $('#codeResponsableCancel').show();
    $('#form_codeRes').prop('required',true);
    $('#form_codeRes').addClass('required');
    $('#form_superieur').prop('required',true);
    $('#form_superieur').addClass('required');
    $('#dossier').prop('required',true);
    $('#dossier').addClass('required');
    $('#form_contentieux').prop('required',true);
    $('#form_contentieux').addClass('required');
    $('#form_abbr').prop('required',true);
    $('#form_abbr').addClass('required');
    $('#form_lib').prop('required',true);
    $('#form_lib').addClass('required');
    $('#form_isCode').prop('checked', true);
    $('#form_lib').val($('#form_firstName').val()+' '+$('#form_name').val());
    $('#form_abbr').val($('#form_firstName').val().substr(0,1).toUpperCase()+'.'+$('#form_name').val().substr(0,8).toUpperCase())
});

$('#codeResponsableCancel').on('click', function(){
    $('#col').hide();
    $('#codeResponsable').show();
    $('#codeResponsableCancel').hide();
    $('#form_codeRes').prop('required',false);
    $('#form_codeRes').removeClass('required');
    $('#form_superieur').prop('required',false);
    $('#form_superieur').removeClass('required');
    $('#dossier').prop('required',false);
    $('#dossier').removeClass('required');
    $('#form_contentieux').prop('required',false);
    $('#form_contentieux').removeClass('required');
    $('#form_abbr').prop('required',false);
    $('#form_abbr').removeClass('required');
    $('#form_lib').prop('required',false);
    $('#form_lib').removeClass('required');
    $('#form_isCode').prop('checked', false);
    $('#form_abbr').val()
    $('#form_lib').val()

});

$(document).ready(function(){
    var submit = false;
    $("form").submit(function(event){
        $('#form_servicess_services').val('');
        $('#form_servicess_sous_services').val('');
    });
});

/*$(document).ready(function(){
    $('#submit').on('click',function(event){
        event.preventDefault();
        $('#form_servicess_services').val('');
        $('#form_servicess_sous_services').val('');
        if(confirm('Cette action est irréversible, êtes vous sur de vouloir continuer ? '))
        {
            $("form").unbind('submit').subm
        }
    })
})*/

$(document).ready(function(){
    var cpt =0;
    $("#collateraux-fields-list > li").each(function(){
        var user = $("#form_collateraux_" + cpt + "_user").prop("defaultValue");
        $("#form_collateraux_" + cpt + "_user").val(user);
        var date = $("#form_collateraux_" + cpt + "_Date").prop("defaultValue");
        $("#form_collateraux_" + cpt + "_Date").val(date);
        var html = "<tr id='collateral"+cpt+"'><td>"+user
        +"</td><td>"+date
        +"</td><td><a href='javascript:void(0);' onClick='deleteEntry("+cpt+")'>Supprimer</a></td></tr>";
        $('#tableBody').append(html);
        cpt ++;

    })
    cpt= 0;
    $("#collaterauxother-fields-list > li").each(function(){
        var user = $("#form_collaterauxother_" + cpt + "_user").prop("defaultValue");
        $("#form_collaterauxother_" + cpt + "_user").val(user);
        var date = $("#form_collaterauxother_" + cpt + "_Date").prop("defaultValue");
        $("#form_collaterauxother_" + cpt + "_Date").val(date);
        var html = "<tr id='collateralother"+cpt+"'><td>"+user
            +"</td><td>"+date
            +"</td><td><a href='javascript:void(0);' onClick='deleteEntryOther("+cpt+")'>Supprimer</a></td></tr>";
        $('#tableBody2').append(html);
        cpt ++;

    })

    if($('#form_codeRes').val() != '')
    {
        $('#col').show();
        $('#codeResponsable').hide();
        $('#codeResponsableCancel').show();
        $('#form_codeRes').prop('required',true);
        $('#form_codeRes').addClass('required');
        $('#form_superieur').prop('required',true);
        $('#form_superieur').addClass('required');
        $('#dossier').prop('required',true);
        $('#dossier').addClass('required');
        $('#form_contentieux').prop('required',true);
        $('#form_contentieux').addClass('required');
        $('#form_abbr').prop('required',true);
        $('#form_abbr').addClass('required');
        $('#form_lib').prop('required',true);
        $('#form_lib').addClass('required');
    }

});

$('#searchSup').autocomplete({
    source : function(request, response){
        $.ajax({
            url : 'userssup/',
            dataType : 'json',
            method: "post",
            data : {
                name : $('#searchSup').val()
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

var name ='';
var firstName='';

$('#form_name').on('change',function(){
    name = $('#form_name').val();
    $('#form_abbr').val(firstName.substr(0,1).toUpperCase()+'.'+name.toUpperCase());
    $('#form_lib').val(firstName+' '+name);
});

$('#form_firstName').on('change',function(){
    firstName = $('#form_firstName').val();
    $('#form_abbr').val(firstName.substr(0,1).toUpperCase()+'.'+name.toUpperCase());
    $('#form_lib').val(firstName+' '+name);
});
