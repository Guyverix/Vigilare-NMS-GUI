  var type = 'fa'

  function loadingTemplate(message) {
    if (type === 'fa') {
      return '<i class="fa fa-spinner fa-spin fa-fw fa-2x"></i>'
    }
    if (type === 'pl') {
      return '<div class="ph-item"><div class="ph-picture"></div></div>'
    }
  }


<!--
window.setTimeout(function() {
  window.location.reload();
}, 5000);
-->
