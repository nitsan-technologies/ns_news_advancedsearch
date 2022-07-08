var form = document.getElementById("advancedsearch");

if (form) {
	form.addEventListener("submit", function (event) {
	  event.preventDefault();
	  sendData();
	});
}


function sendData() {
	$.ajax({
      method: 'post',
      dataType: 'text',
      url: form.getAttribute('action'),
      data:  $('form').serialize(),
      success: function success(data) {
	      var dom = document.createElement('div');
		    dom.innerHTML = data;
		    let app = dom.querySelector('.advancedsearch-result');
		    let docs = document.querySelector('.advancedsearch-result');
		    docs.innerHTML = "";
		    docs.appendChild(app);
			    $(".paginate").click(function(e){
						e.preventDefault();
						var $this = $(this);
						var pagenum = $(this).attr('name');
						var pageno = document.createElement("input");
						pageno.setAttribute("type", "hidden");
						pageno.setAttribute("name", "tx_news_pi1[currentPage]");
						pageno.setAttribute("value",pagenum);
						var form = document.getElementById("advancedsearch");
						form.append(pageno);
						sendData();
					});
      }
    });
}

