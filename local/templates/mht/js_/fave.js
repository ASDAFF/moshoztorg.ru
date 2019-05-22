$(function(){
	$('.fave_page .remove').click(function(e){
		e.preventDefault();
		var $product = $(this).closest('.product'),
			id = $(this).attr('data-id');

		$.post('/ajax.php', {
			action : 'fav-remove',
			id : id
		}, function(){
			$product.remove();
		});
	})
});