jQuery(document).ready(function($){


    $("#btnuevo").click(function(){
        $("#modalnuevo").modal("show");
        console.log("estoy aqui");
    });

    var i = 1;
    $("#add").click(function () {  
        i++;add
        $("#camposdinamicos").append('<tr id="row'+i+'"><td><label for="txtnombre" class="coo-form-label" style="margin-left: 5px;">Pregunta '+i+'</label></td><td><input type="text" name="name[]" id="name" class="form-control name_list"></td><td><select name="type[]" id="type" class="form-control type_list"><option value="1" select>SI - NO</option><option value="2">Rango 0 -5</option></select></td><td><button name="remove" id="'+i+'" class="btn btn-danger btn_remove" style="margin-left: 5px;">X</button></td></tr>');
        return false;

    });

$(document).on('click','.btn_remove',function(){  
    var button_id = $(this).attr('id');
    $("#row" + button_id+"").remove();
});

$(document).on('click',"a[data-id]",function () {
   var id = this.dataset.id;
   var url = SolicitudesAjax.url;
   $.ajax({
    type: "POST",
    url: url,
    data:{
        action: "peticioneliminar",
        nonce: SolicitudesAjax.seguridad,
        id: id,
    },
    success:function () {  
        location.reload();
    
    }
    
   });
});

$(document).on('click',"a[data-ver]",function(){
    $("#modalestadisticas").modal("show");
})
});