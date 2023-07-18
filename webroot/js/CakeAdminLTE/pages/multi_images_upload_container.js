var MULTI_IMAGE_UPLOADS = {
  limit_size: 3,
  lst_material: [],
  lst_edit_material_string: "",
  lst_edit_material: [],
  lst_remove_material: [],
  lst_material_extension_allowed: [],

  validation_file: function (file) {
    var f_extension = file.name.toLowerCase().split(".").pop();

    if (MULTI_IMAGE_UPLOADS.lst_material_extension_allowed.indexOf("." + f_extension) == -1) {
      return false;
    }
    if (file.size > MULTI_IMAGE_UPLOADS.limit_size * 1024 * 1024) {
      return false; // 5 * 1024 * 1024
    }
    return true;
  },

  remove_material: function (index) {
    MULTI_IMAGE_UPLOADS.lst_material.splice(index, 1);
    MULTI_IMAGE_UPLOADS.get_file_preview();

    // updatefileLists (input file)
    MULTI_IMAGE_UPLOADS.update_file_lists();
  },

  bytes_to_size: function (bytes) {
    var sizes = ["Bytes", "KB", "MB", "GB", "TB"];
    if (bytes == 0) return "0 Byte";
    var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
    return Math.round(bytes / Math.pow(1024, i), 2) + " " + sizes[i];
  },

  get_file_preview: function () {
    var files = MULTI_IMAGE_UPLOADS.lst_material;
    var preview_img = "";
    for (var i = 0; i < files.length; i++) {
      var f_extension = files[i].name.split(".").pop();
      preview_img += '<li class="item">';
      preview_img += '  <p class="file-name m-0">' + files[i].name + "</p>";
      preview_img += '  <p class="file-info text-grey">';
      preview_img += MULTI_IMAGE_UPLOADS.bytes_to_size(files[i].size);
      preview_img += '      <span class="seperate"></span>';
      preview_img += "(" + f_extension + ")";
      preview_img +=
        '  </p>  <span class="fa fa-times remove-file pointer" onclick="MULTI_IMAGE_UPLOADS.remove_material(' +
        i +
        ')"></span>';
      preview_img += "</li>";
    }

    $("#lst-imported-material").html(preview_img);
  },

  toggle_current_material: function (material_id) {
    var index = MULTI_IMAGE_UPLOADS.lst_remove_material.indexOf(material_id);

    if (index != -1) {
      MULTI_IMAGE_UPLOADS.lst_remove_material.splice(index, 1);
    } else {
      MULTI_IMAGE_UPLOADS.lst_remove_material.push(material_id);
    }

    $("#material-" + material_id + " .remove-file").toggleClass("hidden");
    $("#material-" + material_id + "").toggleClass("removed");
    $("#remove_image").val(MULTI_IMAGE_UPLOADS.lst_remove_material);
    console.log(material_id);
  },

  // Vilh - update modified fileList (20230104)
  // https://stackoverflow.com/questions/52078853/is-it-possible-to-update-filelist
  update_file_lists: function () {
    let dataTransfer = new DataTransfer();
    MULTI_IMAGE_UPLOADS.lst_material.forEach((file) => {
      let newFile = new File([file], file.name);
      dataTransfer.items.add(newFile);
    });
    let myFileList = dataTransfer.files;

    let fileInput = document.querySelector("#upload-material");
    fileInput.files = myFileList;
  },

  add: function () {
    // Drag enter
    // $(".import-file-area").on("dragenter", function (e) {
    //   e.stopPropagation();
    //   e.preventDefault();
    //   $(".import-file-area").addClass("hover");
    // });

    // // Drag over
    // $(".import-file-area").on("dragover", function (e) {
    //   e.stopPropagation();
    //   e.preventDefault();
    // });

    // Drop
    $(".import-file-area").on("drop", function (e) {
      e.stopPropagation();
      e.preventDefault();
      $(".import-file-area").removeClass("hover");
      let files = e.originalEvent.dataTransfer.files;
      for (let i = 0; i < files.length; i++) {
        if (MULTI_IMAGE_UPLOADS.validation_file(files[i])) {
          MULTI_IMAGE_UPLOADS.lst_material.push(files[i]);
          MULTI_IMAGE_UPLOADS.get_file_preview();
        }
      }

      MULTI_IMAGE_UPLOADS.update_file_lists();
    });

    // // Open file selector on div click
    // $(".import-file-area").click(function () {
    //   $("#upload-material").click();
    // });

    // file selected
    $("#upload-material").change(function () {
      var files = $("#upload-material")[0].files;
      for (var i = 0; i < files.length; i++) {
        if (MULTI_IMAGE_UPLOADS.validation_file(files[i])) {
          MULTI_IMAGE_UPLOADS.lst_material.push(files[i]);
          MULTI_IMAGE_UPLOADS.get_file_preview();
        }
      }
 
      MULTI_IMAGE_UPLOADS.update_file_lists();
    });
  },

  common: function() { 
    // Drag enter
    $(".import-file-area").on("dragenter", function (e) {
        e.stopPropagation();
        e.preventDefault();
        $(".import-file-area").addClass("hover");
      });
  
      // Drag over
      $(".import-file-area").on("dragover", function (e) {
        e.stopPropagation();
        e.preventDefault();
      });
  
    // Open file selector on div click
    $(".import-file-area").click(function () {
        $("#upload-material").click();
      });
  },

  // for edit
  edit: function () {
    let listDataString = MULTI_IMAGE_UPLOADS.lst_edit_material_string;

    if (!listDataString) {
      return;
    }
    let lists = JSON.parse(listDataString);

    lists.forEach((item, index) => {
      MULTI_IMAGE_UPLOADS.lst_edit_material.push(item);
    });
    MULTI_IMAGE_UPLOADS.get_edit_file_preview();

    // Drop
    $(".import-file-area").on("drop", function (e) {
      e.stopPropagation();
      e.preventDefault();
      $(".import-file-area").removeClass("hover");
      let files = e.originalEvent.dataTransfer.files;
 
      for (let i = 0; i < files.length; i++) {
        if (MULTI_IMAGE_UPLOADS.validation_file(files[i])) {
          MULTI_IMAGE_UPLOADS.lst_material.push(files[i]);
        }
      }

      MULTI_IMAGE_UPLOADS.get_edit_add_file_preview(); 
      MULTI_IMAGE_UPLOADS.update_file_lists();
    });


    // file selected
    $("#upload-material").change(function () {
      var files = $("#upload-material")[0].files;
      for (var i = 0; i < files.length; i++) {
        if (MULTI_IMAGE_UPLOADS.validation_file(files[i])) {
          MULTI_IMAGE_UPLOADS.lst_material.push(files[i]);
          MULTI_IMAGE_UPLOADS.get_edit_add_file_preview();
        }
      }
 
      MULTI_IMAGE_UPLOADS.update_file_lists();
    });
  },

  get_edit_file_preview: function () {
    let preview_img = "";
    let arrFiles = MULTI_IMAGE_UPLOADS.lst_edit_material; 

    for (var i = 0; i < arrFiles.length; i++) {
      let f_extension = arrFiles[i].ext;
      preview_img += '<li class="item" id="material-' + arrFiles[i].id + '">';
      preview_img += '  <p class="file-name m-0">' + arrFiles[i].file_name + "</p>";
      preview_img += '  <p class="file-info text-grey">';
      preview_img +=        MULTI_IMAGE_UPLOADS.bytes_to_size(arrFiles[i].size);
      preview_img += '      <span class="seperate"></span>';
      preview_img +=        "(" + f_extension + ")";
      preview_img += '  </p>';
      preview_img += '  <span class="fa fa-times remove-file pointer" onclick="MULTI_IMAGE_UPLOADS.remove_edit_material(' + arrFiles[i].id  + ', ' + i +  ')"></span>';
      preview_img += "</li>";
    }
    $("#lst-imported-material").html(preview_img);
  },

  get_edit_add_file_preview: function () {
    let arrFiles = MULTI_IMAGE_UPLOADS.lst_edit_material;
  
    let preview_img = "";

    for (var i = 0; i < arrFiles.length; i++) {
      let f_extension = arrFiles[i].ext;
      preview_img += '<li class="item" id="material-' + arrFiles[i].id + '">';
      preview_img += '  <p class="file-name m-0">' + arrFiles[i].file_name + "</p>";
      preview_img += '  <p class="file-info text-grey">';
      preview_img +=        MULTI_IMAGE_UPLOADS.bytes_to_size(arrFiles[i].size);
      preview_img += '      <span class="seperate"></span>';
      preview_img +=        "(" + f_extension + ")";
      preview_img += '  </p>  ';
      preview_img += '  <span class="fa fa-times remove-file pointer" onclick="MULTI_IMAGE_UPLOADS.remove_edit_material(' + arrFiles[i].id + ', ' + i + ')"></span>';
      preview_img += "</li>";
    }

    arrFiles = MULTI_IMAGE_UPLOADS.lst_material;
 
    for (var i = 0; i < arrFiles.length; i++) {
      var f_extension = arrFiles[i].name.split(".").pop();
      preview_img += '<li class="item">';
      preview_img += '  <p class="file-name m-0">' + arrFiles[i].name + "</p>";
      preview_img += '  <p class="file-info text-grey">';
      preview_img +=        MULTI_IMAGE_UPLOADS.bytes_to_size(arrFiles[i].size);
      preview_img += '      <span class="seperate"></span>';
      preview_img +=        "(" + f_extension + ")";
      preview_img += '  </p>';
      preview_img += '  <span class="fa fa-times remove-file pointer" onclick="MULTI_IMAGE_UPLOADS.remove_add_material(' +   i  +  ')"></span>';
      preview_img += "</li>";
    }

    console.log(MULTI_IMAGE_UPLOADS.lst_material)
    $("#lst-imported-material").html(preview_img);
  },

  remove_edit_material: function (image_id, id) {  
    MULTI_IMAGE_UPLOADS.lst_edit_material.splice(id, 1); 
    MULTI_IMAGE_UPLOADS.lst_remove_material.push(image_id);   
    $("#remove_image").val( JSON.stringify(MULTI_IMAGE_UPLOADS.lst_remove_material) ); 

    MULTI_IMAGE_UPLOADS.get_edit_add_file_preview();

    // update file Lists (input file)
    MULTI_IMAGE_UPLOADS.update_file_lists();
  },

  remove_add_material: function (index) { 
    MULTI_IMAGE_UPLOADS.lst_material.splice(index, 1);
    MULTI_IMAGE_UPLOADS.get_edit_add_file_preview();
 
    MULTI_IMAGE_UPLOADS.update_file_lists();
  },
};
