/**
 * AStats Tables & Charts - Admin JavaScript
 */

(function($) {
    'use strict';

    /**
     * Module Toggle Handler
     */
    function initModuleToggles() {
        $(document).on('change', '.astats-module-toggle', function() {
            var $toggle = $(this);
            var $row = $toggle.closest('.astats-module-row, .astats-module-card');
            var module = $toggle.data('module');
            var action = $toggle.is(':checked') ? 'activate' : 'deactivate';

            // Disable toggle during request
            $toggle.prop('disabled', true);
            $row.addClass('astats-loading');

            $.ajax({
                url: astatsAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'astats_toggle_module',
                    nonce: astatsAdmin.nonce,
                    module: module,
                    module_action: action
                },
                success: function(response) {
                    if (response.success) {
                        $row.toggleClass('is-active', response.data.active);

                        // Update status text if present
                        var $status = $row.find('.astats-module-status');
                        if ($status.length) {
                            $status
                                .toggleClass('active', response.data.active)
                                .toggleClass('inactive', !response.data.active)
                                .text(response.data.active ? 'Active' : 'Inactive');
                        }

                        // Reload page if on dashboard to update buttons
                        if ($row.hasClass('astats-module-card')) {
                            location.reload();
                        }
                    } else {
                        // Revert toggle on error
                        $toggle.prop('checked', !$toggle.is(':checked'));
                        alert(response.data.message || astatsAdmin.strings.error);
                    }
                },
                error: function() {
                    // Revert toggle on error
                    $toggle.prop('checked', !$toggle.is(':checked'));
                    alert(astatsAdmin.strings.error);
                },
                complete: function() {
                    $toggle.prop('disabled', false);
                    $row.removeClass('astats-loading');
                }
            });
        });

        // Dashboard activate button
        $(document).on('click', '.astats-activate-module', function(e) {
            e.preventDefault();
            var module = $(this).data('module');
            var $card = $(this).closest('.astats-module-card');
            var $button = $(this);

            $button.prop('disabled', true).text(astatsAdmin.strings.activating);
            $card.addClass('astats-loading');

            $.ajax({
                url: astatsAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'astats_toggle_module',
                    nonce: astatsAdmin.nonce,
                    module: module,
                    module_action: 'activate'
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert(response.data.message || astatsAdmin.strings.error);
                        $button.prop('disabled', false).text('Activate');
                        $card.removeClass('astats-loading');
                    }
                },
                error: function() {
                    alert(astatsAdmin.strings.error);
                    $button.prop('disabled', false).text('Activate');
                    $card.removeClass('astats-loading');
                }
            });
        });
    }

    /**
     * Settings Form Handler
     */
    function initSettingsForm() {
        $('#astats-settings-form').on('submit', function(e) {
            e.preventDefault();

            var $form = $(this);
            var $button = $('#astats-save-settings');
            var $status = $('.astats-save-status');
            var formData = $form.serializeArray();

            // Build settings object
            var settings = {};
            $.each(formData, function(i, field) {
                var match = field.name.match(/settings\[(\w+)\]/);
                if (match) {
                    settings[match[1]] = field.value;
                }
            });

            $button.prop('disabled', true).text(astatsAdmin.strings.saving);
            $status.text('');

            $.ajax({
                url: astatsAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'astats_save_settings',
                    nonce: astatsAdmin.nonce,
                    settings: settings
                },
                success: function(response) {
                    if (response.success) {
                        $status.text(astatsAdmin.strings.saved);
                        setTimeout(function() {
                            $status.fadeOut(function() {
                                $(this).text('').show();
                            });
                        }, 3000);
                    } else {
                        alert(response.data.message || astatsAdmin.strings.error);
                    }
                },
                error: function() {
                    alert(astatsAdmin.strings.error);
                },
                complete: function() {
                    $button.prop('disabled', false).text('Save Settings');
                }
            });
        });
    }

    /**
     * Initialize
     */
    $(document).ready(function() {
        initModuleToggles();
        initSettingsForm();
    });

})(jQuery);
