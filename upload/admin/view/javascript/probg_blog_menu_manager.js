(function($) {
    'use strict';

    $(function() {
        var $tab = $('#tab-menu');
        var $editors = $('#probg-blog-menu-editors');
        var $addButton = $('#button-add-probg-menu');

        if (!$tab.length || !$editors.length || !$addButton.length) {
            return;
        }

        var lang = (document.documentElement.getAttribute('lang') || '').toLowerCase();
        var isBg = lang.indexOf('bg') === 0;
        var text = isBg ? {
            title: 'Списък с менюта',
            search: 'Търси меню...',
            name: 'Име',
            status: 'Статус',
            content: 'Съдържание',
            category: 'Категория',
            display: 'Изглед',
            limit: 'Лимит',
            actions: 'Действия',
            edit: 'Редакция',
            remove: 'Премахни',
            back: 'Към списъка',
            empty: 'Няма създадени менюта.',
            noResults: 'Няма менюта, отговарящи на търсенето.',
            enabled: 'Включен',
            disabled: 'Изключен',
            blog: 'Блог',
            categories: 'Категории',
            articles: 'Статии',
            confirmRemove: 'Сигурни ли сте, че искате да премахнете това меню? Промяната ще се приложи след натискане на Запази.',
            unsaved: 'Промените по менюто се записват с основния бутон „Запази“.',
            hasErrors: 'Има полета за корекция'
        } : {
            title: 'Menu list',
            search: 'Search menus...',
            name: 'Name',
            status: 'Status',
            content: 'Content',
            category: 'Category',
            display: 'Display',
            limit: 'Limit',
            actions: 'Actions',
            edit: 'Edit',
            remove: 'Remove',
            back: 'Back to list',
            empty: 'No menus have been created yet.',
            noResults: 'No menus match your search.',
            enabled: 'Enabled',
            disabled: 'Disabled',
            blog: 'Blog',
            categories: 'Categories',
            articles: 'Articles',
            confirmRemove: 'Are you sure you want to remove this menu? The change is applied after clicking Save.',
            unsaved: 'Menu changes are stored with the main Save button.',
            hasErrors: 'Contains fields that need attention'
        };

        var $legacyAddWrap = $addButton.closest('.clearfix');
        var managerHtml = '' +
            '<div id="probg-blog-menu-manager" class="panel panel-default">' +
                '<div class="panel-heading clearfix">' +
                    '<h3 class="panel-title pull-left"><i class="fa fa-bars"></i> ' + text.title + ' <span class="badge" id="probg-blog-menu-count">0</span></h3>' +
                    '<div class="pull-right" id="probg-blog-menu-add-host"></div>' +
                '</div>' +
                '<div class="panel-body">' +
                    '<div class="row probg-blog-menu-toolbar">' +
                        '<div class="col-sm-6">' +
                            '<div class="input-group">' +
                                '<span class="input-group-addon"><i class="fa fa-search"></i></span>' +
                                '<input type="search" id="probg-blog-menu-search" class="form-control" placeholder="' + text.search.replace(/"/g, '&quot;') + '">' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                    '<div class="table-responsive">' +
                        '<table class="table table-bordered table-hover probg-blog-menu-table">' +
                            '<thead><tr>' +
                                '<th>' + text.name + '</th>' +
                                '<th>' + text.status + '</th>' +
                                '<th>' + text.content + '</th>' +
                                '<th>' + text.category + '</th>' +
                                '<th>' + text.display + '</th>' +
                                '<th class="text-center">' + text.limit + '</th>' +
                                '<th class="text-right">' + text.actions + '</th>' +
                            '</tr></thead>' +
                            '<tbody id="probg-blog-menu-list"></tbody>' +
                        '</table>' +
                    '</div>' +
                    '<div class="alert alert-info probg-blog-menu-save-note"><i class="fa fa-info-circle"></i> ' + text.unsaved + '</div>' +
                '</div>' +
            '</div>';

        var $manager = $(managerHtml);
        $legacyAddWrap.before($manager);
        $addButton.detach().appendTo('#probg-blog-menu-add-host');
        $legacyAddWrap.remove();
        $editors.addClass('probg-blog-menu-editors-managed').hide();
        $tab.addClass('probg-blog-menu-manager-enhanced');

        function field($editor, suffix) {
            return $editor.find('[name$="[' + suffix + ']"]').first();
        }

        function value($editor, suffix, fallback) {
            var $field = field($editor, suffix);
            return $field.length ? $field.val() : fallback;
        }

        function selectedText($editor, suffix, fallback) {
            var $field = field($editor, suffix);
            if (!$field.length) return fallback;
            var selected = $field.find('option:selected').text();
            return selected || fallback;
        }

        function boolValue($editor, suffix) {
            return String(value($editor, suffix, '0')) === '1';
        }

        function escapeHtml(value) {
            return $('<div>').text(value == null ? '' : String(value)).html();
        }

        function editorInfo($editor) {
            var row = String($editor.data('row'));
            var name = $.trim($editor.find('.probg-blog-menu-name').val() || 'ProBG Blog Menu');
            var enabled = boolValue($editor, 'status');
            var display = selectedText($editor, 'display', '');
            var category = selectedText($editor, 'category_id', '');
            var limit = value($editor, 'limit', '');
            var content = [];

            if (boolValue($editor, 'show_blog')) content.push(text.blog);
            if (boolValue($editor, 'show_categories')) content.push(text.categories);
            if (boolValue($editor, 'show_articles')) content.push(text.articles);

            return {
                row: row,
                name: name,
                enabled: enabled,
                display: display,
                category: category,
                limit: limit,
                content: content,
                hasErrors: $editor.find('.has-error, .text-danger').length > 0
            };
        }

        function contentBadges(items) {
            if (!items.length) return '<span class="text-muted">—</span>';
            return $.map(items, function(item) {
                return '<span class="label label-info probg-blog-menu-content-badge">' + escapeHtml(item) + '</span>';
            }).join(' ');
        }

        function rowHtml(info) {
            var statusClass = info.enabled ? 'label-success' : 'label-default';
            var statusText = info.enabled ? text.enabled : text.disabled;
            var errorBadge = info.hasErrors ? ' <span class="label label-danger" title="' + escapeHtml(text.hasErrors) + '"><i class="fa fa-exclamation-triangle"></i></span>' : '';

            return '' +
                '<tr data-menu-row="' + escapeHtml(info.row) + '" data-search="' + escapeHtml((info.name + ' ' + info.category + ' ' + info.display + ' ' + info.content.join(' ')).toLowerCase()) + '">' +
                    '<td><strong>' + escapeHtml(info.name) + '</strong>' + errorBadge + '</td>' +
                    '<td><span class="label ' + statusClass + '">' + statusText + '</span></td>' +
                    '<td>' + contentBadges(info.content) + '</td>' +
                    '<td>' + escapeHtml(info.category || '—') + '</td>' +
                    '<td>' + escapeHtml(info.display || '—') + '</td>' +
                    '<td class="text-center">' + escapeHtml(info.limit) + '</td>' +
                    '<td class="text-right probg-blog-menu-actions">' +
                        '<button type="button" class="btn btn-primary btn-sm button-edit-probg-menu" data-row="' + escapeHtml(info.row) + '" title="' + escapeHtml(text.edit) + '"><i class="fa fa-pencil"></i></button> ' +
                        '<button type="button" class="btn btn-danger btn-sm button-delete-probg-menu-list" data-row="' + escapeHtml(info.row) + '" title="' + escapeHtml(text.remove) + '"><i class="fa fa-trash"></i></button>' +
                    '</td>' +
                '</tr>';
        }

        function applyFilter() {
            var query = $.trim($('#probg-blog-menu-search').val() || '').toLowerCase();
            var visible = 0;

            $('#probg-blog-menu-list tr[data-menu-row]').each(function() {
                var $row = $(this);
                var match = !query || String($row.data('search') || '').indexOf(query) !== -1;
                $row.toggle(match);
                if (match) visible++;
            });

            $('#probg-blog-menu-list .probg-blog-menu-no-results').remove();
            if (query && !visible && $editors.children('.probg-blog-menu-editor').length) {
                $('#probg-blog-menu-list').append('<tr class="probg-blog-menu-no-results"><td colspan="7" class="text-center text-muted">' + escapeHtml(text.noResults) + '</td></tr>');
            }
        }

        function renderList() {
            var html = '';
            var count = 0;

            $editors.children('.probg-blog-menu-editor').each(function() {
                html += rowHtml(editorInfo($(this)));
                count++;
            });

            if (!count) {
                html = '<tr class="probg-blog-menu-empty"><td colspan="7" class="text-center text-muted"><i class="fa fa-bars"></i> ' + escapeHtml(text.empty) + '</td></tr>';
            }

            $('#probg-blog-menu-list').html(html);
            $('#probg-blog-menu-count').text(count);
            applyFilter();
        }

        function ensureEditorControls($editor) {
            var $heading = $editor.children('.panel-heading');
            if (!$heading.find('.button-probg-menu-list').length) {
                $('<button type="button" class="btn btn-default btn-xs pull-right button-probg-menu-list"><i class="fa fa-arrow-left"></i> ' + escapeHtml(text.back) + '</button>')
                    .css('margin-right', '6px')
                    .insertBefore($heading.find('.button-remove-probg-menu').first());
            }
        }

        function openEditor(row) {
            var $editor = $editors.children('.probg-blog-menu-editor[data-row="' + row + '"]');
            if (!$editor.length) return;

            ensureEditorControls($editor);
            $manager.hide();
            $editors.show();
            $editors.children('.probg-blog-menu-editor').hide();
            $editor.show();

            if (!$editor.find('.probg-menu-language-tabs li.active').length) {
                $editor.find('.probg-menu-language-tabs a:first').tab('show');
            }

            if ($editor[0] && $editor[0].scrollIntoView) {
                $editor[0].scrollIntoView({behavior: 'smooth', block: 'start'});
            }
        }

        function showList() {
            renderList();
            $editors.hide();
            $editors.children('.probg-blog-menu-editor').hide();
            $manager.show();
        }

        $editors.children('.probg-blog-menu-editor').each(function() {
            ensureEditorControls($(this));
        });

        renderList();

        $('#probg-blog-menu-search').on('input', applyFilter);

        $(document).on('click', '.button-edit-probg-menu', function() {
            openEditor(String($(this).data('row')));
        });

        $(document).on('click', '.button-probg-menu-list', function() {
            showList();
        });

        $(document).on('click', '.button-delete-probg-menu-list', function() {
            if (!window.confirm(text.confirmRemove)) return;
            var row = String($(this).data('row'));
            $editors.children('.probg-blog-menu-editor[data-row="' + row + '"]').remove();
            renderList();
        });

        $addButton.on('click.probgMenuManager', function() {
            window.setTimeout(function() {
                var $editor = $editors.children('.probg-blog-menu-editor').last();
                if (!$editor.length) return;
                ensureEditorControls($editor);
                renderList();
                openEditor(String($editor.data('row')));
            }, 0);
        });

        $(document).on('click.probgMenuManager', '.button-remove-probg-menu', function() {
            window.setTimeout(showList, 0);
        });

        $(document).on('input.probgMenuManager change.probgMenuManager', '#probg-blog-menu-editors input, #probg-blog-menu-editors select', function() {
            var $editor = $(this).closest('.probg-blog-menu-editor');
            var name = $.trim($editor.find('.probg-blog-menu-name').val() || 'ProBG Blog Menu');
            $editor.find('.probg-blog-menu-editor-title').text(name);
        });

        var $firstErrorEditor = $editors.children('.probg-blog-menu-editor').filter(function() {
            return $(this).find('.has-error, .text-danger').length > 0;
        }).first();

        if ($firstErrorEditor.length) {
            $('a[href="#tab-menu"]').tab('show');
            openEditor(String($firstErrorEditor.data('row')));
        }
    });
})(jQuery);
