function onlyUnique(value, index, self) { 
    return self.indexOf(value) === index;
}

$(function(){
	if($("html").is(".highres")){
		var src = [];
		$("img").each(
			function(){
				src.push($(this).attr("src"));	
			}
		);	
		src = src.filter(onlyUnique);
		for(i in src){
			if (typeof src[i] == 'string' && src[i].indexOf('.') > 0) {
			img = new Image();
				img.onload = function(){
					var src1 = $(this).attr("src");
					if (src1.indexOf('.') > 0 ) {
						s = $(this).attr("src").split(".");
						s[s.length-2] = s[s.length-2].substring(0,s[s.length-2].length-3);
						s = s.join(".");
						$("img[src='"+s+"']").attr("src",$(this).attr("src"));
					}
				}
			
			
				
				s = src[i].split(".");
				s[s.length-2] = s[s.length-2]+"@2x";
				s = s.join(".");
				img.src = s;
			}
		}
	}	
});