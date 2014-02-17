$('#add_to_cart').click(function(){
			console.log('ok');
			var id = $(this).id;
			console.log(id);
			var form = document.forms['myform3'];
			var startTime = form.elements['startTime'].value;
			var limit = form.elements['limit'].value;
			alert(startTime + limit);
				$.ajax({
					url: ".cart/add",
					method: "post",   
					data:{
						'startTime': startTime,
						'limit': limit,
					},
					success: function(data) {
						alert(data);
					},
					error: function(request) {
						alert("Connection error");
					}
				});
			});