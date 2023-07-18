var SERVICE = {
    eforms_admin_add: function(params, handleData){
        $.ajax({
            url: cakephp.base_url + "api/eform/eforms/add.json",
            type: "POST",
            data: params,
            dataType: "JSON",
            contentType: false,
            processData: false,
            success: function(resp){
                handleData(resp)
            },
            error: function(error){
                bootbox.alert("Connection error!");
            }
        })
    },

    tests_admin_add: function(params, handleData){
        $.ajax({
            url: cakephp.base_url + "api/eform/tests/add.json",
            type: "POST",
            data: params,
            dataType: "JSON",
            contentType: false,
            processData: false,
            success: function(resp){
                handleData(resp)
            },
            error: function(error){
                bootbox.alert("Connection error!");
            }
        })
    },

    tests_admin_edit: function(params, handleData){
		$.ajax({
            url: cakephp.base_url + "api/eform/tests/edit.json",
            type: "POST",
            data: params,
            dataType: "JSON",
            contentType: false,
            processData: false,
            beforeSend: function(){},
            success: function(resp){
                handleData(resp);
            },
            error: function(error){
                bootbox.alert("Connection error!");
            },
            complete: function(){}
        })
	},
}