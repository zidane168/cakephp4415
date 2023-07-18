
var ADMIN_VERIFICATION = {

    number: '',
    generate_number: function() {

        $('#btn_generate_number').on('click', function() {
            const min = 1 * Math.pow(10, ADMIN_VERIFICATION.number - 1);
            const max = 9 * Math.pow(10, ADMIN_VERIFICATION.number - 1);
            let num = Math.floor(min + Math.random() * max);
            $('#code').val(num);
        });
    }
}