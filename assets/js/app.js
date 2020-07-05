import '../scss/app.scss';
import '../scss/loading.scss';
import '../scss/responsive.scss';
import '../scss/emoji.scss';

import 'bootstrap/dist/js/bootstrap';

import '../images/100-offline-sprite.png';
import '../images/200-offline-sprite.png';
import '../images/100-error-offline.png';
import '../images/200-error-offline.png';
import '../images/emoji.png';

import {emoji} from '../js/emoji';
import './comment';

import 'popper.js/dist/popper.min';

$(document).ready(function () {
  let page = 1;
  let action = 'active';
  let listEmoji = [];
  let listEmojiInner = [];

  emoji(listEmoji, listEmojiInner);

  $(window).scroll(function () {
    const body = $('body');
    if (($(window).scrollTop() + $(window).height()) >= $(document).height() / 2 && action === 'active' && (body.is("#index") || body.is("#community") || body.is("#user"))) {
      page = page + 1;
      action = 'inactive';
      $('#post-loading').html('<div class="lds-ripple"><div></div><div></div></div>');
      loadData(page);
      dropdown();
      emoji(listEmoji, listEmojiInner);
    }
  });

  function loadData(pageNumber) {
    let baseUrl = window.location.href;
    if (baseUrl.charAt(baseUrl.length - 1) === '/') {
      baseUrl = baseUrl.substring(0, baseUrl.length - 1);
    }
    $.ajax({
      url: baseUrl + "/page/" + pageNumber,
      method: "GET",
      cache: false,
      dataType: "html",
      success: function (data) {
        $('#post-loading').html('');
        if (typeof $('#no-post-found', data).html() === 'undefined') {
          action = 'active';
          let content = $('#posts', data).html();
          $('#posts').append(content);
          $('.content', '#posts').each(function () {
            loadMoreCollapse(this);
          });
        } else {
          action = 'inactive';
          $('#posts').append('<div class="text-center mt-4 mb-4" style="width: 100%; color:#563d7b;"><span class="loaded"><i class="fas fa-check-circle"></i> All posts loaded</span></div>');
        }
      }
    });
  }

});


$(document).ready(function () {
  // Show hide popover
  dropdown();
});

$(document).click(function (e) {
  e.stopPropagation();
  const container = $(".dropdown-group");
  //check if the clicked area is dropDown or not
  if (container.has(e.target).length === 0) {
    $('.dropdown-data').hide();
  }
});

function dropdown() {
  $(".dropdown-group").click(function () {
    $(this).find(".dropdown-data").show();
  });
}

$('textarea[name=\'comment[content]\']').each(function () {
  this.setAttribute('style', 'height:' + (this.scrollHeight) + 'px;overflow-y:hidden;');
  $(this).keypress(function (e) {
    if (e.which === 13 && !e.shiftKey) {
      $(this).closest("form").submit();
      e.preventDefault();
    }
  });
}).on('input', function () {
  this.style.height = 'auto';
  this.style.height = (this.scrollHeight) + 'px';
});

$('#textarea-forget').each(function () {
  this.setAttribute('style', 'height:' + (this.scrollHeight) + 'px;overflow-y:hidden;');
}).on('input', function () {
  this.style.height = 'auto';
  this.style.height = (this.scrollHeight) + 'px';
});


$('.content').each(function () {
  loadMoreCollapse(this);
});

function loadMoreCollapse(element) {
  if ($(element).parent().hasClass('see-more')) {
    return true;
  }
  const height = $(element).height();
  if (height > 200) {
    $(element).parent().addClass('see-more');
    $(element).append('<div class="load-more-hidden"></div>');

    $(element).parent().append('<button class="see-more-btn" onclick="window.loadMore(this)">See more...</button>')
  }
  return true;
}

window.loadMore = function (element) {
  $(element).css('display', 'none');
  $('.content', $(element).parent()).css('height', 'auto');
  $('.load-more-hidden', $(element).parent()).css('display', 'none');
};
