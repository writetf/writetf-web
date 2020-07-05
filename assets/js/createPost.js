import '../scss/createPost.scss';

function readURL(input) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    reader.onload = function (e) {
      $('#preview').css('display', 'block');
      $('#custom-post-type').css('display', 'none');
      $('#imagePreview').attr("src", e.target.result);
      $('#post-title').attr("placeholder", "Say something about this photo...");
      $('#imagePreview').hide();
      $('#imagePreview').fadeIn(650);
    }
    reader.readAsDataURL(input.files[0]);
    let form = new FormData();
    let file = input.files[0];
    console.log('file', file);
    form.append("image", file);
    $.ajax({
      url: '/ajax/image/upload',
      type: 'POST',
      data: form,
      processData: false,
      contentType: false,
      async: true,
      crossDomain: true,
      enctype: 'multipart/form-data',
      success: function (response) {
        $('#imagePreview').attr("src", response.path);
        $('#upload-loading').css("display", "none");
        $('#short_post_thumbnail').val(response.path);
        $('#submit-post').css("display", "block");
      }
    });
  }
}

$("#customImage").change(function () {
  readURL(this);
});


$("#close-preview").click(function () {
  $('#preview').css('display', 'none');
  $('#custom-post-type').css('display', 'flex');
});

$("#post-content").focusin(function (event) {
  $("#submit-post").css('display', 'block');
  $('#custom-post-type').css('display', 'flex');
}).focusout(function () {
  let element = $(this);
  if (!element.html().replace(" ", "").length) {
    element.empty();
  }
}).blur(function () {
  let $this = $(this);
  $('#short_post_title').val($this.html());
}).keydown(function (e) {
  // trap the return key being pressed
  if (e.keyCode === 13) {
    window.document.execCommand('insertLineBreak', false, null);
    e.preventDefault();
    return false;
  }
});
