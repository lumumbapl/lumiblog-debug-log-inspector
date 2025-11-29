/**
 * Debug Log Inspector - Admin JavaScript
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        
        // Auto-dismiss notices after 5 seconds
        setTimeout(function() {
            $('.notice.is-dismissible').fadeOut();
        }, 5000);

        // Confirm before deleting a plugin
        $('.dli-delete-plugin').on('click', function(e) {
            if (!confirm('Are you sure you want to delete this plugin from monitoring?')) {
                e.preventDefault();
                return false;
            }
        });

        // Add visual feedback when toggling plugin status
        $('.dli-toggle-btn').on('click', function() {
            $(this).closest('tr').css('opacity', '0.5');
        });

        // Validate form before submission
        $('form').on('submit', function(e) {
            var action = $(this).find('input[name="debug_log_inspector_action"]').val();
            
            if (action === 'add_plugin' || action === 'edit_plugin') {
                var pluginName = $('#plugin_name').val().trim();
                var searchTerms = $('#plugin_search_terms').val().trim();
                
                if (pluginName === '') {
                    alert('Please enter a plugin name.');
                    $('#plugin_name').focus();
                    e.preventDefault();
                    return false;
                }
                
                if (searchTerms === '') {
                    alert('Please enter at least one search term.');
                    $('#plugin_search_terms').focus();
                    e.preventDefault();
                    return false;
                }
            }
        });

        // Highlight the form when in edit mode
        if (window.location.search.indexOf('edit=') > -1) {
            $('.dli-card').first().css({
                'border-color': '#2271b1',
                'border-width': '2px'
            });
        }

    });

})(jQuery);
