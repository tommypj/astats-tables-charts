/**
 * AStats Tables - Admin JavaScript
 */

(function($) {
    'use strict';

    var TableEditor = {
        init: function() {
            this.bindEvents();
            this.initThemeSelector();
        },

        bindEvents: function() {
            // Add column
            $(document).on('click', '.astats-add-column', this.addColumn.bind(this));

            // Add row
            $(document).on('click', '.astats-add-row', this.addRow.bind(this));

            // Delete column
            $(document).on('click', '.astats-delete-column', this.deleteColumn.bind(this));

            // Delete row
            $(document).on('click', '.astats-delete-row', this.deleteRow.bind(this));

            // Save table
            $(document).on('submit', '#astats-table-editor-form', this.saveTable.bind(this));

            // Delete table from list
            $(document).on('click', '.astats-delete-table', this.deleteTable.bind(this));

            // Copy shortcode
            $(document).on('click', '.astats-shortcode', this.copyShortcode.bind(this));

            // Pagination toggle
            $(document).on('change', 'input[name="settings[enable_pagination]"]', function() {
                $('.astats-pagination-rows').toggle($(this).is(':checked'));
            });

            // Theme selection
            $(document).on('change', 'input[name="settings[theme]"]', function() {
                $('.astats-theme-option').removeClass('selected');
                $(this).closest('.astats-theme-option').addClass('selected');
            });
        },

        initThemeSelector: function() {
            // Set initial selected state
            $('input[name="settings[theme]"]:checked').closest('.astats-theme-option').addClass('selected');
        },

        addColumn: function(e) {
            e.preventDefault();

            var $table = $('#table-grid');
            var $headerRow = $table.find('thead tr');
            var $bodyRows = $table.find('tbody tr');
            var colCount = $headerRow.find('th').length - 1; // -1 for actions column

            // Add header
            var $newHeader = $('<th>' +
                '<div class="astats-column-header">' +
                    '<input type="text" class="astats-column-input" value="Column ' + (colCount + 1) + '" placeholder="Column name">' +
                    '<button type="button" class="astats-delete-column" title="Delete column">' +
                        '<span class="dashicons dashicons-no-alt"></span>' +
                    '</button>' +
                '</div>' +
            '</th>');

            $headerRow.find('.astats-row-actions-header').before($newHeader);

            // Add cells to each row
            $bodyRows.each(function() {
                var $newCell = $('<td>' +
                    '<div class="astats-cell" contenteditable="true" data-col="' + colCount + '"></div>' +
                '</td>');
                $(this).find('.astats-row-actions').before($newCell);
            });
        },

        addRow: function(e) {
            e.preventDefault();

            var $table = $('#table-grid');
            var $tbody = $table.find('tbody');
            var colCount = $table.find('thead th').length - 1;

            var rowHtml = '<tr>';
            for (var i = 0; i < colCount; i++) {
                rowHtml += '<td><div class="astats-cell" contenteditable="true" data-col="' + i + '"></div></td>';
            }
            rowHtml += '<td class="astats-row-actions">' +
                '<button type="button" class="astats-delete-row" title="Delete row">' +
                    '<span class="dashicons dashicons-no-alt"></span>' +
                '</button>' +
            '</td></tr>';

            $tbody.append(rowHtml);
        },

        deleteColumn: function(e) {
            e.preventDefault();

            var $table = $('#table-grid');
            var colCount = $table.find('thead th').length - 1;

            if (colCount <= 1) {
                alert('Cannot delete the last column');
                return;
            }

            var $th = $(e.target).closest('th');
            var colIndex = $th.index();

            // Remove header
            $th.remove();

            // Remove cells from each row
            $table.find('tbody tr').each(function() {
                $(this).find('td').eq(colIndex).remove();
            });

            // Update data-col attributes
            this.updateColumnIndexes();
        },

        deleteRow: function(e) {
            e.preventDefault();

            var $table = $('#table-grid');
            var rowCount = $table.find('tbody tr').length;

            if (rowCount <= 1) {
                alert('Cannot delete the last row');
                return;
            }

            $(e.target).closest('tr').remove();
        },

        updateColumnIndexes: function() {
            $('#table-grid tbody tr').each(function() {
                $(this).find('.astats-cell').each(function(index) {
                    $(this).attr('data-col', index);
                });
            });
        },

        getTableData: function() {
            var columns = [];
            var rows = [];

            // Get columns
            $('#table-grid thead .astats-column-input').each(function() {
                columns.push($(this).val());
            });

            // Get rows
            $('#table-grid tbody tr').each(function() {
                var rowData = {};
                $(this).find('.astats-cell').each(function() {
                    var col = $(this).data('col');
                    rowData['col_' + col] = $(this).text();
                });
                rows.push(rowData);
            });

            return {
                columns: columns,
                rows: rows
            };
        },

        saveTable: function(e) {
            e.preventDefault();

            var $form = $(e.target);
            var $button = $('#save-table');
            var $status = $('.astats-save-status');
            var title = $('#table-title').val().trim();

            if (!title) {
                alert(astatsTablesAdmin.strings.titleRequired);
                $('#table-title').focus();
                return;
            }

            var tableData = this.getTableData();

            // Build settings
            var settings = {};
            $form.find('[name^="settings["]').each(function() {
                var name = $(this).attr('name').match(/settings\[(\w+)\]/)[1];
                if ($(this).is(':checkbox')) {
                    settings[name] = $(this).is(':checked') ? '1' : '0';
                } else {
                    settings[name] = $(this).val();
                }
            });

            $button.prop('disabled', true).text(astatsTablesAdmin.strings.saving);
            $status.text('');

            $.ajax({
                url: astatsTablesAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'astats_tables_save',
                    nonce: astatsTablesAdmin.nonce,
                    table_id: $form.find('[name="table_id"]').val(),
                    title: title,
                    description: $('#table-description').val(),
                    columns: tableData.columns,
                    rows: tableData.rows,
                    settings: settings
                },
                success: function(response) {
                    if (response.success) {
                        $status.text(astatsTablesAdmin.strings.saved);

                        // Redirect to edit page if new table
                        if (!$form.find('[name="table_id"]').val()) {
                            window.location.href = 'admin.php?page=astats-tables&action=edit&id=' + response.data.table_id;
                        }

                        setTimeout(function() {
                            $status.fadeOut(function() {
                                $(this).text('').show();
                            });
                        }, 3000);
                    } else {
                        alert(response.data.message || astatsTablesAdmin.strings.error);
                    }
                },
                error: function() {
                    alert(astatsTablesAdmin.strings.error);
                },
                complete: function() {
                    $button.prop('disabled', false).text('Save Table');
                }
            });
        },

        deleteTable: function(e) {
            e.preventDefault();

            if (!confirm(astatsTablesAdmin.strings.confirmDelete)) {
                return;
            }

            var $button = $(e.target).closest('button');
            var $row = $button.closest('tr');
            var tableId = $button.data('table-id');

            $button.prop('disabled', true).text(astatsTablesAdmin.strings.deleting);

            $.ajax({
                url: astatsTablesAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'astats_tables_delete',
                    nonce: astatsTablesAdmin.nonce,
                    table_id: tableId
                },
                success: function(response) {
                    if (response.success) {
                        $row.fadeOut(function() {
                            $(this).remove();

                            // Show empty state if no tables left
                            if ($('.astats-tables-table tbody tr').length === 0) {
                                location.reload();
                            }
                        });
                    } else {
                        alert(response.data.message || astatsTablesAdmin.strings.error);
                        $button.prop('disabled', false).text('Delete');
                    }
                },
                error: function() {
                    alert(astatsTablesAdmin.strings.error);
                    $button.prop('disabled', false).text('Delete');
                }
            });
        },

        copyShortcode: function(e) {
            var text = $(e.target).text();

            if (navigator.clipboard) {
                navigator.clipboard.writeText(text).then(function() {
                    var $el = $(e.target);
                    var originalText = $el.text();
                    $el.text('Copied!');
                    setTimeout(function() {
                        $el.text(originalText);
                    }, 1500);
                });
            }
        }
    };

    $(document).ready(function() {
        TableEditor.init();
    });

})(jQuery);
