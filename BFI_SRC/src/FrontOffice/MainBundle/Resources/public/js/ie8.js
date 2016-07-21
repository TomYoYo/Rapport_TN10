if(navigator.appName.indexOf("Internet Explorer")!=-1){
    var badBrowser=(
        navigator.appVersion.indexOf("MSIE 9")==-1 &&
        navigator.appVersion.indexOf("MSIE 1")==-1
    );

    if(badBrowser && window.location.pathname != "/old-browser"){
        document.location.href="/old-browser";
    }
}