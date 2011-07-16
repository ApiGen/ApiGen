$(function() {

	/**
	 * Updates menu.
	 *
	 * @param string page
	 */
	function updateMenu(page)
	{
		if (page === window.top.page) {
			return;
		}

		if (0 === page.search(/^class|function|constant/)) {
			window.top.frames['left'].$('#elements a[href^="' + page + '"]').click();
		} else if (0 === page.indexOf('source')) {
			window.top.frames['left'].$('#elements a[href^="' + page.substr(7) + '"]').click();
		} else if (0 === page.search(/^package|namespace/)) {
			window.top.frames['left'].$('#groups a[href^="' + page + '"]').click();
		} else if (0 === page.search(/^overview|tree|deprecated|todo/)) {
			window.top.frames['left'].$('#menu > a').click();
		}

		window.top.page = page;
	}

	var $frameset = $('frameset', window.top.document);

	// Menu size
	if (window.self === window.top) {
		window.page = 'overview.html';

		if (null !== $.cookie('splitter')) {
			$frameset.attr('cols', $.cookie('splitter') + ',*');
		}
	}

	// Menu
	if (window.self === window.top.frames['left']) {
		var $menu = $('#menu');
		var $groups = $('#groups', $menu);
		var $elements = $('#elements', $menu);

		var namespacesHidden = false;

		// Collapse deep packages and namespaces
		$('span', $groups).click(function() {
			$(this)
				.toggleClass('collapsed')
				.next('ul')
					.toggleClass('collapsed');
		});

		// Reset menu
		$('> a', $menu).click(function() {
			var $this = $(this);

			$(this).blur();

			$('li.active', $menu).removeClass('active');
			$('ul', $elements).show();
			$('li', $elements).show();
			$('a span', $elements).show();
			$('hr', $menu).show();

			namespacesHidden = false;

			// Collapse deep packages and namespaces
			$('span:not(.collapsed)', $groups).click();

			var $main = $('> ul > li.main', $groups);
			if ($main.length > 0) {
				// Open first level of the main project
				$('> span', $main).click();
			} else {
				// Open first level of all
				$('> ul > li > span', $groups).click();
			}

			window.top.page = $this.attr('href');
		}).click();
		// Mark active
		// Show only elements in package/namespace
		$('a', $groups).click(function() {
			var $this = $(this);

			// Collapse deep packages and namespaces
			$('span:not(.collapsed)', $groups).click();

			// Unmark active
			$('li.active', $menu).removeClass('active');

			// Mark active
			$this
				.blur()
				.parentsUntil('#groups', 'li')
					.addClass('active')
					.children('span')
						.click();

			// Shows only elements in package/namespace
			var elementsListsHidden = 0;
			var $elementsLists = $('ul', $elements);
			$elementsLists.each(function() {
				var $innerThis = $(this);

				var $all = $('li', $innerThis);
				$all.hide()
				var $visible = $('a[rel="' + $this.attr('rel') + '"]', $all);
				$visible
					.parent()
						.show();

				var visible = 0 !== $visible.length;
				$innerThis
					.toggle(visible)
					.prev()
						.toggle(visible);
				if (!visible) {
					elementsListsHidden++;
				}
			});
			$elements
				.prev()
					.toggle($elementsLists.length !== elementsListsHidden);

			// Hide namespaces in elements names
			if (!namespacesHidden) {
				$('span', $elements).hide();
				namespacesHidden = true;
			}

			window.top.page = $this.attr('href');
		});
		$('a', $elements).click(function() {
			var $this = $(this);

			// Mark active package/namespace
			$('a[rel="' + $this.attr('rel') + '"]', $groups).click();

			// Mark active
			$this
				.blur()
				.parent()
					.addClass('active');

			window.top.page = $this.attr('href');
		});
	}

	// Content
	if (window.self === window.top.frames['right']) {
		// Move back/next in browser history
		$(function() {
			window.top.document.title = window.document.title;
			updateMenu(window.location.pathname.split('/').pop());
		});

		var $wrapper = $('#wrapper');
		var $content = $('#content', $wrapper);

		// Update menu
		$('a', $wrapper).click(function() {
			updateMenu($(this).attr('href'));
		});

		// Search autocompletion
		var autocompleteFound = false;
		var $search = $('#search input[name=q]', $wrapper);
		$search
			.autocomplete(window.top.elements, {
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
				var page = data[0] + '-' + data[1].replace(/[^\w]/g, '.') + '.html';
				updateMenu(page);
				var location = window.location.href.split('/');
				location.pop();
				location.push(page);
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
		var $caption = $('table.summary', $content).filter(':has(tr[data-order])').find('caption');
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
			}).addClass('switchable')
			.attr('title', 'Switch between natural and alphabetical order');
		if ('true' === $.cookie('sorted')) {
			$caption.click();
		}

		// Delayed hover efect on summary
		var timeout;
		$('tr', $content).filter(':has(.detailed)')
			.hover(function() {
				clearTimeout(timeout);
				var $this = $(this);
				timeout = setTimeout(function() {
					$this.find('.short').hide();
					$this.find('.detailed').show();
				}, 500);
			}, function() {
				clearTimeout(timeout);
			}).click(function() { // Immediate hover effect on summary
				clearTimeout(timeout);
				var $this = $(this);
				$this.find('.short').hide();
				$this.find('.detailed').show();
			});


		// Splitter
		var $documentLeft = $(window.top.frames['left'].document);
		var $documentRight = $(window.top.frames['right'].document);
		var $documents = $()
			.add($documentLeft)
			.add($documentRight);
		var $splitter = $('#splitter');

		$splitter
			.attr('unselectable', 'on')
			.css({
				'user-select': 'none',
				'-moz-user-select': 'none',
				'-ms-user-select': 'none',
				'-webkit-user-select': 'none',
				'-khtml-user-select': 'none'
			}).mousedown(function() {
				$splitter.addClass('active');

				$documentLeft.mousemove(function(event) {
					if (event.pageX >= 230) {
						$frameset.attr('cols', event.pageX + ',*');
					}
				});
				$documentRight.mousemove(function(event) {
					if ($documentRight.width() >= 600) {
						$frameset.attr('cols', parseInt($frameset.attr('cols')) + event.pageX + ',*');
					}
				});

				$('body', $documents).css('-webkit-user-select', 'none');

				$()
					.add($splitter)
					.add($documents)
						.mouseup(function() {
							$splitter
								.removeClass('active')
								.unbind('mouseup');
							$documents
								.unbind('mousemove')
								.unbind('mouseup')
								.find('body')
									.css('-webkit-user-select', 'text');

							$.cookie('splitter', parseInt($frameset.attr('cols')), {expires: 365});
						});
			});
	}
});
