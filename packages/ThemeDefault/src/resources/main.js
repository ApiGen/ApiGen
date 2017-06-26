// $( function() {
$(document).ready(function() {
    var $document = $(document);

    // Content
/*
    var availableTags = [
        "ActionScript",
        "Able"
    ];

    $('#search input[name=q]').autocomplete({
        source: availableTags // ApiGen.elements
    });

    // Search autocompletion
    var autocompleteFound = false;
    var $search = $('#search input[name=q]');
    $search
        .autocomplete(ApiGen.elements, {
            matchContains: true,
            scrollHeight: 200,
            max: 5,
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
            },
            show: function($list) {
                var $items = $('li span', $list);
                var maxWidth = Math.max.apply(null, $items.map(function() {
                    return $(this).width();
                }));
                // 10px padding
                $list
                    .width(Math.max(maxWidth + 10, $search.innerWidth()))
                    .css('left', $search.offset().left + $search.outerWidth() - $list.outerWidth());
            }
        }).result(function(event, data) {
            autocompleteFound = true;
            var location = window.location.href.split('/');
            location.pop();
            // var parts = data[1].split(/::|$/);
            var file = data[0];

            console.log(file);

            // if (parts[1]) {
            //     file = data[0];
            // }
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
*/


    // Select selected lines
    var matches = window.location.hash.substr(1).match(/^\d+(?:-\d+)?(?:,\d+(?:-\d+)?)*$/);
    if (null !== matches) {
        var lists = matches[0].split(',');
        for (var i = 0; i < lists.length; i++) {
            var lines = lists[i].split('-');
            lines[0] = parseInt(lines[0]);
            lines[1] = parseInt(lines[1] || lines[0]);
            for (var j = lines[0]; j <= lines[1]; j++) {
                $('#' + j + ', #line-' + j).addClass('selected');
            }
        }

        var $firstLine = $('#' + parseInt(matches[0]));
        if ($firstLine.length > 0) {
            $document.scrollTop($firstLine.offset().top);
        }
    }

    // Save selected lines
    var lastLine;
    $('.numbers .l a').click(function(event) {
        event.preventDefault();

        var selectedLine = $(this).parent().index() + 1;

        if (event.shiftKey) {
            if (lastLine) {
                for (var i = Math.min(selectedLine, lastLine); i <= Math.max(selectedLine, lastLine); i++) {
                    $('#' + i + ', #line-' + i).addClass('selected');
                }
            } else {
                 $('#' + selectedLine + ', #line-' + selectedLine).addClass('selected');
            }
        } else if (event.ctrlKey) {
              $('#' + selectedLine + ', #line-' + selectedLine).toggleClass('selected');
        } else {
           var selected = $('.l.selected').not('#' + selectedLine + ', #line-' + selectedLine).removeClass('selected');
           $('#' + selectedLine + ', #line-' + selectedLine).addClass('selected');
        }

        lastLine = $('#' + selectedLine).hasClass('selected') ? selectedLine : null;

        // Update hash
        var lines = $('.numbers .l.selected')
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

        hash = '#' + hash.join(',');
        history.pushState(null, null, hash);
    });
});
