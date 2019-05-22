$(function(){

	var source = [],
		activeCategory = null;
	$.each(mht.brandCategories, function(i, category){
		if(category.active){
			activeCategory = category;
		}
		source.push({
			label : category.name,
			value : category.link,
		});
	});
	
	$("input[name='category']").attr({
		readonly : 'readonly'
	}).autocomplete({
		source : source,
		minLength: 0,
		position: {
			my: "left top",
			at: "left bottom",
			collision: "flip"
		},
		select : function(e, ui){
			location.href = ui.item.value;
			$("input[name='category']").val(ui.item.label);
			e.preventDefault();
		}
	}).val(activeCategory ? activeCategory.name : 'Выберите категорию товара');		

	$('#all_brands a.childed').click(function(e){
		e.preventDefault();
		$(this).siblings('.categories').slideToggle();
	})
});