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
                } else if ($(this).is(':radio')) {
                    // Only get value from checked radio button
                    if ($(this).is(':checked')) {
                        settings[name] = $(this).val();
                    }
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

    /**
     * Import Modal Handler
     */
    var ImportModal = {
        $modal: null,
        $form: null,
        $submitBtn: null,
        fileData: null,
        fileType: null, // 'csv' or 'excel'
        allowedExtensions: ['csv', 'xlsx', 'xls'],

        init: function() {
            this.$modal = $('#astats-import-modal');
            this.$form = $('#astats-import-form');
            this.$submitBtn = $('.astats-import-submit');

            if (!this.$modal.length) {
                return;
            }

            this.bindEvents();
        },

        bindEvents: function() {
            var self = this;

            // Open modal
            $(document).on('click', '.astats-import-btn', function(e) {
                e.preventDefault();
                self.openModal();
            });

            // Close modal
            $(document).on('click', '.astats-modal-close, .astats-modal-cancel, .astats-modal-overlay', function(e) {
                e.preventDefault();
                self.closeModal();
            });

            // Prevent closing when clicking inside modal content
            this.$modal.find('.astats-modal-content').on('click', function(e) {
                e.stopPropagation();
            });

            // File input change
            $(document).on('change', '#import-file', function(e) {
                self.handleFileSelect(e);
            });

            // Drag and drop
            var $fileUpload = this.$modal.find('.astats-file-upload');

            $fileUpload.on('dragover dragenter', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).addClass('dragover');
            });

            $fileUpload.on('dragleave drop', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).removeClass('dragover');
            });

            $fileUpload.on('drop', function(e) {
                var files = e.originalEvent.dataTransfer.files;
                if (files.length) {
                    $('#import-file')[0].files = files;
                    self.handleFileSelect({ target: $('#import-file')[0] });
                }
            });

            // Has header checkbox change
            $(document).on('change', 'input[name="has_header"]', function() {
                if (self.fileData) {
                    self.updatePreview();
                }
            });

            // Submit import
            this.$submitBtn.on('click', function(e) {
                e.preventDefault();
                self.submitImport();
            });
        },

        openModal: function() {
            this.$modal.fadeIn(200);
            this.resetForm();
            $('body').addClass('astats-modal-open');
        },

        closeModal: function() {
            this.$modal.fadeOut(200);
            $('body').removeClass('astats-modal-open');
        },

        resetForm: function() {
            this.$form[0].reset();
            this.fileData = null;
            this.fileType = null;
            this.$submitBtn.prop('disabled', true);
            this.$modal.find('.astats-import-preview').hide();
            this.$modal.find('.astats-file-name').text(astatsTablesAdmin.strings.chooseFile || 'Choose a CSV or Excel file, or drag it here');
        },

        handleFileSelect: function(e) {
            var file = e.target.files[0];

            if (!file) {
                return;
            }

            // Get file extension
            var fileName = file.name.toLowerCase();
            var fileExt = fileName.split('.').pop();

            // Validate file type
            if (this.allowedExtensions.indexOf(fileExt) === -1) {
                alert(astatsTablesAdmin.strings.invalidFile || 'Please select a CSV or Excel file (.csv, .xlsx, .xls)');
                return;
            }

            // Update file name display
            this.$modal.find('.astats-file-name').text(file.name);

            // Determine file type
            this.fileType = (fileExt === 'csv') ? 'csv' : 'excel';

            var self = this;

            if (this.fileType === 'csv') {
                // Read and parse CSV for preview
                var reader = new FileReader();

                reader.onload = function(event) {
                    self.fileData = self.parseCSV(event.target.result);
                    self.updatePreview();
                    self.$submitBtn.prop('disabled', false);
                };

                reader.readAsText(file);
            } else {
                // Excel files - can't preview client-side, just enable submit
                this.fileData = null;
                this.$modal.find('.astats-import-preview').hide();
                this.$modal.find('.astats-preview-info').text('Excel file selected. Preview not available for Excel files.');
                this.$modal.find('.astats-preview-table').html('');
                this.$modal.find('.astats-import-preview').slideDown();
                this.$submitBtn.prop('disabled', false);
            }
        },

        parseCSV: function(text) {
            var lines = text.split(/\r\n|\n/);
            var result = [];

            for (var i = 0; i < lines.length; i++) {
                var line = lines[i].trim();
                if (!line) continue;

                // Simple CSV parsing (handles basic cases)
                var row = [];
                var inQuotes = false;
                var field = '';

                for (var j = 0; j < line.length; j++) {
                    var char = line[j];

                    if (char === '"') {
                        inQuotes = !inQuotes;
                    } else if (char === ',' && !inQuotes) {
                        row.push(field.trim());
                        field = '';
                    } else {
                        field += char;
                    }
                }
                row.push(field.trim());
                result.push(row);
            }

            return result;
        },

        updatePreview: function() {
            if (!this.fileData || !this.fileData.length) {
                return;
            }

            var hasHeader = $('input[name="has_header"]').is(':checked');
            var $preview = this.$modal.find('.astats-import-preview');
            var $table = $preview.find('.astats-preview-table');
            var $info = $preview.find('.astats-preview-info');

            var headers = hasHeader ? this.fileData[0] : [];
            var dataRows = hasHeader ? this.fileData.slice(1) : this.fileData;
            var previewRows = dataRows.slice(0, 5); // Show first 5 rows

            // Build preview table
            var html = '<thead><tr>';

            if (hasHeader) {
                for (var i = 0; i < headers.length; i++) {
                    html += '<th>' + this.escapeHtml(headers[i]) + '</th>';
                }
            } else {
                for (var j = 0; j < (this.fileData[0] ? this.fileData[0].length : 0); j++) {
                    html += '<th>Column ' + (j + 1) + '</th>';
                }
            }

            html += '</tr></thead><tbody>';

            for (var k = 0; k < previewRows.length; k++) {
                html += '<tr>';
                for (var l = 0; l < previewRows[k].length; l++) {
                    html += '<td>' + this.escapeHtml(previewRows[k][l]) + '</td>';
                }
                html += '</tr>';
            }

            html += '</tbody>';

            $table.html(html);

            // Update info
            var colCount = this.fileData[0] ? this.fileData[0].length : 0;
            var rowCount = hasHeader ? this.fileData.length - 1 : this.fileData.length;

            $info.text(
                (astatsTablesAdmin.strings.previewInfo || 'Preview: {cols} columns, {rows} rows')
                    .replace('{cols}', colCount)
                    .replace('{rows}', rowCount)
            );

            $preview.slideDown();
        },

        escapeHtml: function(text) {
            var div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        },

        submitImport: function() {
            var title = $('#import-title').val().trim();
            var fileInput = $('#import-file')[0];

            if (!title) {
                alert(astatsTablesAdmin.strings.titleRequired);
                $('#import-title').focus();
                return;
            }

            if (!fileInput.files || !fileInput.files.length) {
                alert(astatsTablesAdmin.strings.noFile || 'Please select a file');
                return;
            }

            var self = this;
            var formData = new FormData();

            formData.append('action', 'astats_tables_import');
            formData.append('nonce', astatsTablesAdmin.nonce);
            formData.append('title', title);
            formData.append('has_header', $('input[name="has_header"]').is(':checked') ? '1' : '0');
            formData.append('import_file', fileInput.files[0]);

            this.$submitBtn.prop('disabled', true).text(astatsTablesAdmin.strings.importing || 'Importing...');

            $.ajax({
                url: astatsTablesAdmin.ajaxUrl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        if (response.data.redirect) {
                            window.location.href = response.data.redirect;
                        } else {
                            location.reload();
                        }
                    } else {
                        alert(response.data.message || astatsTablesAdmin.strings.error);
                        self.$submitBtn.prop('disabled', false).text(astatsTablesAdmin.strings.importBtn || 'Import Table');
                    }
                },
                error: function() {
                    alert(astatsTablesAdmin.strings.error);
                    self.$submitBtn.prop('disabled', false).text(astatsTablesAdmin.strings.importBtn || 'Import Table');
                }
            });
        }
    };

    $(document).ready(function() {
        TableEditor.init();
        ImportModal.init();
    });

})(jQuery);
