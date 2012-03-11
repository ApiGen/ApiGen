/*!
 * ApiGen 2.5.0 - API documentation generator for PHP 5.3+
 *
 * Copyright (c) 2010-2011 David Grudl (http://davidgrudl.com)
 * Copyright (c) 2011-2012 Jaroslav Hanslík (https://github.com/kukulich)
 * Copyright (c) 2011-2012 Ondřej Nešpor (https://github.com/Andrewsville)
 *
 * For the full copyright and license information, please view
 * the file LICENSE.md that was distributed with this source code.
 */

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
	var autocompleteFiles = {'c': 'class', 'co': 'constant', 'f': 'function', 'm': 'class', 'p': 'class', 'cc': 'class'};
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
			var parts = data[1].split(/::|$/);
			var file = $.sprintf(ApiGen.config.templates.main[autocompleteFiles[data[0]]].filename, parts[0].replace('\\', '.').replace(/[^\w\.]/g, ''));
			if (parts[1]) {
				file += '#' + parts[1].replace(/([\w]+)\(\)/, '_$1');
			}
			location.push(file);
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
	if ((null === $.cookie('order') && 'alphabetical' === ApiGen.config.options.elementsOrder) || 'alphabetical' === $.cookie('order')) {
		$caption.click();
	}

	// Delayed hover efect on summary
	if (ApiGen.config.options.elementDetailsCollapsed) {
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

	// Splitter
	var $document = $(document);
	var $left = $('#left');
	var $right = $('#right');
	var $rightInner = $('#rightInner');
	var $splitter = $('#splitter');
	var splitterWidth = $splitter.width();
	function setSplitterPosition(position)
	{
		$left.width(position);
		$right.css('margin-left', position + splitterWidth);
		$splitter.css('left', position);
	}
	function setNavigationPosition()
	{
		var width = $rightInner.width();
		$rightInner
			.toggleClass('medium', width <= 960)
			.toggleClass('small', width <= 650);
	}
	$splitter.mousedown(function() {
			$splitter.addClass('active');

			$document.mousemove(function(event) {
				if (event.pageX >= 230 && $document.width() - event.pageX >= 600 + splitterWidth) {
					setSplitterPosition(event.pageX);
					setNavigationPosition();
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
		setSplitterPosition(parseInt(splitterPosition));
	}
	setNavigationPosition();
	$(window).resize(setNavigationPosition);
});
