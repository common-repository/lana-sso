jQuery(function () {

    jQuery('.settings_page_lana-sso-settings').on('click', '.copy-to-clipboard', function (e) {
        e.preventDefault();

        var $targetElement = jQuery(jQuery(this).data('target'));

        navigator.clipboard.writeText($targetElement.text()).then(
            function () {
                toastr.info(lana_sso_l10n['copied_to_clipboard'])
            },
            function () {
                toastr.warning(lana_sso_l10n['browser_not_support_clipboard_api'])
            }
        );
    });
});