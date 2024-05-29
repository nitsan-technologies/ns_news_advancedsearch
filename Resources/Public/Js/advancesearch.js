function resetFunc(){
    const url = window.location.href.split('?')[0];
    window.location.href = url;
}
var form = document.getElementById("advancedsearch");

if (form) {
    form.addEventListener("submit", function (event) {
        event.preventDefault();
        sendData();
    });
}


function sendData() {
    const XHR = new XMLHttpRequest();
    const FD = new FormData(form);
    XHR.addEventListener("load", function (event) {
        var dom = document.createElement('div');
        dom.innerHTML = event.target.responseText;
        let app = dom.querySelector('.news-search-result');
        let docs = document.querySelector('.news-search-result');
        docs.innerHTML = "";
        docs.appendChild(app);
        let paginate = document.querySelectorAll('.paginate');
        paginate.forEach(($ele) => {
            $ele.addEventListener('click', (e) => {
                e.preventDefault();
                var pagenum = $ele.getAttribute('name');
                var pageno = document.createElement("input");
                pageno.setAttribute("type", "hidden");
                pageno.setAttribute("name", "tx_news_pi1[currentPage]");
                pageno.setAttribute("value",pagenum);
                var form = document.getElementById("advancedsearch");
                form.append(pageno);
                sendData();
            });
        });
    });
    XHR.open("POST", form.getAttribute('action'));
    XHR.send(FD);
}