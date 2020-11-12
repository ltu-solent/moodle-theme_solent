// Standard license block omitted.
/*
 * @package    theme_solent
 * @copyright  2020 Sarah Cotton
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 /**
  * @module theme_solent/headerimage
  */
define(['jquery', 'theme_solent/headerimage'], function($) {

    return {
        init: function() {

            function setheaderimage() {
				
				document.getElementById("headerimageform").submit();

            }

            // $(document).ready(function() {
                // var $window = $(window);
                // $window.on('scroll resize', check_if_in_view);
            // });

        }
    };
});