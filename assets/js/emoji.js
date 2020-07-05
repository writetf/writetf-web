import {listEmojiImage} from "./emoji-img";

export function emoji(listEmoji, listEmojiInner) {
  $('.emoji-box').each(function () {
    let $this = $(this);
    const id = $(this).attr("id");
    let htmlEmoji = '';
    if (!listEmoji.includes(id)) {
      listEmoji.push(id);
      // listCss.forEach(function (value, index, array) {
      //   htmlEmoji += '<i class="emoji ' + value + ' clickable" data-class="' + value + '"/>';
      // });
      listEmojiImage.appleFaces.forEach(function (value, index, array) {
        htmlEmoji += '<img src="' + value + '" width="25" height="25"/>';
      });
      $this.html(htmlEmoji);
    }
  });

  $('.emoji-box img').click(function (e) {
    let $this = $(this);
    let parent = $($this.parent());
    const innerParentId = parent.attr('id');
    if (innerParentId === 'emoji-create-post') {
      let postContentObject = $('#post-content');
      let value = $this.attr('src');
      let postContent = postContentObject.html();
      postContent += '<img src="' + value + '" width="25" height="25"/>';
      postContentObject.html(postContent);
      $('#short_post_title').val(postContent);
      postContentObject.focus();
      document.execCommand('selectAll', false, null);
      document.getSelection().collapseToEnd();
    }
  })
}
