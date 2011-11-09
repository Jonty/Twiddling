javascript:(
function(){
    if (!document.location.host.match(/twitter\.com/)) {
        alert('You don\'t seem to be on the twitter website!');
        return;
    }

    var username = document.location.href.match(/twitter\.com\/(?:#!\/)?(.*?)(\/|\?|$)/);
    if (!username || !username[1]) {
        alert('Oh dear, failed to work out what this twitter user is called!');
    }
    
    location.href = 'http://jonty.co.uk/bits/twintersect?userA=<?=htmlentities($userA)?>&userB=' + username[1];
})();
