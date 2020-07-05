import '../scss/admin.scss';
import 'eonasdan-bootstrap-datetimepicker';
import 'typeahead.js';
import Bloodhound from "bloodhound-js";
import 'bootstrap-tagsinput';
import 'bootstrap-select/dist/js/bootstrap-select.min';

import 'codemirror/lib/codemirror.css';
import 'tui-editor/dist/tui-editor.css';
import 'tui-editor/dist/tui-editor-contents.css';
import 'highlight.js/styles/github.css';


import Editor from 'tui-editor';

const editorSection = document.getElementById('editorSection');
if (editorSection) {
    const instance = new Editor({
        el: editorSection,
        initialEditType: 'wysiwyg',
        previewStyle: 'vertical',
        height: 'auto',
        hooks: {
            addImageBlobHook: function (file, callback) {
                let form = new FormData();
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
                        callback(response.path, file.name);
                    }
                });
            }
        },
        initialValue: $('#post_content').val()
    });
    $('#post-submit').click(function () {
        $('#post_content').val(instance.getValue());
        $('#postForm').submit();
    });
}

$(function() {
    // Datetime picker initialization.
    // See http://eonasdan.github.io/bootstrap-datetimepicker/
    $('[data-toggle="datetimepicker"]').datetimepicker({
        icons: {
            time: 'fa fa-clock-o',
            date: 'fa fa-calendar',
            up: 'fa fa-chevron-up',
            down: 'fa fa-chevron-down',
            previous: 'fa fa-chevron-left',
            next: 'fa fa-chevron-right',
            today: 'fa fa-check-circle-o',
            clear: 'fa fa-trash',
            close: 'fa fa-remove'
        }
    });

    // Bootstrap-tagsinput initialization
    // http://bootstrap-tagsinput.github.io/bootstrap-tagsinput/examples/
    var $input = $('input[data-toggle="tagsinput"]');
    if ($input.length) {
        var source = new Bloodhound({
            local: $input.data('tags'),
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            datumTokenizer: Bloodhound.tokenizers.whitespace
        });
        source.initialize();

        $input.tagsinput({
            trimValue: true,
            focusClass: 'focus',
            typeaheadjs: {
                name: 'tags',
                source: source.ttAdapter()
            }
        });
    }
});

// Handling the modal confirmation message.
$(document).on('submit', 'form[data-confirmation]', function (event) {
    var $form = $(this),
        $confirm = $('#confirmationModal');

    if ($confirm.data('result') !== 'yes') {
        //cancel submit event
        event.preventDefault();

        $confirm
            .off('click', '#btnYes')
            .on('click', '#btnYes', function () {
                $confirm.data('result', 'yes');
                $form.find('input[type="submit"]').attr('disabled', 'disabled');
                $form.submit();
            })
            .modal('show');
    }
});
