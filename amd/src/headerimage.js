define(['jquery'], function($) {

    return {
        init: function(wwwroot) {

            $("input[name='opt']").click(function() {
                var opt = $("input[name='opt']:checked").val();
                var url = wwwroot + "/theme/solent/set_header_image.php?";
                if (opt) {
                    var course = document.getElementById('course').value;
                    window.location.replace(url + "course=" + course + "&opt=" + opt);
                }
            });
        }
    };
});
