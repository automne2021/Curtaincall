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
        const editorInstances = [];
        
        editorElements.forEach((element, index) => {
            console.log('Initializing editor #' + index);
            
            // Remove required attribute from textarea to prevent form validation issues
            element.removeAttribute('required');
            
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
                    
                    // Find the form that contains this editor
                    const form = element.closest('form');
                    if (form) {
                        // Add a custom validation function to the form
                        form.addEventListener('submit', function(e) {
                            // Update the textarea value with the editor content
                            element.value = editor.getData();
                            
                            // Check if the content is empty and the original element was required
                            if (element.hasAttribute('data-required') && editor.getData().trim() === '') {
                                // Show validation error
                                e.preventDefault();
                                
                                // Focus the editor
                                editor.editing.view.focus();
                                
                                // Show error message
                                let errorMsg = element.nextElementSibling;
                                if (!errorMsg || !errorMsg.classList.contains('invalid-feedback')) {
                                    errorMsg = document.createElement('div');
                                    errorMsg.classList.add('invalid-feedback');
                                    errorMsg.style.display = 'block';
                                    element.insertAdjacentElement('afterend', errorMsg);
                                }
                                errorMsg.textContent = 'This field is required';
                                
                                // Add invalid class to the editor
                                editor.editing.view.change(writer => {
                                    writer.addClass('is-invalid', editor.editing.view.document.getRoot());
                                });
                            }
                        });
                    }
                    
                    // Store the editor instance
                    element.ckeditorInstance = editor;
                    editorInstances.push(editor);
                    
                    // If the element was required, store this information as a data attribute
                    if (element.required) {
                        element.setAttribute('data-required', 'true');
                    }
                })
                .catch(error => {
                    console.error('CKEditor initialization error:', error);
                    element.style.minHeight = '300px';
                });
        });
    }
});