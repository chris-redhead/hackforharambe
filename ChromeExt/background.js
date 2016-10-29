
var urls = [];


// window.alert('setting listener...');


chrome.runtime.onMessage.addListener(
  function(req, _, respond) {
    switch(req.type) {
        case "getUrls":
            respond(urls);
            return true;

        // case "postUrl":
        //     urls.push(req.data);
        //     respond(true);            
        //     return true;
    }
 });


chrome.contextMenus.create({
    id: 'addImageUrl',
    title: "Add image to decryption set",
    contexts: ["image"]
});

chrome.contextMenus.onClicked.addListener(function(info, tab) {
    switch(info.menuItemId) {
        case "addImageUrl":
            urls.push(info.srcUrl);
            return;
    }
});
