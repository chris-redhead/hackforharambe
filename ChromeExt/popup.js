

function getUrls() {                    
    chrome.runtime.sendMessage(
        { type: "getUrls" }, 
        urls => {           
            var urlList = $('#urlList');

            urls.forEach(url => {
                urlList.append('<li>' + url + '</li>')
            });
        });
}

getUrls();

