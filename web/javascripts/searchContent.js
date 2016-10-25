window.onload = function() {
    // Setup search
/*    var content = [
        { title: 'Andorra' },
        { title: 'United Arab Emirates' },
        { title: 'Afghanistan' },
        { title: 'Antigua' },
        { title: 'Anguilla' },
        { title: 'Albania' },
        { title: 'Armenia' }
    ]; */

    $('.ui.search')
        .search({
            type: 'standard',
            source: content,
            searchFields : ['title'],
        });
}


/*function getJSonObject(value) {
    return $.parseJSON(value.replace(/&quot;/ig, '"'));
}

var storeJSON = getJSonObject("{{patent|json_encode()}}");

$.each(storeJSON, function(k,v){
    alert('Key = ' + k + '\n' + 'Value = ' + v);
});
*/
