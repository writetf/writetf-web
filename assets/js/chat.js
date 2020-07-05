import '../scss/chat.scss';

$("#chat")
  .mouseover(function () {
    $('body').css('overflow', 'hidden')
  })
  .mouseout(function () {
    $('body').css('overflow', 'inherit')
  });
