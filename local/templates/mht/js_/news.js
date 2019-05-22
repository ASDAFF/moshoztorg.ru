$(function(){
	var wall = new freewall(".news_list");
	wall.reset({
		selector: '.new',
		gutterX: 30,
		gutterY: 30,
		cellW: 310,
		cellH: 'auto',
		onResize: function() {
			wall.fitWidth();
		}
	});	
	wall.fitWidth();
});