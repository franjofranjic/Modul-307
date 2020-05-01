function tanken() {
    console.log('tanken');
}

function edit() {
    console.log('edit');
}

function remove() {
    console.log('remove');
}

function addVehicle() {
    console.log('add Vehicle');
    window.alert('add Vehicle');
    window.confirm('add Vehicle');
}

// document.addEventListener('DOMContentLoaded', function() {
//     var elems = document.querySelectorAll('.modal');
//     var instances = M.Modal.init(elems, options);
//   });

$(document).ready(function() {
    console.log('ready!');
    $('.modal').modal();

    $("#addBtn").click(function() {
        console.log('add Vehicle');
        var mymodal = M.Modal.getInstance($('.modal'));
        mymodal.open();
        var id = $(this).parent().attr('data-id');
        $('#modaltitle').html('Auto hinzufügen');
        $('#modalinhalt').load('sites/formular.html')
    });
    
    $('.editBtn').click(function() {
        console.log('edit');
        var mymodal = M.Modal.getInstance($('.modal'));
        mymodal.open();
        var id = $(this).parent().attr('data-id');
        $('#modaltitle').html('Auto bearbeiten: ' + id);
        $('#modalinhalt').load('sites/formular.html')
    });
    
    $('.tankenBtn').click(function() {
        var id = $(this).parent().attr('data-id');
        console.log('tanken von: ' + id);
    });
    
    $('.removeBtn').click(function() {
        var id = $(this).parent().attr('data-id');
        console.log('löschen von: ' + id);
    });
});

