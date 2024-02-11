function checkCookieExpiration() {
    // Get the value of the cookie "token"
    var cookieValue = document.cookie.replace(/(?:(?:^|.*;\s*)token\s*=\s*([^;]*).*$)|^.*$/, "$1");
    console.log("Validate JWT cookie life");

    if (cookieValue) {
        // Parse the cookie value to extract the expiration timestamp
        var expirationTimestamp = parseInt(cookieValue);

        // Get the current timestamp
        var currentTimestamp = Math.floor(Date.now() / 1000);

        if (currentTimestamp > expirationTimestamp) {
            // Cookie has expired, take appropriate action (e.g., redirect)
            alert('JWT cookie has expired. Redirecting...');
            // You can use window.location.href to redirect to another page
            window.location.href = '/login/login.php';
        } else {
            // Cookie is still valid
            console.log('JWT cookie is still valid.');
        }
    } else {
        // Cookie does not exist
        window.location.href = '/login/login.php';
        console.log('JWT cookie does not exist.');
    }
}

function deleteExpiredCookies2() {
    var cookies = document.cookie.split(';');

    for (var i = 0; i < cookies.length; i++) {
        var cookie = cookies[i];
        var eqPos = cookie.indexOf('=');
        var name = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;

        // Remove leading spaces
        name = name.replace(/^\s+/g, '');

        // Check if the cookie is expired (or doesn't have an expiration)
        if (document.cookie.indexOf(name + '=') == -1) {
            continue;
        }

        // Get the cookie value and expiration date
        var cookieParts = cookie.split('=');
        var cookieName = cookieParts[0].trim();
        var cookieValue = cookieParts[1];

        // Check if the cookie has an expiration date
        if (cookieName && cookieValue) {
            var expirationTimestamp = parseInt(cookieValue);
            var currentTimestamp = Math.floor(Date.now() / 1000);

            if (currentTimestamp > expirationTimestamp) {
                // Cookie has expired; delete it
                document.cookie = cookieName + '=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
            }
        }
    }
}


function deleteExpiredCookies3() {
    var cookies = document.cookie.split(';');
    console.log('Inside function');
    for (var i = 0; i < cookies.length; i++) {
        var cookie = cookies[i];
        var eqPos = cookie.indexOf('=');
        var name = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;

        // Remove leading spaces
        name = name.replace(/^\s+/g, '');
        console.log("Inside loop", name);
        // Check if the cookie is expired (or doesn't have an expiration)
        if (document.cookie.indexOf(name + '=') == -1) {
            continue;
        }

        // Get the cookie value and expiration date
        var cookieParts = cookie.split('=');
        var cookieName = cookieParts[0].trim();
        var cookieValue = cookieParts[1];

        // Check if the cookie has an expiration date
        if (cookieName && cookieValue) {
            var expirationTimestamp = parseInt(cookieValue);
            var currentTimestamp = Math.floor(Date.now() / 1000);

            if (currentTimestamp > expirationTimestamp) {
                // Debug: Log information about the cookie being deleted
                console.log('Deleting expired cookie:', cookieName);

                // Cookie has expired; delete it
                document.cookie = cookieName + '=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
            } else {
                // Debug: Log information about the cookie being valid
                console.log('Valid cookie:', cookieName);
            }
        }
    }
}


function deleteExpiredCookies() {
    var cookies = document.cookie.split(';');

    for (var i = 0; i < cookies.length; i++) {
        var cookie = cookies[i];
        var eqPos = cookie.indexOf('=');
        var name = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;

        // Remove leading spaces
        name = name.replace(/^\s+/g, '');

        // Check if the cookie is expired (or doesn't have an expiration)
        if (document.cookie.indexOf(name + '=') == -1) {
            continue;
        }

        var cookieValue = getCookie(name);

        // Check if the cookie has an expiration date
        if (cookieValue) {
            var expirationTimestamp = parseInt(cookieValue);
            var currentTimestamp = Math.floor(Date.now() / 1000);

            if (currentTimestamp > expirationTimestamp) {
                // Cookie has expired; delete it
                document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
            }
        }
    }
}

function getCookie(name) {
    var cookies = document.cookie.split(';');

    for (var i = 0; i < cookies.length; i++) {
        var cookie = cookies[i];
        var eqPos = cookie.indexOf('=');
        var cookieName = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;

        // Remove leading spaces
        cookieName = cookieName.replace(/^\s+/g, '');

        if (cookieName == name) {
            return cookie.substr(eqPos + 1);
        }
    }
    return null;
}
