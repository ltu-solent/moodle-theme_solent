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

/**
 * Toggle any form fieldset to be open on load.
 * @param {array} ids List of IDs. Full id including #
 */
export const togglefieldsets = (ids) => {
    ids.forEach(element => {
        togglefieldset(element);
    });
};

const togglefieldset = (id) => {
    // Just in case wierd characters manage to get to the query selector,
    // using a try/catch block to quietly fail.
    try {
        id.replace('/[^a-zA-Z0-9#_-]/i', '');
        let header = document.querySelector('fieldset' + id);
        if (!header) {
            return;
        }
        header.classList.remove('collapsed');
        let toggle = header.querySelector('a[data-toggle="collapse"]');
        toggle.classList.remove('collapsed');
        toggle.setAttribute('aria-expanded', "true");
        let container = header.querySelector(id + 'container');
        container.classList.add('show');
    } catch (ex) {
        return;
    }
};
