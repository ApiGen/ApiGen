// $( function() {
$(document).ready(function() {
    var $document = $(document);

    // Search autocompletion
    var autocompleteFound = false;
    var fuzzySet = FuzzySet();
    var searchable = Object.keys(ApiGen.elements);
    for (var i = 0; i < searchable.length; i++) {
        fuzzySet.add(searchable[i]);
    }
    var $search = $('#search input[name=q]');
    $search
        .autocomplete({
            source: function(req, responseFn) {
                var result = fuzzySet.get(req.term, undefined, 0.1).sort(function (a, b) {
                    if (a[0] < b[0]) {
                        return 1;
                    } else if (a[0] > b[0]) {
                        return -1;
                    } else {
                        return 0;
                    }
                }).reduce(function (a, b) {
                    a.push(b[1]);
                    return a;
                }, []);
                responseFn(result);
            },
            matchContains: true,
            scrollHeight: 200,
            max: 5,
            noRecord: '',
            select: function(event, data) {
                autocompleteFound = true;
                var location = window.location.href.split('/');
                location.pop();
                var file = ApiGen.elements[data.item.value];
                location.push(file);
                window.location = location.join('/');
            }
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
