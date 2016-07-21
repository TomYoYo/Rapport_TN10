$(document).ready(function(){
    
    // init
    watch($('#obj_fin'));
    
    $('#obj_fin').keypress(function(){
        watch($(this));
    });

    $('.add_gar').click(function(){
        $('#sortable .form-group:hidden:first').show();
    });
});

function watch(elem) {
    var width = elem.val().length;
    
    if (width <= 8) {
        setProgress(0, 'progress-bar-success');
    } else if (width > 8 && width <= 200) {
        setProgress(5, 'progress-bar-success');
    } else if (width > 200 && width <= 350) {
        setProgress(25, 'progress-bar-warning');
    } else if (width > 350 && width <= 450) {
        setProgress(50, 'progress-bar-warning');
    } else if (width > 450 && width <= 500) {
        setProgress(75, 'progress-bar-danger');
    } else if (width > 500 && width <= 552) {
        setProgress(90, 'progress-bar-danger');
    } else if (width > 552) {
        setProgress(100, 'progress-bar-danger');
    }
    
    setCaract(width);
}

function setProgress(val, className) {
    $('.progress-bar').attr('aria-valuenow', val);
    $('.progress-bar').css('width', val+'%');
    $('.progress-bar').html(val+'%');
    
    $('.progress-bar').removeClass('progress-bar-danger');
    $('.progress-bar').removeClass('progress-bar-success');
    $('.progress-bar').removeClass('progress-bar-warning');
    
    $('.progress-bar').addClass(className);
}

function setCaract(width) {
    var msg = "Caractères : " + width + " / 552 (maximum)";
    $('#compteur').text(msg);
}

function autoGrow (oField) {
    if (oField.scrollHeight > oField.clientHeight) {
        oField.style.height = oField.scrollHeight + "px";
    }
}

function blockTextArea(textArea, maxRows, maxChars) {
    textArea.keypress(function(e){
        var text = $(this).val();
        var lines = text.split('\n');
        if (e.keyCode == 13) {
            return lines.length < maxRows;
        } else if (e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40 && e.keyCode != 46) {
            var caret = $(this).get(0).selectionStart;

            var line = 0;
            var charCount = 0;
            $.each(lines, function(i,e) {
                charCount += e.length;
                if (caret <= charCount) {
                    line = i;
                    return false;
                }
                charCount += 1;
            });

            var theLine = lines[line];
            return theLine.length < maxChars;
        }
    });
}

function saveAlert() {
    checkForm();
    if (formIsValid) {
        $('#submit .alert-danger').hide();
        return confirm('Souhaitez-vous valider définitivement les modifications effectuées ?');
    }
    
    $('#submit .alert-danger').show();
    return false;
}

function generateAlert() {
    checkForm();
    if (formIsValid) {
        $('#submit .alert-danger').hide();
        return confirm('Souhaitez-vous valider définitivement les modifications effectuées et générer l\'Editique ?');
    }
    
    $('#submit .alert-danger').show();
    return false;
}

function cancelAlert() {
    return confirm('Souhaitez-vous annuler définitivement les modifications effectuées ?');
}

function checkForm() {
    formIsValid = true;
    
    testField($('#villeNai'));
    testField($('#rcs'));
    testField($('#descEi'));
    testField($('#capital'));
    testFieldObj($('#obj_fin'));
    testField($('#dtDec'));
    testField($('#diffAmo'));
    testField($('#jourPre'));
    testField($('#ass1'));
    testField($('#nbExe'));
    testField($('#guarantee'));
    $('#dirigeants input.qualite').each(function(){
        testField($(this));
    });
}

function testField(field) {
    if (field.val() === "") {
        field.parent().parent().removeClass('has-success');
        field.parent().parent().addClass('has-error');
        formIsValid = false;
    } else {
        field.parent().parent().removeClass('has-error');
        field.parent().parent().addClass('has-success');
    }
}

function testFieldObj(field) {
    if (field.val() === "" || field.val().length > 552) {
        field.parent().parent().removeClass('has-success');
        field.parent().parent().addClass('has-error');
        formIsValid = false;
    } else {
        field.parent().parent().removeClass('has-error');
        field.parent().parent().addClass('has-success');
    }
}

$("#sortable").sortable();