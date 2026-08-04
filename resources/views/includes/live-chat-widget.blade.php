{{-- 
    LIVE CHAT WIDGET
    
    To change the live chat script:
    1. Open this file: resources/views/includes/live-chat-widget.blade.php
    2. Replace the script below with your new live chat code
    3. Upload this file to your server
    4. Clear cache at: /admin/dashboard/clearcache
    
    Supported widgets: Smartsupp, Tawk.to, Crisp, Intercom, LiveChat, etc.
--}}

<!-- Smartsupp Live Chat script -->
<script type="text/javascript">
var _smartsupp = _smartsupp || {};
_smartsupp.key = '981552f3acb735de4aff60e78c1154b36c17fa30';
window.smartsupp||(function(d) {
  var s,c,o=smartsupp=function(){ o._.push(arguments)};o._=[];
  s=d.getElementsByTagName('script')[0];c=d.createElement('script');
  c.type='text/javascript';c.charset='utf-8';c.async=true;
  c.src='https://www.smartsuppchat.com/loader.js?';s.parentNode.insertBefore(c,s);
})(document);
</script>
<noscript> Powered by <a href=“https://www.smartsupp.com” target=“_blank”>Smartsupp</a></noscript>


