$('.comment-utils .btn-reply').click(function (e) {
  let $this = $(this);
  const commentId = $this.data('comment-id');
  const replyTo = $this.data('reply-to');
  let commentReplyInput = $("textarea[name=\'comment[content]\']", '#comment-reply-' + commentId);
  commentReplyInput.val('@' + replyTo + ' ');
  commentReplyInput.focus();
});
