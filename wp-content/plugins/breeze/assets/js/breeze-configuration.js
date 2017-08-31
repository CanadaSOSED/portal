jQuery(document).ready(function($){
    // database clean tabs
    $('input[name="all_control"]').click(function () {
        var checked = $(this).is(':checked');
        if (checked == true) {
            $(".clean-data").prop("checked", true);
        } else {
            $(".clean-data").prop("checked", false);
        }
    });

    $('.clean-data').click(function () {
        var checked = $(this).is(':checked');
        if (checked == false) {
            $('input[name="all_control"]').prop('checked', false);
        }
    });

    // Tab
    $("#breeze-tabs .nav-tab").click(function (e) {
        e.preventDefault();
        $("#breeze-tabs .nav-tab").removeClass('active');
        $(e.target).addClass('active');
        id_tab = $(this).data('tab-id');
        $("#tab-" + id_tab).addClass('active');
        $("#breeze-tabs-content .tab-pane").removeClass('active');
        $("#tab-content-" + id_tab).addClass('active');
        document.cookie = 'breeze_active_tab=' + id_tab;
    });

    // Cookie do
    function setTabFromCookie() {
        active_tab = getCookie('breeze_active_tab');
        if (!active_tab){
            active_tab = 'basic';
        }
        $("#tab-" + active_tab).addClass('active');
        $("#tab-content-" + active_tab).addClass('active');
    }

    function getCookie(cname) {
        var name = cname + "=";
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') c = c.substring(1);
            if (c.indexOf(name) == 0) return c.substring(name.length, c.length);
        }
        return "";
    }

    setTabFromCookie();

});