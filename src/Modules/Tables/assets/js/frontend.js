/**
 * AStats Tables - Frontend JavaScript
 */

(function($) {
    'use strict';

    var AStatsTables = {
        init: function() {
            $('.astats-table-wrapper').each(function() {
                var $wrapper = $(this);
                var instance = new TableInstance($wrapper);
                instance.init();
            });
        }
    };

    function TableInstance($wrapper) {
        this.$wrapper = $wrapper;
        this.$table = $wrapper.find('.astats-table');
        this.$tbody = this.$table.find('tbody');
        this.$rows = this.$tbody.find('tr');

        this.enableSearch = $wrapper.data('search') === true;
        this.enableSorting = $wrapper.data('sorting') === true;
        this.enablePagination = $wrapper.data('pagination') === true;
        this.perPage = parseInt($wrapper.data('per-page'), 10) || 10;

        this.currentPage = 1;
        this.sortColumn = null;
        this.sortDirection = 'asc';
        this.filteredRows = this.$rows;
    }

    TableInstance.prototype = {
        init: function() {
            if (this.enableSearch) {
                this.initSearch();
            }

            if (this.enableSorting) {
                this.initSorting();
            }

            if (this.enablePagination) {
                this.initPagination();
            }
        },

        initSearch: function() {
            var self = this;
            var $input = this.$wrapper.find('.astats-search-input');
            var debounceTimer;

            $input.on('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function() {
                    self.search($input.val());
                }, 200);
            });
        },

        search: function(query) {
            var self = this;
            query = query.toLowerCase().trim();

            if (!query) {
                this.filteredRows = this.$rows;
                this.$rows.removeClass('astats-hidden');
            } else {
                this.$rows.each(function() {
                    var $row = $(this);
                    var text = $row.text().toLowerCase();
                    var match = text.indexOf(query) !== -1;

                    $row.toggleClass('astats-hidden', !match);
                });

                this.filteredRows = this.$rows.not('.astats-hidden');
            }

            // Reset to first page after search
            this.currentPage = 1;

            if (this.enablePagination) {
                this.updatePagination();
            }
        },

        initSorting: function() {
            var self = this;

            this.$table.find('th.astats-sortable').on('click', function() {
                var $th = $(this);
                var colIndex = $th.index();

                // Toggle direction
                if (self.sortColumn === colIndex) {
                    self.sortDirection = self.sortDirection === 'asc' ? 'desc' : 'asc';
                } else {
                    self.sortColumn = colIndex;
                    self.sortDirection = 'asc';
                }

                // Update UI
                self.$table.find('th.astats-sortable').removeClass('asc desc');
                $th.addClass(self.sortDirection);

                // Sort
                self.sort(colIndex, self.sortDirection);
            });
        },

        sort: function(colIndex, direction) {
            var self = this;
            var rows = this.$rows.toArray();

            rows.sort(function(a, b) {
                var aVal = $(a).find('td').eq(colIndex).text().trim();
                var bVal = $(b).find('td').eq(colIndex).text().trim();

                // Try numeric sort first
                var aNum = parseFloat(aVal);
                var bNum = parseFloat(bVal);

                if (!isNaN(aNum) && !isNaN(bNum)) {
                    return direction === 'asc' ? aNum - bNum : bNum - aNum;
                }

                // Fall back to string sort
                aVal = aVal.toLowerCase();
                bVal = bVal.toLowerCase();

                if (aVal < bVal) return direction === 'asc' ? -1 : 1;
                if (aVal > bVal) return direction === 'asc' ? 1 : -1;
                return 0;
            });

            // Re-append sorted rows
            this.$tbody.append(rows);

            // Update references
            this.$rows = this.$tbody.find('tr');
            this.filteredRows = this.$rows.not('.astats-hidden');

            if (this.enablePagination) {
                this.updatePagination();
            }
        },

        initPagination: function() {
            var self = this;
            var $pagination = this.$wrapper.find('.astats-table-pagination');

            $pagination.find('.astats-page-prev').on('click', function() {
                if (self.currentPage > 1) {
                    self.currentPage--;
                    self.updatePagination();
                }
            });

            $pagination.find('.astats-page-next').on('click', function() {
                var totalPages = Math.ceil(self.filteredRows.length / self.perPage);
                if (self.currentPage < totalPages) {
                    self.currentPage++;
                    self.updatePagination();
                }
            });

            this.updatePagination();
        },

        updatePagination: function() {
            var self = this;
            var totalRows = this.filteredRows.length;
            var totalPages = Math.ceil(totalRows / this.perPage);

            if (this.currentPage > totalPages) {
                this.currentPage = Math.max(1, totalPages);
            }

            var start = (this.currentPage - 1) * this.perPage;
            var end = start + this.perPage;

            // Hide all rows first
            this.$rows.addClass('astats-hidden');

            // Show only current page rows (from filtered set)
            this.filteredRows.slice(start, end).removeClass('astats-hidden');

            // Update pagination UI
            var $pagination = this.$wrapper.find('.astats-table-pagination');
            var $prevBtn = $pagination.find('.astats-page-prev');
            var $nextBtn = $pagination.find('.astats-page-next');
            var $info = $pagination.find('.astats-page-info');

            $prevBtn.prop('disabled', this.currentPage <= 1);
            $nextBtn.prop('disabled', this.currentPage >= totalPages);

            if (totalRows === 0) {
                $info.text('No results');
            } else {
                $info.text('Page ' + this.currentPage + ' of ' + totalPages + ' (' + totalRows + ' rows)');
            }
        }
    };

    $(document).ready(function() {
        AStatsTables.init();
    });

})(jQuery);
