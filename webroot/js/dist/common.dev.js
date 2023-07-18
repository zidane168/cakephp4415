"use strict";

var COMMON = {
  check_number_rules: function check_number_rules() {
    $('input#latitude').on('input', function () {
      this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');
    });
    $('input#latitude').on('change', function () {
      this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');
    });
    $('input#longitude').on('input', function () {
      this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');
    });
    $('input#longitude').on('change', function () {
      this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');
    });
    $('input#telephone').on('input', function () {
      this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');
    });
    $('input#telephone').on('change', function () {
      this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');
    });
    $('input#mobile_phone').on('input', function () {
      this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');
    });
    $('input#mobile_phone').on('change', function () {
      this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');
    });
  }
};