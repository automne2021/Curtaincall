document.addEventListener('DOMContentLoaded', function() {
    if(document.getElementById('play-description')) {
        ClassicEditor
            .create(document.getElementById('play-description'))
            .catch(error => {
                console.error(error);
            });
    }
});