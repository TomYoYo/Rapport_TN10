/**
 * Created by t.pueyo on 29/04/2016.
 */

// keep track of how many email fields have been rendered
var deptCount = 0;
var cpt = 0

$('#form_civility').on('change',function(){
    cpt = 0
    $('#formeJuridique').html('');
    changeCivility($('#form_civility').val());
})

$('#form_juridique').on('change',function(){
    cpt = 0
    $('#formeJuridique').html('');
    changeNace($('#form_juridique').val());
})

$('#form_quality').on('change',function(){
    cpt = 0
    $('#formeJuridique').html('');
    changeQuality($('#form_quality').val());
})

$('#form_category').on('change',function(){
    cpt = 0
    $('#formeJuridique').html('');
    changeCategory($('#form_category').val());
})

$('#add_form').on('click',function(){
    var code =  $('#form_civility').val();
    var forme = $('#form_formeJuridique option:selected ').text();
    if(forme != '')
    {
        $.ajax({
            url : 'addforme/',
            dataType : 'json',
            method: "post",
            data : {
                code : code,
                forme : forme
            },
            success : function(json){
                if(!json.nope)
                {
                    var fonc = "supprimer('"+forme+"');"
                    console.log(fonc);
                    $('#formeJuridique').append("<tr id="+'tr'+cpt+"><td>"+forme+"</td><td>"+json.code+"</td><td><a href='javascript:void(0);' onclick='supprimer("+cpt+")'>supprimer</a></td></tr>")
                    cpt++;
                }
            },
            error: function(resultat, statut, erreur) {
                console.log(resultat,statut,erreur);
            }
        })
    }
});

$('#add_juridique').on('click',function(){
    var forme =  $('#form_juridique').val();
    console.log(forme)
    var nace = $('#form_code').val();
    console.log(nace)
    if(forme != '')
    {
        $.ajax({
            url : 'addnace/',
            dataType : 'json',
            method: "post",
            data : {
                nace : nace,
                forme : forme
            },
            success : function(json){
                if(!json.nope)
                {
                    var fonc = "supprimer('"+forme+"');"
                    console.log(fonc);
                    $('#formeJuridique').append("<tr id="+'tr'+cpt+"><td>"+nace+"</td><td><a href='javascript:void(0);' onclick='supprimerNace("+cpt+")'>supprimer</a></td></tr>")
                    cpt++;
                }
            },
            error: function(resultat, statut, erreur) {
                console.log(resultat,statut,erreur);
            }
        })
    }
});

$('#add_juridique_quality').on('click',function(){
    var quality =  $('#form_quality').val();
    console.log(quality)
    var forme = $('#form_forme_juridique option:selected').text();
    console.log(forme)
    if(forme != '')
    {
        $.ajax({
            url : 'addQuality/',
            dataType : 'json',
            method: "post",
            data : {
                quality : quality,
                forme : forme
            },
            success : function(json){
                if(!json.nope)
                {
                    $('#formeJuridique').append("<tr id="+'tr'+cpt+"><td>"+forme+"</td><td><a href='javascript:void(0);' onclick='supprimerQuality("+cpt+")'>supprimer</a></td></tr>")
                    cpt++;
                }
            },
            error: function(resultat, statut, erreur) {
                console.log(resultat,statut,erreur);
            }
        })
    }
});

jQuery(document).ready(function() {
    cpt = 0;
    console.log($(this).attr('title'))
    if($(this).attr('title')== 'Intégration Clients - Paramétrage Code Etat')
    {
        changeState($('#form_state').val())

    }
    else if($(this).attr('title')== 'Intégration Clients - Paramétrage Code Civilité')
    {
        changeCivility($('#form_civility').val())
    }
    else if($(this).attr('title')== 'Intégration Clients - Paramétrage Forme Juridique SAB')
    {
        changeNace($('#form_juridique').val())
    }
    else if($(this).attr('title')== 'Intégration Clients - Paramétrage qualité client SAB')
    {
        changeQuality($('#form_quality').val())
    }
    else if($(this).attr('title')== 'Intégration Clients - Paramétrage Categorie Client')
    {
        changeCategory($('#form_category').val())
    }

    $('#backoffice_customerbundle_customer_dateNaissance').datepicker({
        changeYear:true,
        defaultDate : "-20y",
        yearRange : "c-20:c+20"
    })

    $('#backoffice_customerbundle_customer_dateCreation').datepicker({
        changeYear:true,
        yearRange : "c-10:c+10",
        dateFormat: "dd/mm/yy"
    })
    if($('#backoffice_customerbundle_customer_codeCivilite').val() == 2 ||$('#backoffice_customerbundle_customer_codeCivilite').val() == 3  ||$('#backoffice_customerbundle_customer_codeCivilite').val() == 4  )
    {
        $('#dateNaissance').show();
        $('#codePays').show();
    }
    else
    {
        $('#dateNaissance').hide();
        $('#codePays').hide();    }
});


$('#backoffice_customerbundle_customer_codeCivilite').on('change',function(){
    if($('#backoffice_customerbundle_customer_codeCivilite').val() == 2 ||$('#backoffice_customerbundle_customer_codeCivilite').val() == 3 ||$('#backoffice_customerbundle_customer_codeCivilite').val() == 4 )
    {
        $('#dateNaissance').show();
        $('#codePays').show();
    }
    else
    {
        $('#dateNaissance').hide();
        $('#codePays').hide();
    }
})

function changeCivility(id)
{
    $.ajax({
        url : 'init/',
        dataType : 'json',
        method: "post",
        data : {
            'id' : id
        },
        success : function(json){
            console.log(json)
            $.each(json.formes,function(index,value){
                $('#formeJuridique').append("<tr id="+'tr'+cpt+"><td>"+value+"</td><td>"+json.code+"</td><td><a href='javascript:void(0);' onclick='supprimer("+cpt+")'>supprimer</a></td></tr>")
                cpt++;
            })
        },
        error: function(resultat, statut, erreur) {
            console.log(resultat,statut,erreur);
        }
    })
}



function supprimer(cpttr)
{
    var code =  $('#form_civility').val();
    var forme = $("#tr"+cpttr).children(":first").text();
    $.ajax({
        url : 'delForme/',
        dataType : 'json',
        method: "post",
        data : {
            code : code,
            forme : forme
        },
        success : function(json){
                $('#tr'+cpttr).remove();
        },
        error: function(resultat, statut, erreur) {
            console.log(resultat,statut,erreur);
        }
    })
}

$('#form_codes').on('change',function(){
    $('#content_info').html('');

    $('.myCheckbox').each(function(){
        $(this).prop('disabled',false);
        $(this).prop('checked',false);
    })
    code = $('#form_codes option:selected').text()
    deptCount = 0;

        $.ajax({
            url : 'dept/',
            dataType : 'json',
            method: "post",
            data : {
                code : $('#form_codes option:selected').text()
            },
            success : function(json){
                console.log(json.others);
               $.each( json.others,function(index,value){
                   if(value>0 && value <10)
                   {
                       value = value.substr(1,1) - 1;
                   }
                   else if(value == 10)
                   {
                       value = '9';
                   }
                   else if(value == '97')
                   {
                       value = '95'
                   }
                   else if(value > 10)
                   {
                       value = value-1;
                   }

                    $('#form_deptChoice_'+value).prop('disabled',true);
                })

                $.each( json.own,function(index,value){
                    if(value>0 && value <10)
                    {
                        value = value.substr(1,1) - 1;
                    }
                    else if(value == 10)
                    {
                        value = '9';
                    }
                    else if(value == '97')
                    {
                        value = '95'
                    }
                    else if(value > 10)
                    {
                        value = value-1;
                    }
                    $('#form_deptChoice_'+value).prop('checked',true);
                    deptCount++;
                })
                $('#content_info').html(deptCount+' départements')

            },
            error: function(resultat, statut, erreur) {
                console.log(resultat,statut,erreur);
            }
        })

});

$('.myCheckbox').each(function(){
    $(this).prop('disabled',true);
})

function removeEntry(nbr)
{
    $('#li_'+nbr).remove();
    deptCount--;
}

function update()
{
    $('#content_info').html('');
    $('.myCheckbox').each(function(){
        $(this).prop('disabled',false);
        $(this).prop('checked',false);
    })
    code = $('#form_codes option:selected').text()
    deptCount = 0;

    $.ajax({
        url : 'dept/',
        dataType : 'json',
        method: "post",
        data : {
            code : $('#form_codes option:selected').text()
        },
        success : function(json){
            console.log(json.others);
            $.each( json.others,function(index,value){
                if(value>0 && value <10)
                {
                    value = value.substr(1,1) - 1;
                }
                else if(value == 10)
                {
                    value = '9';
                }
                else if(value == '97')
                {
                    value = '95'
                }
                else if(value > 10)
                {
                    value = value-1;
                }
                $('#form_deptChoice_'+value).prop('disabled',true);
            })
            $.each( json.own,function(index,value){
                if(value>0 && value <10)
                {
                    value = value.substr(1,1) - 1;
                }
                else if(value == 10)
                {
                    value = '9';
                }
                else if(value == '97')
                {
                    value = '95'
                }
                else if(value > 10)
                {
                    value = value-1;
                }
                $('#form_deptChoice_'+value).prop('checked',true);
                deptCount++;
            })
            $('#content_info').html(deptCount+' départements')
        },
        error: function(resultat, statut, erreur) {
            console.log(resultat,statut,erreur);
        }
    })
}

function change(cpt){
    if (cpt== 96)
    {
        cpt=97;
    }
    $.ajax({
        url : 'searchResp/',
        dataType : 'json',
        method: "post",
        data : {
            dept : cpt
        },
        success : function(json){
          $('#form_codes').val(json.result)
            update();
        },
        error: function(resultat, statut, erreur) {
            console.log(resultat,statut,erreur);
        }
    })
}

$('#form_state').on('change',function(){
    cpt = 0
    $('#formeJuridique').html('');
    changeState($('#form_state').val());
})

$('#add_form_state').on('click',function(){
    var code =  $('#form_state').val();
    console.log(code)
    var forme = $('#form_formeJuridique_state option:selected').text();
    console.log(forme)
    if(forme != '')
    {
        $.ajax({
            url : 'addstate/',
            dataType : 'json',
            method: "post",
            data : {
                code : code,
                forme : forme
            },
            success : function(json){
                if(!json.nope)
                {
                    $('#formeJuridique').append("<tr id="+'tr'+cpt+"><td>"+forme+"</td><td><a href='javascript:void(0);' onclick='supprimer_state("+cpt+")'>supprimer</a></td></tr>")
                    cpt++;
                }
            },
            error: function(resultat, statut, erreur) {
                console.log(resultat,statut,erreur);
            }
        })
    }
});

$('#add_form_cat').on('click',function(){
    var code =  $('#form_category').val();
    console.log(code)
    var forme = $('#form_forme_juridique_cat option:selected').text();
    console.log(forme)
    if(forme != '')
    {
        $.ajax({
            url : 'addcategory/',
            dataType : 'json',
            method: "post",
            data : {
                categorie : code,
                forme : forme
            },
            success : function(json){
                if(!json.nope)
                {
                    $('#formeJuridique').append("<tr id="+'tr'+cpt+"><td>"+forme+"</td><td><a href='javascript:void(0);' onclick='supprimerCategory("+cpt+")'>supprimer</a></td></tr>")
                    cpt++;
                }
            },
            error: function(resultat, statut, erreur) {
                console.log(resultat,statut,erreur);
            }
        })
    }
});

function changeState(id)
{
    $.ajax({
        url : 'init_state/',
        dataType : 'json',
        method: "post",
        data : {
            'id' : id
        },
        success : function(json){
            $.each(json.formes,function(index,value){
                $('#formeJuridique').append("<tr id="+'tr'+cpt+"><td>"+value+"</td><td><a href='javascript:void(0);' onclick='supprimer_state("+cpt+")'>supprimer</a></td></tr>")
                cpt++;
            })
        },
        error: function(resultat, statut, erreur) {
            console.log(resultat,statut,erreur);
        }
    })
}



function supprimer_state(cpttr)
{
    var code =  $('#form_state').val();
    var forme = $("#tr"+cpttr).children(":first").text();
    $.ajax({
        url : 'delstate/',
        dataType : 'json',
        method: "post",
        data : {
            code : code,
            forme : forme
        },
        success : function(json){
            $('#tr'+cpttr).remove();
        },
        error: function(resultat, statut, erreur) {
            console.log(resultat,statut,erreur);
        }
    })
}

function changeNace(id)
{
    $.ajax({
        url : 'init_nace/',
        dataType : 'json',
        method: "post",
        data : {
            'id' : id
        },
        success : function(json){
            $.each(json.naces,function(index,value){
                $('#formeJuridique').append("<tr id="+'tr'+cpt+"><td>"+value+"</td><td><a href='javascript:void(0);' onclick='supprimerNace("+cpt+")'>supprimer</a></td></tr>")
                cpt++;
            })
        },
        error: function(resultat, statut, erreur) {
            console.log(resultat,statut,erreur);
        }
    })
}

function changeCategory(id)
{
    $.ajax({
        url : 'init_category/',
        dataType : 'json',
        method: "post",
        data : {
            'id' : id
        },
        success : function(json){
            $.each(json.formes,function(index,value){
                $('#formeJuridique').append("<tr id="+'tr'+cpt+"><td>"+value+"</td><td><a href='javascript:void(0);' onclick='supprimerCategory("+cpt+")'>supprimer</a></td></tr>")
                cpt++;
            })
        },
        error: function(resultat, statut, erreur) {
            console.log(resultat,statut,erreur);
        }
    })
}

function changeQuality(id)
{
    $.ajax({
        url : 'init_quality/',
        dataType : 'json',
        method: "post",
        data : {
            'id' : id
        },
        success : function(json){
            $.each(json.formes,function(index,value){
                $('#formeJuridique').append("<tr id="+'tr'+cpt+"><td>"+value+"</td><td><a href='javascript:void(0);' onclick='supprimerQuality("+cpt+")'>supprimer</a></td></tr>")
                cpt++;
            })
        },
        error: function(resultat, statut, erreur) {
            console.log(resultat,statut,erreur);
        }
    })
}

function supprimerNace(cpttr)
{
    var id =  $('#form_juridique').val();
    var nace = $("#tr"+cpttr).children(":first").text();
    $.ajax({
        url : 'delNace/',
        dataType : 'json',
        method: "post",
        data : {
            id : id,
            nace : nace
        },
        success : function(json){
            $('#tr'+cpttr).remove();
        },
        error: function(resultat, statut, erreur) {
            console.log(resultat,statut,erreur);
        }
    })
}


function supprimerCategory(cpttr)
{
    var id =  $('#form_category').val();
    var forme = $("#tr"+cpttr).children(":first").text();
    $.ajax({
        url : 'delcategory/',
        dataType : 'json',
        method: "post",
        data : {
            id : id,
            forme : forme
        },
        success : function(json){
            $('#tr'+cpttr).remove();
        },
        error: function(resultat, statut, erreur) {
            console.log(resultat,statut,erreur);
        }
    })
}

function supprimerQuality(cpttr)
{
    var id =  $('#form_quality').val();
    var forme = $("#tr"+cpttr).children(":first").text();
    $.ajax({
        url : 'delQuality/',
        dataType : 'json',
        method: "post",
        data : {
            id : id,
            forme : forme
        },
        success : function(json){
            $('#tr'+cpttr).remove();
        },
        error: function(resultat, statut, erreur) {
            console.log(resultat,statut,erreur);
        }
    })
}

