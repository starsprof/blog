function showEditDialog(id) {
    $('.modal-content').load('/admin/tags/ajax-edit/'+id,function(){
        $('#modal').modal({show:true});
    });
}


function showAddDialog() {
    $('.modal-content').load('/admin/tags/ajax-add',function(){
        $('#modal').modal({show:true});
    });
}


function addTag() {
    let title = $("#inputTile");
    let slug = $("#inputSlug");
    event.preventDefault();
    $.ajax({
        type: "POST",
        url: "/admin/tags/ajax-add",
        data: {
            title : title.val(),
            slug: slug.val(),
        },
        success : function(response){
            if(response.status == 'success'){
                location.reload();
            }else {
                showValidationErrors(response);
            }
        }
    })
}

function editTag() {
    let title = $("#inputTile");
    let slug = $("#inputSlug");
    event.preventDefault();
    $.ajax({
        type: "POST",
        url: "/admin/tags/ajax-edit",
        data: {
            title: title.val(),
            slug: slug.val(),
            id: $('#hiddenId').val()
        },
        success: function (response) {
            if (response.status == 'success') {
                location.reload();
            } else {
                showValidationErrors(response);
            }
        }
    });
}

function showValidationErrors(response) {
    if(typeof response.errors.title === 'undefined')
    {
        $("#inputTile").removeClass('is-invalid');
        $("#inputTile").addClass('is-valid');
    }else {
        $('#titleError').html(response.errors.title);
        $("#inputTile").removeClass('is-valid');
        $("#inputTile").addClass('is-invalid');
    }
    if(typeof response.errors.slug === 'undefined')
    {
        $("#inputSlug").removeClass('is-invalid');
        $("#inputSlug").addClass('is-valid');
    }else {
        $('#slugError').html(response.errors.slug);
        $("#inputSlug").removeClass('is-valid');
        $("#inputSlug").addClass('is-invalid');
    }
}

function confirmTagDeleteSubmit (form, title) {
    swal({
        title: "Remove tag",
        text: title,
        icon: "error",
        closeOnEsc: false,
        dangerMode: true,
        closeModal: false,
        buttons: {
            cancel: 'Cancel',
            confirm: 'Remove',
        },
        closeOnClickOutside: false,

    }).then((value) => {
        if(!!value){
            form.submit();
        }
    });
}