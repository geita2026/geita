(function ($) {
    'use strict';

    const actionHeading = /^(actions?|options?|operations?|manage)$/i;
    const spreadsheet = 'input:not([type="hidden"]), select, textarea';

    function rowActions(cell) {
        const existing = cell.querySelector('[data-toggle="dropdown"], [data-bs-toggle="dropdown"]');
        if (existing) {
            existing.classList.add('gememis-table-more');
            existing.setAttribute('aria-label', 'Row actions');
            existing.setAttribute('aria-haspopup', 'true');
            if (!existing.hasAttribute('href') && existing.tagName === 'A') {
                existing.setAttribute('href', '#');
            }
            return;
        }

        const nodes = Array.from(cell.children);
        if (!nodes.length || cell.querySelector('.modal, .dropdown-menu, ' + spreadsheet)) {
            return;
        }
        const actions = 'a[href], a[data-toggle], button';
        const actionItems = nodes.filter(node => node.matches(actions) || node.querySelector(actions));
        if (actionItems.length !== nodes.length) {
            return;
        }

        const dropdown = document.createElement('div');
        dropdown.className = 'dropdown gememis-row-actions';
        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'gememis-table-more';
        button.setAttribute('data-toggle', 'dropdown');
        button.setAttribute('aria-label', 'Row actions');
        button.setAttribute('aria-haspopup', 'true');
        button.setAttribute('aria-expanded', 'false');
        const menu = document.createElement('ul');
        menu.className = 'dropdown-menu dropdown-menu-right gememis-row-action-menu';
        actionItems.forEach(item => {
            const entry = document.createElement('li');
            const action = item.matches(actions) ? item : item.querySelector(actions);
            const label = action.getAttribute('aria-label') || action.title || action.dataset.originalTitle;
            if (label && !action.textContent.trim()) {
                action.append(' ' + label);
            }
            entry.append(item);
            menu.append(entry);
        });
        dropdown.append(button, menu);
        cell.append(dropdown);
    }

    function enhance(table) {
        if (table.classList.contains('gememis-table') || $.fn.dataTable.isDataTable(table)
            || table.closest('[data-table-static]') || !table.tHead || !table.tBodies.length) {
            return;
        }

        const header = table.tHead.rows[table.tHead.rows.length - 1];
        if (!header || !header.cells.length) {
            return;
        }
        const headings = Array.from(header.cells);
        const actionColumns = headings.flatMap((cell, index) =>
            actionHeading.test(cell.textContent.trim() || cell.title) ? [index] : []);
        table.classList.add('gememis-table');
        const rows = Array.from(table.tBodies[0].rows);
        rows.forEach(row => {
            Array.from(row.cells).forEach((cell, index) => {
                if (actionColumns.includes(index) || cell.querySelector('.dropdown-menu')) {
                    rowActions(cell);
                }
            });
        });

        const hasComplexRows = rows.some(row => row.cells.length !== headings.length
            || Array.from(row.cells).some(cell => cell.colSpan > 1 || cell.rowSpan > 1));
        if (hasComplexRows || table.tBodies.length > 1 || table.querySelector(spreadsheet)) {
            return;
        }

        const paginated = table.hasAttribute('data-table-paginated');
        const searchable = table.dataset.tableSearch !== 'server';
        const controls = searchable ? 'f' : '';
        const footer = paginated ? '' : '<"gememis-table-footer"ilp>';
        const excluded = actionColumns.concat(headings[0].textContent.trim() === '#' ? [0] : []);
        const options = {
            autoWidth: false,
            order: [],
            paging: !paginated,
            searching: searchable,
            info: !paginated,
            pageLength: 25,
            lengthMenu: [10, 25, 50, 100],
            stateSave: !paginated && table.classList.contains('datatable-save-state'),
            columnDefs: excluded.length ? [{ targets: excluded, orderable: false, searchable: false }] : [],
            dom: (controls ? '<"gememis-table-toolbar"' + controls + '>' : '')
                + '<"gememis-table-scroll"t>' + footer,
            language: {
                search: '_INPUT_',
                searchPlaceholder: paginated ? 'Search this page' : 'Search table',
                lengthMenu: 'Rows per page _MENU_',
                info: '_START_–_END_ of _TOTAL_',
                infoEmpty: '0 rows',
                infoFiltered: ' (filtered from _MAX_)',
                emptyTable: 'No records to display.',
                zeroRecords: 'No matching records.',
                paginate: { previous: '‹', next: '›' },
                aria: { paginate: { previous: 'Previous page', next: 'Next page' } },
            },
        };
        const api = $(table).DataTable(options);
        const label = paginated ? 'Search the current page' : 'Search table';
        $(api.table().container()).find('input[type="search"]').attr('aria-label', label);
    }

    function positionMenu(dropdown) {
        const trigger = dropdown.querySelector('.gememis-table-more');
        const menu = dropdown.querySelector('.dropdown-menu');
        if (!trigger || !menu) {
            return;
        }
        const rect = trigger.getBoundingClientRect();
        menu.classList.add('gememis-table-floating-menu');
        menu.style.left = Math.max(8, Math.min(rect.right - menu.offsetWidth,
            document.documentElement.clientWidth - menu.offsetWidth - 8)) + 'px';
        const roomBelow = window.innerHeight - rect.bottom;
        const roomAbove = rect.top;
        const above = menu.offsetHeight + 8 > roomBelow && roomAbove > roomBelow;
        menu.style.maxHeight = Math.max(40, (above ? roomAbove : roomBelow) - 8) + 'px';
        menu.style.top = (above ? Math.max(8, rect.top - menu.offsetHeight - 4) : rect.bottom + 4) + 'px';
    }

    $(function () {
        if (!$.fn.dataTable) {
            return;
        }
        $('.gememis-app table.table, table[data-table-paginated], table.datatable-basic, table.datatable-pagination, '
            + 'table.datatable-save-state, table.datatable-scroll-y, table.datatable-button-html5-columns')
            .each(function () {
                enhance(this);
            });

        $(document).on('shown.bs.dropdown', '.gememis-table .dropdown, .gememis-table .btn-group', function (event) {
            if (event.target === this) {
                positionMenu(this);
            }
        }).on('hidden.bs.dropdown', '.gememis-table .dropdown, .gememis-table .btn-group', function (event) {
            if (event.target === this) {
                const menu = this.querySelector('.gememis-table-floating-menu');
                if (menu) {
                    menu.classList.remove('gememis-table-floating-menu');
                    ['top', 'left', 'max-height'].forEach(property => menu.style.removeProperty(property));
                }
            }
        });

        function refreshOpenMenus(event) {
            document.querySelectorAll('.gememis-table-floating-menu').forEach(menu => {
                if (!(event.target instanceof Node) || !menu.contains(event.target)) {
                    positionMenu(menu.parentElement);
                }
            });
        }

        document.addEventListener('scroll', refreshOpenMenus, true);
        window.addEventListener('resize', refreshOpenMenus);
    });
})(jQuery);
