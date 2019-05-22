	
    <script src="/banners/countdown.js"></script>
    <link href="/banners/countdown.css" rel="stylesheet" type="text/css" />
    <script>
      $(function(){
        $(".digits").countdown({
          image: "/banners/digits.png",
          format: "dd:mm:ss",
          startTime: "12:25:14"
        });
      });
    </script>
   
	<div class="banner">
		<div class="wrapper">
		  <div class="cell">
			<div id="holder">
			  <div class="digits"></div>
			</div>
		  </div>
		</div>
	</div>