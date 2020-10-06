var $e = jQuery.noConflict();

$e(document).ready(function () {
    $e.ajax({
        type: "POST",
        url: "/index.php/emailchef/ajax/syncabandonedcarts",
        dataType: "json",
        success: function (response) {
            if (response.type == "success") {
                console.log(response);
            }
            else {

            }
        },
        error: function (jqXHR, textStatus, thrown) {

        }
    });
});
