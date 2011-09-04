$(function() {

	// Menu

	var $groups = $('#groups');

	// Hide deep packages and namespaces
	$('ul span', $groups).click(function() {
		$(this)
			.toggleClass('collapsed')
			.next('ul')
				.toggleClass('collapsed');
	}).click();

	$active = $('ul li.active', $groups);
	if ($active.length > 0) {
		// Open active
		$('> span', $active).click();
	} else {
		$main = $('> ul > li.main', $groups);
		if ($main.length > 0) {
			// Open first level of the main project
			$('> span', $main).click();
		} else {
			// Open first level of all
			$('> ul > li > span', $groups).click();
		}
	}

	// Content

	var $content = $('#content');

	// Search autocompletion
	var autocompleteFound = false;
	var $search = $('#search input[name=q]');
	$search
		.autocomplete(ApiGen.elements, {
			matchContains: true,
			scrollHeight: 200,
			max: 20,
			formatItem: function(data) {
				return data[1].replace(/^(.+\\)(.+)$/, '<small>$1</small>$2');
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
			location.push(data[0] + '-' + data[1].replace(/[^\w]/g, '.') + '.html');
			window.location = location.join('/');
		}).closest('form')
			.submit(function() {
				var query = $search.val();
				if ('' === query) {
					return false;
				}

				var label = $('#search input[name=more]').val();
				if (!autocompleteFound && label && -1 === query.indexOf('more:')) {
					$search.val(query + ' more:' + label);
				}

				return !autocompleteFound && '' !== $('#search input[name=cx]').val();
			});

	// Save original order
	$('table.summary tr[data-order]', $content).each(function(index) {
		do {
			index = '0' + index;
		} while (index.length < 3);
		$(this).attr('data-orig-order', index);
	});

	// Switch between natural and alphabetical order
	var $caption = $('table.summary', $content)
		.filter(':has(tr[data-order])')
			.find('caption');
	$caption
		.click(function() {
			var $this = $(this);
			var sorted = !$this.data('sorted');
			$this.data('sorted', sorted);
			$.cookie('sorted', sorted, {expires: 365});
			var attr = sorted ? 'data-order' : 'data-orig-order';
			$this
				.closest("table")
					.find('tr').sortElements(function(a, b) {
						return $(a).attr(attr) > $(b).attr(attr) ? 1 : -1;
					});
			return false;
		})
		.addClass('switchable')
		.attr('title', 'Switch between natural and alphabetical order');
	if ('true' === $.cookie('sorted')) {
		$caption.click();
	}

	// Delayed hover efect on summary
	if (ApiGen.options.elementDetailsCollapsed) {
		var timeout;
		$('tr', $content).filter(':has(.detailed)')
			.hover(function() {
				clearTimeout(timeout);
				var $this = $(this);
				timeout = setTimeout(function() {
					$('.short', $this).hide();
					$('.detailed', $this).show();
				}, 500);
			}, function() {
				clearTimeout(timeout);
			}).click(function() { // Immediate hover effect on summary
				clearTimeout(timeout);
				var $this = $(this);
				$('.short', $this).hide();
				$('.detailed', $this).show();
			});
	}

	// Splitter
	var $document = $(document);
	var $left = $('#left');
	var $right = $('#right');
	var $splitter = $('#splitter');
	var splitterWidth = $splitter.width();
	$splitter.mousedown(function() {
			$splitter.addClass('active');

			$document.mousemove(function(event) {
				if (event.pageX >= 230 && $document.width() - event.pageX >= 600 + splitterWidth) {
					$left.width(event.pageX);
					$right.css('margin-left', event.pageX + splitterWidth);
					$splitter.css('left', event.pageX);
				}
			});

			$()
				.add($splitter)
				.add($document)
					.mouseup(function() {
						$splitter
							.removeClass('active')
							.unbind('mouseup');
						$document
							.unbind('mousemove')
							.unbind('mouseup');

						$.cookie('splitter', parseInt($splitter.css('left')), {expires: 365});
					});

			return false;
		});
	var splitterPosition = $.cookie('splitter');
	if (null !== splitterPosition) {
		splitterPosition = parseInt(splitterPosition);
		$left.width(splitterPosition);
		$right.css('margin-left', splitterPosition + splitterWidth + 'px');
		$splitter.css('left', splitterPosition + 'px');
	}
});
