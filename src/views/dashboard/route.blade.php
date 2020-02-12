let route = (routeUrl, param) => {
    let append = [];

    for (let x in param) {
        let search = '{' + x + '}';
        let searchQ = '{' + x + '?}';

        if (routeUrl.indexOf(search) >= 0) {
            routeUrl = routeUrl.replace('{' + x + '}', param[x]);
        }else if(routeUrl.indexOf(searchQ) >= 0){
            routeUrl = routeUrl.replace('{' + x + '?}', param[x]);
        } else {
            append.push(x + '=' + param[x]);
        }
    }

    let url = '/' + routeUrl.trimStart('/');

    if (append.length == 0) {
        return url;
    }

    if (url.indexOf('?') >= 0) {
        url += '&';
    } else {
        url += '?';
    }

    url += append.join('&');

    return url;
}
var routes = {!! $route !!};