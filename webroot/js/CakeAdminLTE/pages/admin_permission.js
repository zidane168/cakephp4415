ADMIN_PERMISSION = {
    slugs: [],
    names: [],
    init_page: function(){
        $('#txtSlug').on('keyup', function(){
            var slug = 'perm-admin-' + $(this).val();
            $('#slug_error').html("");
            $("#btnAdded").attr('disabled', false);

            if(ADMIN_PERMISSION.slugs.indexOf(slug) > -1){
                $('#slug_error').html("別號已經存在了");
                $("#btnAdded").attr('disabled', true);
            }
        })

        $('#txtName').on('keyup', function(){
            $('#name_error').html("");
            $("#btnAdded").attr('disabled', false);

            if(ADMIN_PERMISSION.names.indexOf($(this).val()) > -1){
                $('#name_error').html("別號已經存在了");
                $("#btnAdded").attr('disabled', true);
            }
        })
    }
}