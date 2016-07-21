$( document ).ready( function (){
    // placeholder pour ie9
    $('input, textarea').placeholder();
    
    initRefresh('testOracleBFI');
    initRefresh('testOracleSAB');
    initRefresh('testOracleTitre');
    initRefresh('testTrigger');
    
    initRefresh('testSabCore');
    initRefresh('testWindows');
    initRefresh('testAngers');
    initRefresh('testSIT');
    
    initRefresh('testSurveillanceTrigger');
    initRefresh('testSurveillanceFichiers');
});

function initRefresh(id){
    $("#"+id).on('click', 'img.refresh', function(){
        $.ajax({
            url:id,
            method:"POST"
        }).done(function(data ) {
            $('#'+id).html(data);
        });
   });
}
