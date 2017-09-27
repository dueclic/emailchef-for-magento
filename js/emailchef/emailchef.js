var $e = jQuery.noConflict();

$e(document).ready(function () {

      var isCreated = 0;

      function addList(apiUser, apiPass, listName, listDesc) {

        var ajax_data = {
          api_user: apiUser,
          api_pass: apiPass,
          list_name: listName,
          list_desc: listDesc
        };

        console.log(ajax_data);

        $e("#create_emailchef_list_btn").attr("disabled", "disabled");
        $e("#tab1_general_list").attr("disabled", "disabled");
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
              $e("#tab1_general_list").append($e('<option>').text(listName).attr('value', response.list_id))
              $e("#tab1_general_list").val(response.list_id).attr("selected", "selected");
            }



            //createCustomFields(apiUser, apiPass, response.list_id);

          },
          error: function (jxqr, textStatus, thrown) {
            $e("#create_emailchef_list_danger").find(".reason").text(jxqr.error + " " + textStatus + " " + thrown);
            $e("#create_emailchef_list_danger").show();
          },
          complete: function () {
            $e("#create_emailchef_list_load").hide();
            $e("#create_emailchef_list_btn").attr("disabled", false);
            $e("#tab1_general_list").attr("disabled", false);
            $e("#tab1_general_username").empty();
            $e("#tab1_general_password").empty();
          }
        });


      }

      function checkLoginData(apiUser, apiPass) {
        var btn = $e("#emailchef_selftest_button");
        $e(btn).attr("disabled", true);
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
              $e("#tab1_general_list").empty();

              if (response.lists.length > 0) {

                $e.each(response.lists, function (key, list) {
                  $e("#tab1_general_list").append($e('<option>').text(list.label).attr('value', list.value));
                });

              }

              else {
                $e("#tab1_general_list").append($e('<option>').text("Nessuna lista trovata.").attr('value', -1))
              }

            }
            else {
              alert(response.msg);
            }
          },
          error: function (jqXHR, textStatus, errorThrown) {
            alert("Errore: " + textStatus + " ( " + errorThrown + " )");
          },
          complete: function (jqXHR, textStatus, errorThrown) {
            $e(btn).attr("disabled", false);
          }
        });
      }

      $e("#create_emailchef_list_trigger").on("click", function (evt) {
        evt.preventDefault();
        $e("#create_emailchef_list").toggle();
      });


      $e("#emailchef_selftest_button").on("click", function (evt) {
        evt.preventDefault();
        var apiUser = $e("#tab1_general_username").val();
        var apiPass = $e("#tab1_general_password").val();
        checkLoginData(apiUser, apiPass);
      });

      $e("#create_emailchef_list_btn").on("click", function (evt) {
        evt.preventDefault();
        var apiUser = $e("#tab1_general_username").val();
        var apiPass = $e("#tab1_general_password").val();
        var listName = $e("#new_list_name").val();
        var listDesc = $e("#new_list_description").val();

        isCreated = 1;

        addList(apiUser, apiPass, listName, listDesc);
      });

    }
);