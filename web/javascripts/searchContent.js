window.onload = function() {

    // Setup search
    var content = [
        { title: 'Andorra' },
        { title: 'United Arab Emirates' },
        { title: 'Afghanistan' },
        { title: 'Antigua' },
        { title: 'Anguilla' },
        { title: 'Albania' },
        { title: 'Armenia' }
    ];

    $('.ui.search').search({
            type: 'standard',
            source: content,
            searchFields : ['title'],
        });

/*        $('.ui.search').search({
          fulltextSearch: true,
          source: content,
          onChange: function() {
            if($(this).val() != "") {
              window.location = 'http://localhost:8080/patents/' + $(this).val();
            }
          }
        });


        $('.ui.search')
          .search({
            apiSettings: {
              url: '//api.github.com/search/repositories?q={query}'
            },
            fields: {
              results : 'items',
              title   : 'name',
              url     : 'html_url'
            },
            minCharacters : 3
          })
        ;*/
}
