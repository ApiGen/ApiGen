// $( function() {
$(document).ready(function() {
    var $document = $(document);

    // Autocomplete search
    $.ui.autocomplete.prototype._renderItem = function (ul, item) {
        var highlightPattern = new RegExp('(' + this.term.replace(/\\/g, '\\\\') + ')', 'i');
        var highligthed = item.label.replace(highlightPattern, "<b>$1</b>");
        return $("<li></li>")
            .data("item.autocomplete", item)
            .append('<a>' + highligthed + '</a>')
            .appendTo(ul);
    };

    $("#search input[name=q]").autocomplete({
        source: ApiGen.elements,
        select: function (event, ui) {
            window.location.href = ui.item.file;
        }
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
