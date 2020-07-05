window.love = function (element) {
  let postID = $(element).data('post-id');
  console.log('postID', postID);
  $.ajax({
    url: '/love/' + postID,
    type: 'POST',
    processData: false,
    contentType: false,
    async: true,
    crossDomain: false,
    success: function (response) {
      const btnLove = $('#btn-love-count-' + postID);
      btnLove.html(parseInt(btnLove.html()) + 1);
      $('#love-' + postID).addClass('btn-loved');
    }
  });
};
