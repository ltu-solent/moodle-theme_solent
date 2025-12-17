/* jshint ignore:start */
/* eslint no-invalid-this: 0 */
define(['jquery', 'core/log'], function($) {

    "use strict"; // jshint ;_;

    var shouldIgnoreVideo = function($element, ignoreList) {
        if ($element.parents(ignoreList.join(',')).length > 0) {
            return true;
        }

        var $id = $element.attr('id');
        if ($id) {
            if ($id === 'onlineaudiorecorder') {
                return true;
            }
            if ($id.indexOf('mp3') >= 0) {
                return true;
            }
        }

        if (
            $element.tagName &&
            $element.tagName.toLowerCase() === 'embed' &&
            $element.parent('object').length
        ) {
            return true;
        }

        if ($element.parent('.fluid-width-video-wrapper').length) {
            return true;
        }

        return false;
    };
    /* jshint browser:true */
    /* !
     * FitVids 1.1.
     *
     * Copyright 2013, Chris Coyier - http://css-tricks.com + Dave Rupert - http://daverupert.com
     * Credit to Thierry Koblentz - http://www.alistapart.com/articles/creating-intrinsic-ratios-for-video/
     * Released under the WTFPL license - http://sam.zoy.org/wtfpl/
     *
     * Adapted to AMD for Moodle by Gareth J Barnard.
     *
     */

    $.fn.fitVids = function(options) {
        var settings = {
            customSelector: null,
            ignore: ["object[type='application/pdf']"],
            maxwidth: 0,
            maxheight: 0
        };

        if (!document.getElementById('fit-vids-style')) {
            // AppendStyles: https://github.com/toddmotto/fluidvids/blob/master/dist/fluidvids.js
            var head = document.head || document.getElementsByTagName('head')[0];
            var css = '.fluid-width-video-wrapper{width:100%;position:relative;padding:0;}' +
                '.fluid-width-video-wrapper iframe,' +
                '.fluid-width-video-wrapper object,' +
                '.fluid-width-video-wrapper embed {position:absolute;top:0;left:0;width:100% !important;height:100% !important;}';
            var div = document.createElement('div');
            div.innerHTML = '<p>x</p><style id="fit-vids-style">' + css + '</style>';
            head.appendChild(div.childNodes[1]);
        }

        if (options) {
            $.extend(settings, options);
        }
        return this.each(function() {
            var selectors = [
                "iframe[src*='player.vimeo.com']",
                "iframe[src*='youtube.com']",
                "iframe[src*='youtube-nocookie.com']",
                "iframe[src*='kickstarter.com'][src*='video.html']",
                "object",
                "embed"
            ];

            if (settings.customSelector) {
                $(settings.customSelector).each(function() {
                    if (this != '') {
                        selectors.push(this);
                    }
                });
            }

            var ignoreList = ['.fitvidsignore'];

            if (settings.ignore) {
                $(settings.ignore).each(function() {
                    if (this != '') {
                        ignoreList.push(this);
                    }
                });
            }

            var $allVideos = $(this).find(selectors.join(','));
            $allVideos = $allVideos.not("object object"); // SwfObj conflict patch.
            $allVideos = $allVideos.not(ignoreList.join(',')); // Disable FitVids on these.

            $allVideos.each(function() {
                var $this = $(this);

                if (shouldIgnoreVideo($this, ignoreList)) {
                    return;
                }
                if ((!$this.css('height') && !$this.css('width')) && (isNaN($this.attr('height')) || isNaN($this.attr('width')))) {
                    $this.attr('height', 9);
                    $this.attr('width', 16);
                }
                var height = (this.tagName.toLowerCase() === 'object' ||
                    ($this.attr('height') && !isNaN(parseInt($this.attr('height'), 10))))
                    ? parseInt($this.attr('height'), 10) : $this.height();
                var width = !isNaN(parseInt($this.attr('width'), 10)) ? parseInt($this.attr('width'), 10) : $this.width();
                var aspectRatio = height / width;

                if (!$this.attr('id')) {
                    var videoID = 'fitvid' + Math.floor(Math.random() * 999999);
                    $this.attr('id', videoID);
                }

                $this.wrap('<div class="fluid-width-video-wrapper"></div>').parent('.fluid-width-video-wrapper')
                    .css('padding-top', (aspectRatio * 100) + "%");
                if (settings.maxwidth > 0 || settings.maxheight > 0) {
                    var p = $this.parent('.fluid-width-video-wrapper').wrap('<div></div>').parent();
                    if (settings.maxwidth > 0) {
                        p.css('max-width', settings.maxwidth + 'px');
                    }
                    if (settings.maxheight > 0) {
                        p.css('max-height', settings.maxheight + 'px');
                    }
                }
                $this.removeAttr('height').removeAttr('width');
            });
        });
    };

    return {
        init: function(params) {
            $(document).ready(function($) {
                $(".pagelayout-course #page, .pagelayout-incourse #page").fitVids(params);
            });
        }
    };
});
/* jshint ignore:end */
