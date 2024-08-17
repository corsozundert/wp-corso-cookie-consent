// script.js

// Function to get a cookie
function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
}

// Function to set a cookie
function setCookie(name, value, days) {
    const date = new Date();
    date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
    const expires = `expires=${date.toUTCString()}`;
    document.cookie = `${name}=${value}; ${expires}; path=/`;
}

// Function to show the cookie consent bar
function showConsentBar() {
    console.log('show consent bar');
    const consentBar = document.getElementById('corso-cookie-consent-bar');
    consentBar.style.display = 'block';
}

// Function to hide the cookie consent bar
function hideConsentBar() {
    const consentBar = document.getElementById('corso-cookie-consent-bar');
    consentBar.style.display = 'none';
}

function loadAdditionalScripts() {
    //  console.log(`Consent given. loading additional scripts`);

    // Load Google Tag Manager
    let gtm_id = document.getElementById('corso-cookie-consent-bar').getAttribute('data-gtm-id');


    if (gtm_id && gtm_id !== '') {
        loadGoogleTagManager(gtm_id);
    }

    // Load Google Analytics
    let ga_id = document.getElementById('corso-cookie-consent-bar').getAttribute('data-ga-id');

    if (ga_id && ga_id !== '') {
        loadGoogleAnalytics(ga_id);
    }

}


function loadGoogleAnalytics(ga_id) {


    console.log(`Loading Google Analytics`);
    (function (i, s, o, g, r, a, m) {
        i['GoogleAnalyticsObject'] = r; i[r] = i[r] || function () {
            (i[r].q = i[r].q || []).push(arguments)
        }, i[r].l = 1 * new Date(); a = s.createElement(o),
            m = s.getElementsByTagName(o)[0]; a.async = 1; a.src = g; m.parentNode.insertBefore(a, m)
    })(window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga');

    ga('create', ga_id, 'auto');
    ga('send', 'pageview');
}


function loadGoogleTagManager(gtm_id) {


    console.log(`Loading Google Tag Manager ${gtm_id}`);


    (function (w, d, s, l, i) {
        w[l] = w[l] || [];
        w[l].push({ 'gtm.start': new Date().getTime(), event: 'gtm.js' });
        var f = d.getElementsByTagName(s)[0],
            j = d.createElement(s),
            dl = l != 'dataLayer' ? '&l=' + l : '';
        j.async = true;
        j.src = 'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
        f.parentNode.insertBefore(j, f);
    })(window, document, 'script', 'dataLayer', gtm_id);

}

// Check for cookie consent
document.addEventListener('DOMContentLoaded', () => {
    if (!getCookie('corsoCookieConsent')) {
        showConsentBar();
    } else {
        loadAdditionalScripts();
    }

    // Add click event listener to the accept button
    document.getElementById('corso-cookie-consent-accept-cookies').addEventListener('click', () => {
        setCookie('corsoCookieConsent', 'true', 365);
        hideConsentBar();
        loadAdditionalScripts();
    });
});
