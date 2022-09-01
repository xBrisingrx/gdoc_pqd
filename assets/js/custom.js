function noty_alert( type, msg, time = 2000) {
  new Noty({
      theme: 'bootstrap-v4',
      type: type,
      layout: 'topRight',
      text: msg,
      timeout: (true, time)
  }).show();
}

function setInputDate(_id){
    var _dat = document.querySelector(_id);
    var hoy = new Date(),
        d = hoy.getDate(),
        m = hoy.getMonth()+1, 
        y = hoy.getFullYear(),
        data;

    if(d < 10) { d = "0"+d }

    if(m < 10) { m = "0"+m }

    data = y+"-"+m+"-"+d;
    _dat.value = data;
}

function clean_form(form_id) {
  $(`#${form_id} .form-control`).removeClass('error')
  $(`#${form_id} .error`).empty()
  $(`#${form_id}`)[0].reset()
}

// con esta fn lo que hago es mostrar los archivos en el modal de archivos
function dibujar_archivo( div_id, archivo_id, archivo_ruta){
  $(`#${div_id}`).append(`
    <div class='col-2 row mx-auto mt-2' id='archivo_${archivo_id}'>
      <div class='col-12 mb-2'> 
        <a href='${archivo_ruta}' target="_blank" class="text-center px-1 py-1"> <i class="fa fa-file-pdf-o fa-2x"></i></a>
      </div>
      <div>
        <button class="btn btn-xs u-btn-red" onclick="modal_delete_archivo('${archivo_id}')"> borrar</button>
      </div>
    </div>
  `).show('slow')
}