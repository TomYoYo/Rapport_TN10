/**
 * Created by t.pueyo on 25/03/2016.
 */

var cpt = 0;
var groupe = 2;
var lib = '';
jQuery.xhrPool = [];

    $(document).ajaxSend(function(event, jqXHR, options) {
        jQuery.xhrPool.push(jqXHR);
    });
    $(document).ajaxComplete(function(event, jqXHR, options) {
        jQuery.xhrPool.remove(jqXHR);
    });

    jQuery.xhrPool.abortAll = function() {
        var requests = [];
        for (var index in this) {
            if (isFinite(index) === true) {
                requests.push(this[index]);
            }
        }
        for (index in requests) {
            requests[index].abort();
        }
    };
    jQuery.xhrPool.remove = function(jqXHR) {
        for (var index in this) {
            if (this[index] === jqXHR) {
                jQuery.xhrPool.splice(index, 1);
                break;
            }
        }
    };

$(document).ready(function(){
   $("#form_groupe").change(function(){
       jQuery.xhrPool.abortAll()
       $("#accordion").html("");
       if($('#form_groupe').val() != null)
       {
           menu = $("#form_groupe option:selected").text();
           valMenu = $("#form_groupe option:selected").val();
           if(menu != 'Choisissez une valeur')
           {
           if(groupe == 2)
           {
               $('#accordion').append("<h1 style='text-align: center'>Menus des "+menu+"</h1>")
               getMenus(0,menu,'accordion',0);
               cpt = 0;
           }else if(groupe == 3)
           {
               $('#accordion').append("<h1 style='text-align: center'>Données des "+menu+"</h1>")
               getDonnees(valMenu);
               cpt = 0;
           }else
           {
               $('#accordion').append("<h1 style='text-align: center'>Métier des "+menu+"</h1>")
               $('#accordion').append("<p style='text-align: center'>Consulter le détail sous SAB</p>")

           }
           }
       }
   })
});



    function getMenus(search,menu,idmenu){
        var id = '#' + idmenu;
        if($(id).hasClass("ui-accordion-content-active"))
        {
            $(id).html('');
        }
        else
        {
            $.ajax({
                url:'detailMenu/',
                method:"post",
                data:{'menu' : menu,'parent' : search},
                dataType: 'json',

                success: function(json){// quand la réponse de la requete arrive

                    if(json.length!=0)
                    {
                        var lib_menu = "Menu_"+cpt;
                        var id_menu = "#"+lib_menu;
                        $(id).append("<ul id='"+lib_menu+"'>")
                        $.each(json, function(index,value) {
                            var lib_li = lib_menu + "_li_"+index;
                            var id_li = "#"+lib_li;
                            var zob = value.CODE;
                            var fonc = "getMenus("+value.CODE+",&quot;"+menu+"&quot;,&quot;"+lib_li+"&quot;)";
                            $(id_menu).append("<li><h3 onclick='"+fonc+"'><a href='#' >"+value.LIB+"</a></h3><div id='"+lib_li+"'></div></li>");

                        });
                        $(id).append('</ul>');
                        $(id_menu).accordion({collapsible: true, active: false});
                        cpt++;
                    }
                    else
                    {
                        $(id).remove();
                    }

                },
                error: function(resultat, statut, erreur) {
                    $(id).append("<h3 style='text-alignt: center'>Pas de menu pour les "+menu+", contactez votre administrateur</h3>")
                    console.log(resultat,statut,erreur);
                }

            })
        }
    }

function getDonnees(menu){
    console.log(menu)
    var lib_menu = "Menu";
    var id_menu = "#"+lib_menu;
    console.log(id_menu)

    $.ajax({
        url:'detailDonnees/',
        method:"post",
        data:{'menu' : menu},
        dataType: 'json',

        success: function(json){// quand la réponse de la requete arrive


            $('#accordion').append("<ul id='"+lib_menu+"'>")
            $.each(json, function(index,value) {
                console.log(value)
                var lib = '';
                var abbr1='';
                var abbr2='';
                if(value.ABBR1 != null)
                {
                    abbr1 = value.ABBR1
                }
                else
                {
                    abbr1 = value.MNUOPTENS.substr(0,3)
                }
                if(value.ABBR2 != null)
                {
                    abbr2 = value.ABBR2
                }
                else
                {
                    abbr2 = value.MNUOPTENS.substr(3)
                }
                if(value.ABBR2 == null && value.ABBR1 == null)
                {
                    lib = value.MNUOPTENS
                }
                else
                {
                    lib = abbr1 + ' ' + abbr2;
                }
                var lib_li = "li_"+index;
                var id_li = '#' + lib_li;
                var fonc = "getSubData(&quot;"+value.MNUOPTENS+"&quot;,"+menu+",&quot;"+id_li+"&quot;)";
                $(id_menu).append("<li><h3 onclick='"+fonc+"'><a href='#' >"+lib+"</a></h3><div id='"+lib_li+"'></div></li>");
            })
            $('#accordion').append("</ul>");
            $(id_menu).accordion({collapsible: true, active: false});

        },
        error: function(resultat, statut, erreur) {
            console.log(resultat,statut,erreur);
        }
    })
}


function getSubData(lib,menu,id)
{
    $("#Menu > li").each(function() {
        $(this).find('> div').html('');
    })
    $.ajax({
        url:'detailDonneesEach/',
        method:"post",
        data:{'donnee' : lib,'menu' : menu},
        dataType: 'json',

        success: function(json){// quand la réponse de la requete arrive
            $(id).append("<ul>");
            $(id).height(json.length * 20);
            $.each(json, function(index,value) {
                $(id).append("<li>"+value.MNUOPTLIB+"</li>");
            });
            $(id).append("</ul>");

        },
        error: function(resultat, statut, erreur) {
            console.log(resultat,statut,erreur);
        }


    })
}


$(document).ready(function(){
    if($(document).attr('title') == 'Details')
    {
        $('#menuu').css('color', 'red');
        getChoices(2);
        groupe = 2
    }
});

function getChoices(groupe){
    $('#form_groupe').prop('disabled',true)
    $.ajax({
        url: 'choiceGroupe/',
        method : "post",
        data:{groupe : groupe},
        dataType : 'json',
        success: function(json){// quand la réponse de la requete arrive
            $('#form_groupe').html('');
            $('#form_groupe').append("<option value=''>Choisissez une valeur</option>")
            $.each(json, function(index,value) {
                $('#form_groupe').append("<option value='"+index+"'>"+value.MNUGRPNOM+"</option>");
            })
            $('#form_groupe').prop('disabled',false)


        },
        error: function(resultat, statut, erreur) {
            console.log(resultat,statut,erreur);
        }
    })
}

$("#metier").click(function(){
    jQuery.xhrPool.abortAll()
    $("#accordion").html("");

    $('#menuu').css('color', '#428bca');
    $('#donnees').css('color', '#428bca');
    $('#metier').css('color', 'red');
    getChoices(4);
    groupe = 4;
   /* while (jQuery.xhrPool.length != 0)
    {
         $('#form_groupe').prop('disabled',true)

    }
    $('#form_groupe').prop('disabled',false)
*/
})


$("#menuu").click(function(){
    jQuery.xhrPool.abortAll()
    $("#accordion").html("");

    $('#menuu').css('color', 'red');
    $('#donnees').css('color', '#428bca');
    $('#metier').css('color', '#428bca');
    getChoices(2);
    groupe = 2
})


$("#donnees").click(function(){
    jQuery.xhrPool.abortAll()
    $("#accordion").html("");

    $('#menuu').css('color', '#428bca');
    $('#donnees').css('color', 'red');
    $('#metier').css('color', '#428bca');
    getChoices(3);
    groupe = 3
})

/*$(function() {
 $( "#progressbar" ).progressbar({
 value: false
 });
});*/