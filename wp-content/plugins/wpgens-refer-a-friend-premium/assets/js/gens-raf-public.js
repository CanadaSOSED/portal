/* eslint-disable */
(function ($) {
    'use strict';

    //Javascript GET cookie parameter
    var $_GET = {};
    document.location.search.replace(/\??(?:([^=]+)=([^&]*)&?)/g, function () {
        function decode(s) {
            return decodeURIComponent(s.split('+').join(' '));
        }

        $_GET[decode(arguments[1])] = decode(arguments[2]);
    });

    // Get time var defined in woo backend
    var $time = 1;
    if (typeof gens_raf !== 'undefined' && gens_raf.cookieTime !== '') {
        $time = parseInt(gens_raf.cookieTime);
    }

    //If raf is set, add cookie.
    if (typeof $_GET['raf'] !== 'undefined' && $_GET['raf'] !== null) {
        //console.log(window.location.hostname);
        cookie.set('gens_raf', $_GET['raf'], { expires: $time, path: '/' });
    }

    // Share Shortcode
    $.fn.rafSocShare = function (opts) {
        var $this = this;

        opts = $.extend(
            {
                attr: 'href',
                linked_in: false,
                pinterest: false,
                whatsapp: false,
            },
            opts
        );

        for (var opt in opts) {
            if (opts[opt] === false) {
                continue;
            }

            switch (opt) {
                case 'facebook':
                    var url = 'https://www.facebook.com/sharer/sharer.php?u=';
                    var name = 'Facebook';
                    _popup(url, name, opts[opt], 400, 640);
                    break;

                case 'twitter':
                    var posttitle = $('.gens-referral_share__tw').data('title');
                    var via = $('.gens-referral_share__tw').data('via');
                    var url = 'https://twitter.com/intent/tweet?via=' + via + '&text=' + posttitle + '&url=';
                    var name = 'Twitter';
                    _popup(url, name, opts[opt], 440, 600);
                    break;

                case 'whatsappDesktop':
                    var posttitle = $('.gens-referral_share__wade').data('title');
                    var url = 'https://web.whatsapp.com/send?text=' + encodeURIComponent(posttitle) + '%20';
                    var name = 'WhatsApp Desktop';
                    _popup(url, name, opts[opt], 600, 600);
                    break;

                case 'linked_in':
                    var url = 'https://www.linkedin.com/shareArticle?mini=true&url=';
                    var name = 'LinkedIn';
                    _popup(url, name, opts[opt], 570, 520);
                    break;

                case 'pinterest':
                    var url = 'https://www.pinterest.com/pin/find/?url=';
                    var name = 'Pinterest';
                    _popup(url, name, opts[opt], 500, 800);
                    break;

                case 'whatsapp':
                    var posttitle = $('.gens-referral_share__wa').data('title');
                    var name = 'Whatsapp';
                    var url = 'whatsapp://send?text=' + encodeURIComponent(posttitle) + '%20';
                    _popup(url, name, opts[opt], 500, 800);
                default:
                    break;
            }
        }

        function _popup(url, name, opt, height, width) {
            if (opt !== false && $this.find(opt).length) {
                $this.on('click', opt, function (e) {
                    e.preventDefault();

                    var top = screen.height / 2 - height / 2;
                    var left = screen.width / 2 - width / 2;
                    var share_link = $(this).attr(opts.attr);

                    if (name === 'Whatsapp') {
                        window.location = url + encodeURIComponent(share_link) + '%20';
                        return true;
                    } else if (name === 'WhatsApp Desktop') {
                        window.open(url, name, 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=' + height + ',width=' + width + ',top=' + top + ',left=' + left);
                    } else {
                        window.open(
                            url + encodeURIComponent(share_link),
                            name,
                            'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=' + height + ',width=' + width + ',top=' + top + ',left=' + left
                        );
                    }

                    return false;
                });
            }
        }
        return;
    };

    function gensCopyText(text) {
        function selectElementText(element) {
            if (document.selection) {
                var range = document.body.createTextRange();
                range.moveToElementText(element);
                range.select();
            } else if (window.getSelection) {
                var range = document.createRange();
                range.selectNode(element);
                window.getSelection().removeAllRanges();
                window.getSelection().addRange(range);
            }
        }
        var element = document.createElement('DIV');
        element.textContent = text;
        element.style.backgroundColor = '#fff';
        document.body.appendChild(element);
        selectElementText(element);
        document.execCommand('copy');
        element.remove();
    }

    jQuery(document).ready(function () {
        $('.gens-referral_share').rafSocShare({
            facebook: '.gens-referral_share__fb',
            twitter: '.gens-referral_share__tw',
            whatsappDesktop: '.gens-referral_share__wade',
            linked_in: '.gens-referral_share__ln',
            pinterest: '.gens-referral_share__pin',
            whatsapp: '.gens-referral_share__wa',
        });

        $('#js--gens-email-clone').on('click', function (e) {
            e.preventDefault();
            var $clone = $('#gens-referral_share__email').children().first().clone();
            $clone.insertBefore('#js--gens-email-clone').find('input').val('');
        });

        $('#js--gens-email-remove').on('click', function (e) {
            e.preventDefault();
            $('#gens-referral_share__email').find('.gens-referral_share__email__inputs').last().remove();
        });

        $('#gens-referral_share__email').submit(function (e) {
            e.preventDefault();
            var valid = true;
            $('.gens-referral_share__email__inputs').each(function () {
                var emailInput = $(this).find("input[type='email']");
                emailInput.removeClass('error');
                if (!validateEmail(emailInput.val())) {
                    valid = false;
                    emailInput.addClass('error');
                }
            });
            if (valid === true) {
                gensAjaxSubmit();
            }
        });

        // Click to copy
        $('.gens-ctc').on('click', function () {
            var that = this;
            var text = $(this).text();
            var copied = $(this).data('text');
            var raf_link = $(this).parent().find('strong').text();
            var copied;
            gensCopyText(raf_link);
            $(this).text(copied);
            setTimeout(function () {
                $(that).text(text);
            }, 1500);
        });

        // Generate referral link from guest email
        $('.gens-raf-generate-link').on('click', function () {
            var valid = true;
            var $email = $('.gens-raf-guest-email');
            if (!validateEmail($email.val())) {
                valid = false;
                $email.addClass('error');
            }
            if (valid === true) {
                $('.gens-raf__url strong').text($('.gens-raf__url strong').text() + '?raf=' + $email.val());
                $('.gens-referral_share')
                    .find('a')
                    .each(function () {
                        var $href = $(this).attr('href');
                        $(this).attr('href', $href + '?raf=' + $email.val());
                    });
                var $link = $('.gens-refer-a-friend').attr('data-link');
                $('.gens-refer-a-friend').attr('data-link', $link + '?raf=' + $email.val());
                $('.gens-refer-a-friend--generate').hide();
                $('.gens-refer-a-friend--guest').show();
                cookie.set('gens_raf_guest', $email.val(), {
                    expires: 7,
                    path: '/',
                });
            }
        });

        // Refresh on email change
        var typingTimer;
        var gensDoneTypingInterval = 1000;

        $('#billing_email').on('input', function () {
            window.clearTimeout(typingTimer);
            typingTimer = window.setTimeout(gensDoneTyping, gensDoneTypingInterval);
        });

        function validateEmail(email) {
            var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return re.test(String(email).toLowerCase());
        }

        function gensDoneTyping() {
            var val = $('#billing_email').val();
            if (val.indexOf('@') > -1) {
                jQuery(document.body).trigger('update_checkout');
            }
        }

        function gensAjaxSubmit() {
            var data = new Array();
            var link = $('.gens-refer-a-friend').data('link');
            $('.gens-referral_share__email__inputs').each(function () {
                var email = $(this).find("input[type='email']").val();
                var text = $(this).find("input[type='text']").val();
                if (email != '') {
                    var valueToPush = {};
                    valueToPush.email = email;
                    valueToPush.name = text;
                    data.push(valueToPush);
                }
            });

            jQuery.ajax({
                type: 'POST',
                url: gens_raf.ajax_url,
                data: {
                    data: data,
                    link: link,
                    action: 'gens_share_via_email',
                },
                success: function (data) {
                    // Remove form and say thx
                    var $success = "<div class='gens-raf-mail-share'>" + gens_raf.success_msg + '</div>';
                    $('.gens-referral_share__email__inputs').not(':first').remove();
                    $('.gens-referral_share__email__inputs').find('input').val('');
                    $('.gens-referral_share__email').after($success);
                    setTimeout(function (d) {
                        $('.gens-raf-mail-share').remove();
                    }, 4500);
                },
            });
            return false;
        }
    });
})(jQuery);
