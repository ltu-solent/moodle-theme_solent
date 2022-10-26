import {get_string as getString} from 'core/str';
import Notification from 'core/notification';

/**
 * Back to top feature.
 */
export const totop = () => {
    let strings = getString('totop', 'theme_solent');
    let footer = document.querySelector('#page-footer');
    let page = document.querySelector('#page');
    let button = document.createElement('button');
    strings.then(function(string) {
        button.setAttribute('id', 'back-to-top');
        button.setAttribute('class', 'btn btn-icon bg-secondary icon-no-margin d-print-none');
        button.setAttribute('aria-label', string);
        button.innerHTML = '<i aria-hidden="true" class="fa fa-chevron-up fa-fw"></i>';
        footer.after(button);
        // This function fades the button in when the page is scrolled down or fades it out
        // if the user is at the top of the page again.
        // Please note that Boost in Moodle 4.0 does not scroll the window object / whole body tag anymore,
        // it scrolls the #page element instead.
        page.addEventListener('scroll', () => {
            if (page.scrollTop > 220) {
                button.style.display = 'block';
            } else {
                button.style.display = 'none';
            }
        });

        // This function scrolls the page to top with a duration of 500ms.
        button.addEventListener('click', (event) => {
            event.preventDefault();
            page.scrollTo({
                top: 0,
                left: 0,
                behavior: 'smooth'
            });
        });

        return true;
    }).fail(Notification.exception);
};