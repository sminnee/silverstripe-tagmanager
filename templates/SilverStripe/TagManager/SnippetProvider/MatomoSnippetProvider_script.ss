<script>
  var settings = {"showIntro":true,"divId":"matomo-opt-out","cookiePath":"","cookieDomain":"","cookieSameSite":"Lax","OptOutComplete":"Opt-out complete","snip":"snip"};
  var _paq = window._paq = window._paq || [];

  _paq.push(['trackPageView']);
  _paq.push(['enableLinkTracking']);

  (function() {
    var u='//{$URL}/';

    <% if not $Cookies %>_paq.push(["disableCookies"]);<% end_if %>
    _paq.push(['setTrackerUrl', u+'matomo.php']);
    _paq.push(['setSiteId', '{$SiteID}']);

    var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
    g.async=true;
    g.src=u+'matomo.js';
    s.parentNode.insertBefore(g,s);
  })();
</script>