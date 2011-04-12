$(function(){

	// enables search autocompletion
	$("#search input[type=text]").autocomplete(classes, {
		matchContains: true,
		scrollHeight: 200,
		max: 20,
		formatItem: function(row) { return row[0].replace(/^(.+\\)(.+)$/, '<small>$1</small>$2'); },
		formatMatch: function(row) { return row[0]; }
	});

	// saves original order
	$("table.summary:has(tr[data-order]) tr").each(function(index) {
		do { index = '0' + index; } while (index.length < 3);
		$(this).attr('data-orig-order', index);
	});

	// switches between natural and alphabetical order
	$("table.summary:has(tr[data-order]) caption").click(function() {
		this.sorted = !this.sorted;
		expire = new Date();
		expire.setTime(expire.getTime()+(365*24*60*60*1000));
		document.cookie = 'methods-order=' + this.sorted + '; expire=' + expire.toUTCString();
		var attr = this.sorted ? 'data-order' : 'data-orig-order';
		$(this).closest("table").find('tr').sortElements(function(a, b) {
			return $(a).attr(attr) > $(b).attr(attr) ? 1 : -1;
		});
		return false;
	}).addClass('switchable').attr('title', 'Switch between natural and alphabetical order');

	if (document.cookie.indexOf('methods-order=true') > -1) {
		$("table.summary:has(tr[data-order]) caption").click();
	}

	// delayed hover efect on method summary
	var timeout;
	$("tr:has(.detailed)").hover(function(){
		clearTimeout(timeout);
		var $tr = $(this);
		timeout = setTimeout(function(){
			$tr.find('.short').hide();
			$tr.find('.detailed').show();
		}, 500);
	}, function(){
		clearTimeout(timeout);

	}).click(function(){ // immediate hover effect on method summary
		clearTimeout(timeout);
		$(this).find('.short').hide();
		$(this).find('.detailed').show();
	});

	// hide deep namespaces
	$('#left ul li:not(.active):not(:has(.active)) ul').addClass('collapsed');

	$('#left > ul > li').hover(function(){
		$('ul.collapsed', this).stop(true, true).delay(300).slideDown();
	}, function(){
		$('ul.collapsed', this).stop(true, true).slideUp('fast');
	});
});
