/js/main.js!function () {
    "use strict";

    function forEach(nodeList, callback)
    {
        Array.prototype.forEach.call(nodeList, callback)
    }

    function adjustSidebarOverflowing() {
        var sidebar = document.body.querySelector('.sidebar')

        if (!sidebar)
        {
            return
        }

        sidebar.classList.add('computing')

        var menu = sidebar.querySelector('.sidebar nav')
        var max = 0

        forEach(menu.querySelectorAll('*'), function (el) {

            max = Math.max(max, el.scrollWidth)

        })

        var method = max > menu.clientWidth ? 'add' : 'remove'

        sidebar.classList[method]('overflowing')
        sidebar.classList.remove('computing')
    }

    function indexAssets()
    {
        var assets = {}

        forEach(document.querySelectorAll('[data-asset]'), function (asset) {

            assets[asset.getAttribute('data-asset')] = asset

        })

        return assets
    }

    function attachAnchors(icon)
    {
        forEach(document.querySelectorAll('a.anchor'), function (anchor) {

            anchor.appendChild(icon.cloneNode(true))

        })
    }

    jQuery(document).ready(function ($) {

        var assets = indexAssets()

        adjustSidebarOverflowing()
        attachAnchors(assets['icon-anchor'])

        /**
         * Events
         */
        $(document.body).on('click', '.sidebar-toggle', function () {

            document.body.classList.toggle('show-sidebar')
            this.setAttribute('aria-pressed', this.getAttribute('aria-pressed') == 'true' ? 'false' : 'true')

        })

        $(document.body).on('click', '.veil', function () {

            document.body.classList.remove('show-sidebar')

        })

        $(document.body).on('focus', '.search-query', function () {

            document.body.classList.add('in-search')

        })

        $(document.body).on('blur', '.search-query', function () {

            document.body.classList.remove('in-search')

        })

    })
} ()

$(window).load(function() {
    var $right = $('#right');
    var $groups = $('#groups');

    // Menu

    // Hide deep packages and namespaces
    $('ul span', $groups).click(function(event) {
        event.preventDefault();
        event.stopPropagation();
        $(this)
            .toggleClass('collapsed')
            .parent()
                .next('ul')
                    .toggleClass('collapsed');
    }).click();

    $active = $('ul li.active', $groups);
    if ($active.length > 0) {
        // Open active
        $('> a > span', $active).click();
    } else {
        $main = $('> ul > li.main', $groups);
        if ($main.length > 0) {
            // Open first level of the main project
            $('> a > span', $main).click();
        } else {
            // Open first level of all
            $('> ul > li > a > span', $groups).click();
        }
    }

    // Content

    // Search autocompletion
    var autocompleteFound = false;
    var $search = $('#search input[name=q]');
    $search
        .autocomplete(ApiGen.elements, {
            matchContains: true,
            max: 20,
            noRecord: '',
            highlight: function(value, term) {
                var term = term.toUpperCase().replace(/([\^\$\(\)\[\]\{\}\*\.\+\?\|\\])/gi, "\\$1").replace(/[A-Z0-9]/g, function(m, offset) {
                    return offset === 0 ? '(?:' + m + '|^' + m.toLowerCase() + ')' : '(?:(?:[^<>]|<[^<>]*>)*' + m + '|' + m.toLowerCase() + ')';
                });
                return value.replace(new RegExp("(?![^&;]+;)(?!<[^<>]*)(" + term + ")(?![^<>]*>)(?![^&;]+;)"), "<strong>$1</strong>");
            },
            formatItem: function(data) {
                return data.length > 1 ? data[1].replace(/^(.+\\)(.+)$/, '<span><small>$1</small>$2</span>') : data[0];
            },
            formatMatch: function(data) {
                return data[1];
            },
            formatResult: function(data) {
                return data[1];
            }
        }).result(function(event, data) {
            autocompleteFound = true;
            var location = window.location.href.split('/');
            location.pop();
            var parts = data[1].split(/::|$/);
            var file = '...';
            // @todo: use direct link instead of mm/mp/m...
            if (parts[1]) {
                file += '#' + ('mm' === data[0] || 'mp' === data[0] ? 'm' : '') + parts[1].replace(/([\w]+)\(\)/, '_$1');
            }
            location.push(file);
            window.location = location.join('/');
        }).closest('form')
            .submit(function() {
                var query = $search.val();
                if ('' === query) {
                    return false;
                }
                return !autocompleteFound && '' !== $('#search input[name=cx]').val();
            });

    // Select selected lines
    var matches = window.location.hash.substr(1).match(/^\d+(?:-\d+)?(?:,\d+(?:-\d+)?)*$/);
    if (null !== matches) {
        var lists = matches[0].split(',');
        for (var i = 0; i < lists.length; i++) {
            var lines = lists[i].split('-');
            lines[0] = parseInt(lines[0]);
            lines[1] = parseInt(lines[1] || lines[0]);
            for (var j = lines[0]; j <= lines[1]; j++) {
                $('#' + j).addClass('selected');
            }
        }

        var $firstLine = $('#' + parseInt(matches[0]));
        if ($firstLine.length > 0) {
            $right.scrollTop($firstLine.position().top);
        }
    }

    // Save selected lines
    var lastLine;
    $('.l a').click(function(event) {
        event.preventDefault();

        var selectedLine = $(this).parent().index() + 1;
        var $selectedLine = $('pre.code .l').eq(selectedLine - 1);

        if (event.shiftKey) {
            if (lastLine) {
                for (var i = Math.min(selectedLine, lastLine); i <= Math.max(selectedLine, lastLine); i++) {
                    $('#' + i).addClass('selected');
                }
            } else {
                $selectedLine.addClass('selected');
            }
        } else if (event.ctrlKey) {
            $selectedLine.toggleClass('selected');
        } else {
            var $selected = $('.l.selected')
                .not($selectedLine)
                .removeClass('selected');
            if ($selected.length > 0) {
                $selectedLine.addClass('selected');
            } else {
                $selectedLine.toggleClass('selected');
            }
        }

        lastLine = $selectedLine.hasClass('selected') ? selectedLine : null;

        // Update hash
        var lines = $('.l.selected')
            .map(function() {
                return parseInt($(this).attr('id'));
            })
            .get()
            .sort(function(a, b) {
                return a - b;
            });

        var hash = [];
        var list = [];
        for (var j = 0; j < lines.length; j++) {
            if (0 === j && j + 1 === lines.length) {
                hash.push(lines[j]);
            } else if (0 === j) {
                list[0] = lines[j];
            } else if (lines[j - 1] + 1 !== lines[j] && j + 1 === lines.length) {
                hash.push(list.join('-'));
                hash.push(lines[j]);
            } else if (lines[j - 1] + 1 !== lines[j]) {
                hash.push(list.join('-'));
                list = [lines[j]];
            } else if (j + 1 === lines.length) {
                list[1] = lines[j];
                hash.push(list.join('-'));
            } else {
                list[1] = lines[j];
            }
        }

        hash = hash.join(',');
        $backup = $('#' + hash).removeAttr('id');
        window.location.hash = hash;
        $backup.attr('id', hash);
    });
});
