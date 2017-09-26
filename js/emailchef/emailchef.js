var $e = jQuery.noConflict();

function checkLogin() {
  if ($e("#row_tab1_general_username").val() === '' && $e("#row_tab1_general_password").val() === '') {
    alert("Errore");
  }
}

$e(document).ready(function () {
  $e("#emailchef_selftest_button").on("click", function (evt) {
    evt.preventDefault();
    var btn = $e(this);
    $e(btn).attr("disabled", true);
    $e.ajax({

      type: "POST",
      url: "/index.php/emailchef/ajax/checkcredentials",
      data: {
        'username': $e("#tab1_general_username").val(),
        'password': $e("#tab1_general_password").val()
      },
      dataType: "json",
      success: function (response) {
        if (response.type == "success"){
          $e("#tab1_general_list").empty();

          if (response.lists.length > 0) {

            $e.each(response.lists, function (key, list) {
              $e("#tab1_general_list").append($e('<option>').text(list.name).attr('value', list.id));
            });

          }

          else {
            $e("#tab1_general_list").append($e('<option>').text("Nessuna lista trovata.").attr('value', -1))
          }

        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        alert("Errore: " + textStatus + " ( " + errorThrown + " )");
      },
      complete: function (jqXHR, textStatus, errorThrown) {
        $e(btn).attr("disabled", false);
      }
    });
  })
});