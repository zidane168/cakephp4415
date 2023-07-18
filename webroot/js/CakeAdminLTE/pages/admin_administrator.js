var ADMIN_ADMINISTRATOR = { 
    
    upload_avatar: function() {
        const fileUploader = document.getElementById('upload-file');
        fileUploader.addEventListener('change', (event) => {
            const files = event.target.files;
            let image = document.getElementById('avatar');
            image.src = URL.createObjectURL(event.target.files[0]);
        });  
    }
    
}