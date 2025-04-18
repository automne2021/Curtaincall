/**
 * CKEditor initialization script
 * This file handles the initialization of CKEditor for rich text fields
 */
document.addEventListener('DOMContentLoaded', function() {
    // Initialize CKEditor on any element with the 'ckeditor' class
    const editorElements = document.querySelectorAll('.ckeditor');
    
    if (editorElements.length > 0) {
        console.log('Found ' + editorElements.length + ' editor elements');
        
        // Try loading from CDN if CKEditor is not already loaded
        if (typeof ClassicEditor === 'undefined') {
            console.log('Loading CKEditor from CDN...');
            
            const script = document.createElement('script');
            script.src = 'https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/ckeditor.js';
            script.onload = initializeEditors;
            document.head.appendChild(script);
        } else {
            // CKEditor already loaded, initialize editors directly
            initializeEditors();
        }
    }
    
    function initializeEditors() {
        editorElements.forEach((element, index) => {
            console.log('Initializing editor #' + index);
            
            ClassicEditor
                .create(element, {
                    toolbar: {
                        items: [
                            'heading',
                            '|',
                            'bold',
                            'italic',
                            'underline',
                            'strikethrough',
                            '|',
                            'bulletedList',
                            'numberedList',
                            '|',
                            'indent',
                            'outdent',
                            '|',
                            'link',
                            'blockQuote',
                            'insertTable',
                            '|',
                            'undo',
                            'redo'
                        ],
                        shouldNotGroupWhenFull: true
                    },
                    placeholder: element.getAttribute('data-placeholder') || 'Enter content here...',
                    // Add content filtering rules to ensure all HTML elements work correctly
                    htmlSupport: {
                        allow: [
                            {
                                name: /.*/,
                                attributes: true,
                                classes: true,
                                styles: true
                            }
                        ]
                    }
                })
                .then(editor => {
                    console.log('CKEditor initialized successfully');
                    // Store the editor instance on the original textarea
                    element.ckeditorInstance = editor;
                })
                .catch(error => {
                    console.error('CKEditor initialization error:', error);
                    element.style.minHeight = '300px';
                });
        });
    }
});