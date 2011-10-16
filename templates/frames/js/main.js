$(function() {
	// Frames detection
	var isTopFrame = 0 !== $('iframe').length;
	var isLeftFrame = 0 !== $('#menu').length;
	var isRightFrame = 0 !== $('#rightInner').length;

	if (isTopFrame) {
		// Top

		if ('' === window.location.hash.substr(1)) {
			window.location.hash = 'overview.html';
		}

		/**
		 * Loads new page.
		 *
		 * @param string href
		 */
		window.ApiGen.loadPage = function(href) {
			// Change content
			var page = href.split('#')[0];
			var location = window.frames['right'].location.href.split('/');
			location.pop();
			location.push(href);
			window.frames['right'].location.replace(location.join('/'));

			// Change menu
			if (0 === page.search(/^overview|tree|deprecated|todo/)) {
				window.frames['left'].location.reload();
			} else {
				if (0 === page.indexOf('source-')) {
					page = page.substr(7);
				}

				var $menu = $('#menu', window.frames['left'].document);
				var $groups = $('#groups', $menu);
				var $elements = $('#elements', $menu);

				// Collapse deep packages and namespaces
				$('span:not(.collapsed)', $groups).click();

				// Unmark active
				$('li.active', $menu).removeClass('active');

				var $group;
				if (0 === page.search(/^package|namespace/)) {
					// Select group
					$group = $('a[href^="' + page + '"]', $groups);
				} else {
					// Select element
					var $element = $('a[href^="' + page + '"]', $elements);
					$group = $('a[rel="' + $element.attr('rel') + '"]', $groups);

					// Mark active element
					$element
						.blur()
						.parent()
							.addClass('active');
				}

				// Mark active group
				$group
					.blur()
					.parentsUntil('#groups', 'li')
						.addClass('active')
						.children('span')
							.click();

				// Shows only elements in active group
				var elementsListsHidden = 0;
				var $elementsLists = $('ul', $elements);
				$elementsLists.each(function() {
					var $this = $(this);

					// Css is a little quicker than show/hide/toggle

					var $all = $('li', $this);
					$all.css('display', 'none');
					var $visible = $('a[rel="' + $group.attr('rel') + '"]', $all);
					$visible
						.parent()
							.css('display', 'list-item');

					if (0 !== $visible.length) {
						$this
							.css('display', 'block')
							.prev()
								.css('display', 'block');
					} else {
						$this
							.css('display', 'none')
							.prev()
								.css('display', 'none');

						elementsListsHidden++;
					}
				});
				$('hr', $menu).toggle($elementsLists.length !== elementsListsHidden);

				// Hide namespaces in elements names
				if (!window.frames['left'].namespacesHidden) {
					$('span', $elements).css('display', 'none');
					window.frames['left'].namespacesHidden = true;
				}
			}
		};

		// Back/Forward button
		window.setInterval(function() {
			var page = window.location.hash.substr(1);
			if (page !== window.frames['right'].location.pathname.split('/').pop()) {
				window.ApiGen.loadPage(page);
			}
		}, 100);

		// Splitter
		var $left = $('#left');
		var $right = $('#rightWrapper');
		var $splitter = $('#splitter');
		var splitterWidth = $splitter.width();
		$splitter.mousedown(function() {
				$splitter.addClass('active');

				var $document = $(window.document);
				var $documentLeft = $(window.frames['left'].document);
				var $documentRight = $(window.frames['right'].document);
				var $documents = $()
					.add($document)
					.add($documentLeft)
					.add($documentRight);

				// For Opera
				$document.mousemove(function(event) {
					if (event.pageX >= 230 && $document.width() - event.pageX >= 600 + splitterWidth) {
						$left.width(event.pageX);
						$right.css('margin-left', event.pageX + splitterWidth);
						$splitter.css('left', event.pageX);
					}
				});
				// For other browsers
				$documentLeft.mousemove(function(event) {
					if (event.pageX >= 230) {
						$left.width(event.pageX);
						$right.css('margin-left', event.pageX + splitterWidth);
						$splitter.css('left', event.pageX);
					}
				});
				$documentRight.mousemove(function(event) {
					if ($right.width() >= 600 + splitterWidth) {
						var splitterPosition = parseInt($splitter.css('left')) + splitterWidth + event.pageX;
						$left.width(splitterPosition);
						$right.css('margin-left', splitterPosition + splitterWidth);
						$splitter.css('left', splitterPosition);
					}
				});

				$()
					.add($splitter)
					.add($documents)
						.mouseup(function() {
							$splitter
								.removeClass('active')
								.unbind('mouseup');
							$documents
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
			$right.css('margin-left', splitterPosition + splitterWidth);
			$splitter.css('left', splitterPosition);
		}
	} else if (isLeftFrame) {
		// Menu

		// Check parent frame
		if (window.self !== window.parent.frames['left']) {
			var leftLocation = window.location.href.split('/');
			leftLocation.pop();
			leftLocation.push('index.html');
			window.location.replace(location.join('/'));
			return;
		}

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
		}).click();

		var $main = $('> ul > li.main', $groups);
		if ($main.length > 0) {
			// Open first level of the main project
			$('> span', $main).click();
		} else {
			// Open first level of all
			$('> ul > li > span', $groups).click();
		}

		// Links
		$('a', $menu).click(function() {
			var page = $(this).attr('href');
			window.parent.ApiGen.loadPage(page);
			window.parent.location.hash = page;
			return false;
		});
	} else if (isRightFrame) {
		// Content

		var actualPage = window.location.pathname.split('/').pop();

		// Check parent frame
		if (window.self !== window.parent.frames['right']) {
			var rightLocation = window.location.href.split('/');
			rightLocation.pop();
			rightLocation.push('index.html#' + actualPage);
			window.location.replace(rightLocation.join('/'));
			return;
		}

		// Update title
		window.parent.document.title = window.document.title;

		var $content = $('#content');

		// Links
		$('a:not([href*="://"]):not([href^="#"])').click(function() {
			var href = $(this).attr('href');
			window.parent.ApiGen.loadPage(href);
			window.parent.location.hash = href.split('#')[0];
			return false;
		});

		// Open external links to top window
		$('a[href*="://"]').attr('target', '_parent');

		// Search autocompletion
		var autocompleteFound = false;
		var $search = $('#search input[name=q]');
		$search
			.autocomplete(window.parent.ApiGen.elements, {
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
				window.parent.ApiGen.loadPage(page);
				window.parent.location.hash = page;
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

		// Save natural order
		$('table.summary tr[data-order]', $content).each(function(index) {
			do {
				index = '0' + index;
			} while (index.length < 3);
			$(this).attr('data-order-natural', index);
		});

		// Switch between natural and alphabetical order
		var $caption = $('table.summary', $content)
			.filter(':has(tr[data-order])')
				.find('caption');
		$caption
			.click(function() {
				var $this = $(this);
				var order = $this.data('order') || 'natural';
				order = 'natural' === order ? 'alphabetical' : 'natural';
				$this.data('order', order);
				$.cookie('order', order, {expires: 365});
				var attr = 'alphabetical' === order ? 'data-order' : 'data-order-natural';
				$this
					.closest('table')
						.find('tr').sortElements(function(a, b) {
							return $(a).attr(attr) > $(b).attr(attr) ? 1 : -1;
						});
				return false;
			})
			.addClass('switchable')
			.attr('title', 'Switch between natural and alphabetical order');
		if ((null === $.cookie('order') && 'alphabetical' === window.parent.ApiGen.options.elementsOrder) || 'alphabetical' === $.cookie('order')) {
			$caption.click();
		}

		// Delayed hover efect on summary
		if (window.parent.ApiGen.options.elementDetailsCollapsed) {
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
				}).click(function() {
					// Immediate hover effect on summary
					clearTimeout(timeout);
					var $this = $(this);
					$('.short', $this).hide();
					$('.detailed', $this).show();
				});
		}
	}
});
