$(function(){

	$("#search input[type=text]").autocomplete(classes, {
		matchContains: true,
		scrollHeight: 200,
		max: 20,
		formatItem: function(row) { return row[0].replace(/^(.+\\)(.+)$/, '<small>$1</small>$2'); },
		formatMatch: function(row) { return row[0]; }
	});

	$("table.summary:has(tr[data-order]) tr").each(function(index) {
		do { index = '0' + index; } while (index.length < 3);
		$(this).attr('data-orig-order', index);
	});

	$("table.summary:has(tr[data-order]) caption").click(function() {
		this.sorted = !this.sorted;
		var attr = this.sorted ? 'data-order' : 'data-orig-order';
		$(this).closest("table").find('tr').sortElements(function(a, b) {
			return $(a).attr(attr) > $(b).attr(attr) ? 1 : -1;
		});
		return false;
	}).addClass('switchable').attr('title', 'Switch between natural and alphabetical order');

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
	});
});
