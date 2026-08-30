{{-- Summernote rich HTML editor (Bootstrap 3 compatible) --}}
<link href="{{ asset('vendor/summernote/summernote.min.css') }}" rel="stylesheet">
<script src="{{ asset('vendor/summernote/summernote.min.js') }}"></script>
<script>
window.initAbstractSummernote = function (selector, height) {
    height = height || 260;
    if (!$.fn.summernote) return;
    $(selector).each(function () {
        var $el = $(this);
        if ($el.next('.note-editor').length) {
            $el.summernote('destroy');
        }
        $el.summernote({
            height: height,
            dialogsInBody: true,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
                ['fontname', ['fontname']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'hr']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });
    });
};
window.destroyAbstractSummernote = function (selector) {
    if (!$.fn.summernote) return;
    $(selector).each(function () {
        if ($(this).next('.note-editor').length) {
            $(this).summernote('destroy');
        }
    });
};
</script>
