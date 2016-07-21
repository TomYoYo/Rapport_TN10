var ti_refreshSab = false;


function synchro(urlSynchro) {
    $.ajax({
        dataType: "json",
        type: "GET",
        url: urlSynchro
    });
}

function refreshArboSAB(urlNodes)
{
    console.log(urlNodes);
    console.log(ti_refreshSab);
    $('.bubblingG').show();
    $('#tree_sab').hide();

    $.ajax({
        dataType: "json",
        type: "GET",
        url: urlNodes,
        success: function (response) {
            console.log(response);
            switch (response.lastSynchro.etat) {
                case 'attente':
                    $('#status').html('La synchronisation des fichiers doit commencer dans quelques instants.');
                    if (!ti_refreshSab) {
                        lanceRefreshSabAuto(urlNodes);
                    }
                    break;
                case 'encours':
                    $('#status').html('Synchronisation en cours. Veuillez patienter.');
                    if (!ti_refreshSab) {
                        lanceRefreshSabAuto(urlNodes);
                    }
                    break;
                case 'KO':
                    if (ti_refreshSab) {
                        clearInterval(ti_refreshSab);
                        ti_refreshSab = false;
                    }
                    $('#btn-synchro').hide();
                    $('.bubblingG').hide();
                    $('#tree_sab').show();
                    $('.ztree').html('Une erreur est survenue. Merci de rééssayer ultérieurement. Si le problème persiste, contactez le SI Banque.');

                    break;
                default:
                    $('#status').html('Dernière synchronisation le ' + response.lastSynchro.date);
                    if (ti_refreshSab) {
                        clearInterval(ti_refreshSab);
                        ti_refreshSab = false;
                    }

                    var zTreeObj;
                    // zTree configuration information, refer to API documentation (setting details)
                    var setting = {
                        view: {dblClickExpand: false},
                        data: {simpleData: {enable: true}},
                        callback: {onClick: onClick}
                    };

                    $('.bubblingG').hide();
                    $('#tree_sab').show();
                    // zTree data attributes, refer to the API documentation (treeNode data details)
                    zTreeObj = $.fn.zTree.init($("#tree_sab"), setting, response.tree);
                    break;
            }
        }
    });
}

function lanceRefreshSabAuto(urlNodes) {
    ti_refreshSab = setInterval("refreshArboSAB('" + urlNodes + "')", 3000);
}

function onClick(e, treeId, treeNode) {
    var zTree = $.fn.zTree.getZTreeObj("treeExchange");
    zTree.expandNode(treeNode);
}
