
var urlList = $('#urlList');
var decodedImage = $('#decoded');


function getUrls(cb) {                    
    chrome.runtime.sendMessage(
        { type: "getUrls" }, 
        urls => {            
            urlList.empty();

            urls.forEach(url => {
                urlList.append('<li>' + url + '</li>')
            });

            cb(urls);
        });
}

function refresh() {
    getUrls(urls => {
        if(urls.length > 0) {
            decodedImage.attr('src', 'http://192.168.43.196/jase/decrypt.php?images=' + urls.join(';'));
        }
        else {
            decoded.attr('src', 'blank.png');
        }
    });
}


refresh();


$('#refresh').on('click', () => {
    refresh();
});

$('#clear').on('click', () => {
    chrome.runtime.sendMessage(
        { type: 'clearUrls' },
        () => refresh());
});


