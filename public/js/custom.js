$(document).ready(function () {
  window._token = $('meta[name="csrf-token"]').attr('content');


  var allEditors = document.querySelectorAll('.ckeditor');
  for (var i = 0; i < allEditors.length; ++i) {
    ClassicEditor.create(
        allEditors[i],
        {
            removePlugins: ['ImageUpload']
        }
    );
  }

  $('.nav-link.active').closest('.nav-dropdown').addClass('open');


  //$('.datatable-loading').parent().prepend('<div class="datatable-loader">  <i class="fas fa-circle-notch fa-spin"></i></div>');

  moment.updateLocale('es', {
    week: {dow: 1} // Monday is the first day of the week
  });



  $('.date').datetimepicker({
    format: 'DD/MM/YYYY',
    locale: 'es'
  });

  $('.datetime').datetimepicker({
    format: 'DD/MM/YYYY HH:mm',
    locale: 'es',
    sideBySide: true
  });

  $('.timepicker').datetimepicker({
    format: 'HH:mm:ss'
  });

  $('.select-all').click(function () {
    let $select2 = $(this).parent().siblings('.to-select2')
    $select2.find('option').prop('selected', 'selected')
    $select2.trigger('change')
  });
  $('.deselect-all').click(function () {
    let $select2 = $(this).parent().siblings('.to-select2')
    $select2.find('option').prop('selected', '')
    $select2.trigger('change')
  });

    $('.to-select2').select2();



  $('.treeview').each(function () {
    var shouldExpand = false
    $(this).find('li').each(function () {
      if ($(this).hasClass('active')) {
        shouldExpand = true
      }
    })
    if (shouldExpand) {
      $(this).addClass('active')
    }
  })
})


// For todays date;
Date.prototype.today = function () {
  return ((this.getDate() < 10)?"0":"") + this.getDate() +"/"+(((this.getMonth()+1) < 10)?"0":"") + (this.getMonth()+1) +"/"+ this.getFullYear();
}

// For the time now
Date.prototype.timeNow = function () {
  return ((this.getHours() < 10)?"0":"") + this.getHours() +":"+ ((this.getMinutes() < 10)?"0":"") + this.getMinutes() +":"+ ((this.getSeconds() < 10)?"0":"") + this.getSeconds();
};


function addZero(i) {
  if (i < 10) {
    i = "0" + i;
  }
  return i;
}

function formatDate(date){
  return  addZero(date.getDate()) + '/' + addZero(date.getMonth()+1) + '/' + date.getFullYear() + ' '
      + addZero(date.getHours()) + ':' + addZero(date.getMinutes())
}