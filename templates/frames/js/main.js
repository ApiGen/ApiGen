$(function() {

	/**
	 * Updates menu.
	 *
	 * @param string file
	 */
	function updateMenu(file)
	{
		if (file.match(/class|function|constant/)) {
			window.top.frames['left'].$('#elements a[href^="' + file + '"]').click();
		} else if (file.match(/package|namespace/)) {
			window.top.frames['left'].$('#groups a[href^="' + file + '"]').click();
		} else if (file.match('overview|tree|deprecated|todo')) {
			window.top.frames['left'].$('#menu > a').click();
		} else if (file.match('source')) {
			// Nothing
		}
	}

	var $frameset = $('frameset', window.top.document);

	// Menu size
	if (window.self === window.top) {
		if (null !== $.cookie('splitter')) {
			$frameset.attr('cols', $.cookie('splitter') + ',*');
		}
	}

	// Menu
	if (window.self === window.top.frames['left']) {
		var $menu = $('#menu');
		var $groups = $('#groups', $menu);
		var $elements = $('#elements', $menu);

		// Collapse deep packages and namespaces
		$('span', $groups).click(function() {
			$(this)
				.toggleClass('collapsed')
				.next('ul')
				.toggleClass('collapsed');
		});

		// Reset menu
		$('> a', $menu).click(function() {
			$('li.active', $menu).removeClass('active');
			$('ul, li, a span', $elements).show();

			$('span:not(.collapsed)', $groups).click();

			var $main = $('> ul > li.main', $groups);
			if ($main.length > 0) {
				// Open first level of the main project
				$('> span', $main).click();
			} else {
				// Open first level of all
				$('> ul > li > span', $groups).click();
			}
		}).click();
		// Mark active
		// Show only elements in package/namespace
		$('a', $groups).click(function() {
			var $this = $(this);
			var groupName = $this.attr('rel');

			// Collapse deep packages and namespaces
			$('span:not(.collapsed)', $groups).click();

			// Unmark active
			$('li.active', $groups).removeClass('active');

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
				var $this = $(this);

				var $all = $('li', $this);
				$all.hide()
				var $visible = $('a[rel="' + groupName + '"]', $all);
				$visible
					.parent()
						.show();

				var visible = 0 !== $visible.length;
				$this
					.toggle(visible)
					.prev()
						.toggle(visible);
				if (!visible) {
					elementsListsHidden++;
				}
			});
			$elements.prev().toggle($elementsLists.length !== elementsListsHidden);

			// Hide namespaces in elements names
			$('span', $elements).hide();
		});
		$('a', $elements).click(function() {
			var $this = $(this);

			// Unmark active
			$('li.active', $elements)
				.removeClass('active');

			// Mark active
			$this
				.blur()
				.parent()
					.addClass('active');

			// Mark active package/namespace
			$('a[rel="' + $this.attr('rel') + '"]', $groups).click();
		});
	}

	// Content
	if (window.self === window.top.frames['right']) {
		var $wrapper = $('#wrapper');
		var $content = $('#content', $wrapper);

		// Update menu
		$('a', $wrapper).click(function() {
			updateMenu($(this).attr('href'));
		});

		// Search autocompletion
		var autocompleteFound = false;
		var $search = $('#search input[name=q]', $wrapper);
		$search.autocomplete(elements, {
			matchContains: true,
			scrollHeight: 200,
			max: 20,
			formatItem: function(data) {return data[1].replace(/^(.+\\)(.+)$/, '<small>$1</small>$2');},
			formatMatch: function(data) {return data[1];},
			formatResult: function(data) {return data[1];}
		}).result(function(event, data) {
			autocompleteFound = true;
			var file = data[0] + '-' + data[1].replace(/[^\w]/g, '.') + '.html';
			updateMenu(file);
			var location = window.location.href.split('/');
			location.pop();
			location.push(file);
			window.location = location.join('/');
		}).closest('form').submit(function() {
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
		var $caption = $('table.summary:has(tr[data-order]) caption', $content);
		$caption.click(function() {
			var $this = $(this);
			var sorted = !$this.data('sorted');
			$this.data('sorted', sorted);
			$.cookie('sorted', sorted, {expires: 365});
			var attr = sorted ? 'data-order' : 'data-orig-order';
			$this.closest("table").find('tr').sortElements(function(a, b) {
				return $(a).attr(attr) > $(b).attr(attr) ? 1 : -1;
			});
			return false;
		}).addClass('switchable').attr('title', 'Switch between natural and alphabetical order');
		if ('true' === $.cookie('sorted')) {
			$caption.click();
		}

		// Delayed hover efect on summary
		var timeout;
		$('tr:has(.detailed)', $content).hover(function() {
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
		var $splitter = $('#splitter');

		$splitter.css({
			'user-select': 'none',
			'-moz-user-select': 'none',
			'-webkit-user-select': 'none',
			'-khtml-user-select': 'none'
		}).mousedown(function() {
			$splitter.addClass('active');

			$documentLeft.mousemove(function(e) {
				if (e.pageX > 230) {
					$frameset.attr('cols', e.pageX + ',*');
				}
			});
			$documentRight.mousemove(function(e) {
				if ($documentRight.width() > 600) {
					$frameset.attr('cols', parseInt($frameset.attr('cols')) + e.pageX + ',*');
				}
			});

			$()
				.add($splitter)
				.add($documentLeft)
				.add($documentRight)
					.mouseup(function() {
						$splitter
							.removeClass('active')
							.unbind('mouseup');
						$documentLeft
							.unbind('mousemove')
							.unbind('mouseup');
						$documentRight
							.unbind('mousemove')
							.unbind('mouseup');

						$.cookie('splitter', parseInt($frameset.attr('cols')), {expires: 365, path: ''});
					});
		});
	}
});
