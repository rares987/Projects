Share = {
    twitter: function(purl, ptitle) {
    url = 'http://twitter.com/share?';
    url += 'text=' + encodeURIComponent(ptitle);
    url += '&url=' + encodeURIComponent(purl);
    url += '&counturl=' + encodeURIComponent(purl);
    Share.popup(url);
    }
    };