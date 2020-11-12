define(['jquery'], function($) {

    return {
        init: function() {

            $("input[name='opt']").click(function(){
                var opt = $("input[name='opt']:checked").val();
                var url = "http://localhost/moodle36/theme/solent/set_header_image.php?";
                if(opt){
                    var course = document.getElementById('course').value;
                    window.location.replace(url+"course="+course+"&opt="+opt);
                }
            });
        }
    };
});