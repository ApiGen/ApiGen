$(function() {

	// Search autocompletion
	var autocompleteFound = false;
	var $search = $('#search input[name=q]');
	$search.autocomplete(elements, {
		matchContains: true,
		scrollHeight: 200,
		max: 20,
		formatItem: function(data) {return data[1].replace(/^(.+\\)(.+)$/, '<small>$1</small>$2');},
		formatMatch: function(data) {return data[1];},
		formatResult: function(data) {return data[1];}
	}).result(function(event, data) {
		autocompleteFound = true;
		var location = window.location.href.split('/');
		location.pop();
		location.push(data[0] + '-' + data[1].replace(/[^\w]/g, '.') + '.html');
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

	// Saves original order
	$('table.summary tr[data-order]').each(function(index) {
		do {
			index = '0' + index;
		} while (index.length < 3);
		$(this).attr('data-orig-order', index);
	});

	// Switches between natural and alphabetical order
	var $caption = $('table.summary:has(tr[data-order]) caption');
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
	$('tr:has(.detailed)').hover(function() {
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

	// Hide deep packages and namespaces
	$('#entities ul span').click(function() {
		$(this)
			.toggleClass('collapsed')
			.next('ul')
			.toggleClass('collapsed');
	}).click();

	$active = $('#entities ul li.active');
	if ($active.length > 0) {
		// Open active
		$('> span', $active).click();
	} else {
		$main = $('#entities > ul > li.main');
		if ($main.length > 0) {
			// Open first level of the main project
			$('> span', $main).click();
		} else {
			// Open first level of all
			$('#entities > ul > li > span').click();
		}
	}

	// Splitter
	$('#rightWrapper').css('marginLeft', 0);
	$('#main').splitter({
		sizeLeft: true,
		minLeft: 230,
		minRight: 600,
		anchorToWindow: true,
		cookie: 'splitter'
	});

	if (window.location.hash && $('#right ' + window.location.hash).size()) {
		$('#rightWrapper').scrollTop($('#rightWrapper ' + window.location.hash).offset().top);
	}
});
