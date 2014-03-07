$(document).ready(function () {
    $("#gen-form").submit(function (e) {
        var typeVal = $("#type");
        if (typeVal.val() == "") {
            typeVal.val($("#dropdown-1").html().substring(0, 4).trim().toLowerCase());
            $("#gen-form").submit();
        }
    });
});