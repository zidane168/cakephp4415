"use strict";

var ADMIN_PATIENT_VERIFICATION = {
  number: '',
  generate_number: function generate_number() {
    $('#btn_generate_number').on('click', function () {
      var min = 1 * Math.pow(10, ADMIN_PATIENT_VERIFICATION.number - 1);
      var max = 9 * Math.pow(10, ADMIN_PATIENT_VERIFICATION.number - 1);
      var num = Math.floor(min + Math.random() * max);
      $('#code').val(num);
    });
  }
};