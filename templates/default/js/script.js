/**
 * [description]
 * @param  {[type]} - question, warning, error, info, success
 */
$(document).ready(function(){
  const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 9000
  });

  if (sweetalert) {
    Toast.fire({
      type: sweetalert,
      title: sweet_title
    })
  }
}); 
