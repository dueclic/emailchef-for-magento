var $e = jQuery.noConflict();

$e(document).ready(function () {

      var isCreated = 0;

      if ($e(".emailchef-logo").length) {
        $e(".emailchef-logo").prependTo("#config_edit_form");
        $e(".emailchef-logo").show();
      }

      if ($e("#emailchef_general_username").length && $e("#emailchef_general_username").val() !== "" && $e("#emailchef_general_password").length && $e("#emailchef_general_password").val() !== "") {
        var apiUser = $e("#emailchef_general_username").val();
        var apiPass = $e("#emailchef_general_password").val();
        checkLoginData(apiUser, apiPass);
      }

      function createCustomFields(apiUser, apiPass, listId) {

        var ajax_data = {
          api_user: apiUser,
          api_pass: apiPass,
          list_id: listId
        };

        $e("#emailchef_response .alert").hide();
        $e("#create_emailchef_cf_load").show();

        $e("#create_emailchef_list_btn").attr("disabled", "disabled");
        $e("#emailchef_general_list").attr("disabled", "disabled");
        $e("#emailchef_save_wizard").attr("disabled", "disabled");

        $e.ajax({
          type: 'POST',
          url: "/index.php/emailchef/ajax/createcustomfields",
          data: ajax_data,
          dataType: 'json',
          success: function (response) {

            if (response.type == 'error') {
              $e("#create_emailchef_cf_load").hide();
              $e("#create_emailchef_cf_danger").find(".reason").text(response.msg);
              $e("#create_emailchef_cf_danger").show();
              return;
            }

            $e("#create_emailchef_cf_success").show().delay(3000).fadeOut();

          },
          error: function (jxqr, textStatus, thrown) {
            $e("#create_emailchef_cf_danger").find(".reason").text(textStatus + " " + thrown);
            $e("#create_emailchef_cf_danger").show();
          },
          complete: function () {
            $e("#create_emailchef_cf_load").hide();
            $e("#create_emailchef_list_btn").attr("disabled", false);
            $e("#emailchef_general_list").attr("disabled", false);
            $e("#emailchef_save_wizard").attr("disabled", false);
          }
        });

      }

      function addList(apiUser, apiPass, listName, listDesc) {

        var ajax_data = {
          api_user: apiUser,
          api_pass: apiPass,
          list_name: listName,
          list_desc: listDesc
        };

        console.log(ajax_data);

        $e("#create_emailchef_list_btn").attr("disabled", "disabled");
        $e("#emailchef_general_list").attr("disabled", "disabled");
        $e("#emailchef_response .alert").hide();
        $e("#create_emailchef_list_load").show();

        $e.ajax({
          type: "POST",
          url: "/index.php/emailchef/ajax/addlist",
          data: ajax_data,
          dataType: 'json',
          success: function (response) {

            if (response.type == 'error') {
              $e("#create_emailchef_list_load").hide();
              $e("#create_emailchef_list_danger").find(".reason").text(response.msg);
              $e("#create_emailchef_list_danger").show();
              return;
            }

            $e("#create_emailchef_list").slideUp();

            $e("#create_emailchef_list_success").show().delay(3000).fadeOut();

            if (response.list_id !== undefined) {
              $e("#emailchef_general_list").append($e('<option>').text(listName).attr('value', response.list_id))
              $e("#emailchef_general_list").val(response.list_id).attr("selected", "selected");
            }


            createCustomFields(apiUser, apiPass, response.list_id);

          },
          error: function (jxqr, textStatus, thrown) {
            $e("#create_emailchef_list_danger").find(".reason").text(textStatus + " " + thrown);
            $e("#create_emailchef_list_danger").show();
          },
          complete: function () {
            $e("#create_emailchef_list_load").hide();
            $e("#create_emailchef_list_btn").attr("disabled", false);
            $e("#emailchef_general_list").attr("disabled", false);
            $e("#emailchef_general_username").empty();
            $e("#emailchef_general_password").empty();
          }
        });


      }

      function checkLoginData(apiUser, apiPass) {
        var btn = $e("#emailchef_selftest_button");
        $e(btn).attr("disabled", true);

        $e("#emailchef_response_login .alert").hide();
        $e("#login_emailchef_list_load").show();

        $e.ajax({
          type: "POST",
          url: "/index.php/emailchef/ajax/checkcredentials",
          data: {
            'username': apiUser,
            'password': apiPass
          },
          dataType: "json",
          success: function (response) {
            if (response.type == "success") {

              if (response.policy === "premium")
                $e("#row_emailchef_general_list, #row_emailchef_general_policy").show();
              else
                $e("#row_emailchef_general_list, #row_emailchef_general_policy").hide();

              $e("#login_emailchef_list_success").show().delay(3000).fadeOut();

            }
            else {
              $e("#login_emailchef_list_danger").find(".reason").text(response.msg);
              $e("#login_emailchef_list_danger").show();
              $e("#row_emailchef_general_list, #row_emailchef_general_policy").hide();
            }
          },
          error: function (jqXHR, textStatus, errorThrown) {
            $e("#login_emailchef_list_danger").find(".reason").text(textStatus + " " + thrown);
            $e("#login_emailchef_list_danger").show();
          },
          complete: function (jqXHR, textStatus, errorThrown) {
            $e("#login_emailchef_list_load").hide();
            $e(btn).attr("disabled", false);
          }
        });
      }

    function checkPostLoginData(apiUser, apiPass) {
      var btn = $e("#emailchef_selftest_button");
      $e(btn).attr("disabled", true);

      $e("#emailchef_response_login .alert").hide();
      $e("#login_emailchef_list_load").show();

      $e.ajax({
        type: "POST",
        url: "/index.php/emailchef/ajax/checkcredentials",
        data: {
          'username': apiUser,
          'password': apiPass
        },
        dataType: "json",
        success: function (response) {
          if (response.type == "success") {
            $e("#emailchef_general_list").empty();

            if (response.lists.length > 0) {

              $e.each(response.lists, function (key, list) {
                $e("#emailchef_general_list").append($e('<option>').text(list.label).attr('value', list.value));
              });

            }

            else {
              $e("#emailchef_general_list").append($e('<option>').text("Nessuna lista trovata.").attr('value', -1))
            }

            if (response.policy === "premium")
              $e("#row_emailchef_general_list, #row_emailchef_general_policy").show();
            else
              $e("#row_emailchef_general_list, #row_emailchef_general_policy").hide();

            $e("#login_emailchef_list_success").show().delay(3000).fadeOut();

          }
          else {
            $e("#login_emailchef_list_danger").find(".reason").text(response.msg);
            $e("#login_emailchef_list_danger").show();
            $e("#row_emailchef_general_list, #row_emailchef_general_policy").hide();
          }
        },
        error: function (jqXHR, textStatus, errorThrown) {
          $e("#login_emailchef_list_danger").find(".reason").text(textStatus + " " + thrown);
          $e("#login_emailchef_list_danger").show();
        },
        complete: function (jqXHR, textStatus, errorThrown) {
          $e("#login_emailchef_list_load").hide();
          $e(btn).attr("disabled", false);
        }
      });
    }

      function checkCustomFields(apiUser, apiPass, listId) {

        $e("#create_emailchef_list_btn").attr("disabled", "disabled");
        //$e("#emailchef_general_list").attr("disabled", "disabled");
        $e("#emailchef_save_wizard").attr("disabled", "disabled");

        var ajax_data = {
          api_user: apiUser,
          api_pass: apiPass,
          list_id: listId
        };

        $e("#emailchef_response_ccf .alert").hide();
        $e("#create_emailchef_ccf_load").show();

        $e.ajax({
          type: 'POST',
          url: "/index.php/emailchef/ajax/createcustomfields",
          data: ajax_data,
          dataType: 'json',
          success: function (response) {

            if (response.type == 'error') {
              $e("#create_emailchef_ccf_load").hide();
              $e("#create_emailchef_ccf_danger").find(".reason").text(response.msg);
              $e("#create_emailchef_ccf_danger").show();
              return;
            }

            $e("#create_emailchef_ccf_success").show().delay(3000).fadeOut();
            $e("#config_edit_form").submit();

          },
          error: function (jxqr, textStatus, thrown) {
            $e("#create_emailchef_ccf_danger").find(".reason").text(textStatus + " " + thrown);
            $e("#create_emailchef_ccf_danger").show();
          },
          complete: function () {
            $e("#create_emailchef_ccf_load").hide();
            $e("#create_emailchef_list_btn").attr("disabled", false);
            //$e("#emailchef_general_list").attr("disabled", false);
            $e("#emailchef_save_wizard").attr("disabled", false);
            isCreated = 0;
          }
        });

      }

      $e(document).on("click", "#create_emailchef_list_trigger", function (evt) {
        evt.preventDefault();
        $e("#create_emailchef_list").toggle();
      });

      $e(document).on("click", "#emailchef_selftest_button", function (evt) {
        evt.preventDefault();
        var apiUser = $e("#emailchef_general_username").val();
        var apiPass = $e("#emailchef_general_password").val();
        checkPostLoginData(apiUser, apiPass);
      });

      $e(document).on("click", "#create_emailchef_list_btn", function (evt) {
        evt.preventDefault();
        var apiUser = $e("#emailchef_general_username").val();
        var apiPass = $e("#emailchef_general_password").val();
        var listName = $e("#new_list_name").val();
        var listDesc = $e("#new_list_description").val();

        isCreated = 1;

        if (listName === ""){
            $e("#create_emailchef_list_danger").find(".reason").text(Translator.translate("provide a valid name for eMailChef list."));
            $e("#create_emailchef_list_danger").show();
            return;
        }

        addList(apiUser, apiPass, listName, listDesc);
      });

      $e(document).on("click", "#emailchef_save_wizard", function (evt) {

        $e("#emailchef_general_syncevent").val(1);

        if (isCreated === 0) {

          var apiUser = $e("#emailchef_general_username").val();
          var apiPass = $e("#emailchef_general_password").val();
          var listId = $e("#emailchef_general_list").val();

          checkCustomFields(apiUser, apiPass, listId);

        }
        else
          configForm.submit();

      });

      $e("#row_emailchef_general_policy .note").show().insertBefore("#emailchef_response_ccf");
      $e("#row_emailchef_general_policy .note").show().insertBefore("#emailchef_response_ccf");

      if ($e("#emailchef_general_syncevent").val() == 1 || $e("#emailchef_general_syncevent_inherit").val() == 1){
        $e.ajax({
          type: 'POST',
          url: "/index.php/emailchef/ajax/initialsync",
          data : {
              'list_id' : $e("#emailchef_general_list").val(),
              'store' : $e("#store_switcher").val()
          },
          dataType: 'json',
          success: function (response) {

            console.log(response.msg);

          },
          error: function (jxqr, textStatus, thrown) {
            alert("Error: "+textStatus + " " + thrown);
          }
        });
      }

    }
);