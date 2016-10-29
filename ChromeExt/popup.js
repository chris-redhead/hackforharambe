
var urls = [];


function getUrls() {                    
    chrome.runtime.sendMessage(
        { type: "getUrls" }, 
        foundUrls => {           
            var urlList = $('#urlList');

            urls = foundUrls;

            urls.forEach(url => {
                urlList.append('<li>' + url + '</li>')
            });
        });
}

getUrls();


$('#submitImages').on('click', (ev) => {

    var getParams = urls.map((url, i) => 'pic' + i + '=' + encodeURIComponent(url))
                        .join('&');

    $.ajax({
        url: 'http://192.168.43.196/jase/decrypt.php?' + getParams,
        success: res => {
                    alert('HELLO!' + JSON.stringify(res));
                },
        error: (err) => alert(JSON.stringify(err))
    });

});


