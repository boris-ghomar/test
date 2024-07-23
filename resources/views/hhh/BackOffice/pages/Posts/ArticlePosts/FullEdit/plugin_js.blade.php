
<script src="{{ url('assets/site/template/vendors/quill/quill.min.js') .'?version='. config('hhh_config.ResourceVersion') }}"></script>

<script>
    $(document).ready(function() {
        /*Quill editor*/

        if ($("#quillContent").length) {

            /* var fonts = ['manrope']; */
            var fonts = ['manrope','sofia', 'slabo', 'roboto', 'inconsolata', 'ubuntu'];
           /*  var FontAttributor = Quill.import('attributors/class/font');
            FontAttributor.whitelist = fonts;
            Quill.register(FontAttributor, true); */

            var quill = new Quill('#quillContent', {
                modules: {
                    toolbar: [
                        [{header: [2, 3, 4, 5, 6, false]}],
                        /* [{'font': fonts}, {'size': []}], */
                        [{'size': []}],
                        ['bold', 'italic', 'underline', 'strike'],
                        ['link', 'image', 'video', 'code-block'],
                        [{'direction': 'rtl'}, {'align': []}],
                        [{'color': []}, {'background': []}],
                        [{'script': 'super'}, {'script': 'sub'}],
                        [{'list': 'ordered'}, {'list': 'bullet'}, {'indent': '-1'}, {'indent': '+1'}],
                        ['clean']
                    ]
                },
                placeholder: '{{ __('PagesContent_PostForm.form.content.placeholder') }}',
                theme: 'snow' // or 'bubble'
            });


            /*Triger*/
            var inputId = "content";
            var contentInput = document.getElementById(inputId);
            var contentHtmlInput = document.getElementById(inputId + "_html");

            quill.on('text-change', function(delta, oldDelta, source) {

                contentInput.value = JSON.stringify(quill.getContents());
                contentHtmlInput.value = quill.root.innerHTML;
            });

            /*Load data*/
            quill.setContents({!! $editorData !!});
        }
    });
</script>
