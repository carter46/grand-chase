{{--
  Live chat: only one active provider (none | tidio | smartsupp | chatway).
  Configure in Admin → App Settings (Website information).
  Docs:
  - Tidio:    <script src="//code.tidio.co/PUBLIC_KEY.js" async></script>
  - Smartsupp: _smartsupp.key + loader.js (https://docs.smartsupp.com/chat-box/installation/)
  - Chatway:  <script id="chatway" async src="https://cdn.chatway.app/widget.js?id=WIDGET_ID"></script>
--}}
@php
    $provider = strtolower(trim((string) ($settings->livechat_provider ?? 'none')));
    $tidioKey = trim((string) ($settings->tido ?? ''));
    $smartsuppKey = trim((string) ($settings->smartsupp_key ?? ''));
    $chatwayId = trim((string) ($settings->chatway_widget_id ?? ''));

    // Normalize Tidio public key (dashboard may include .js)
    if ($tidioKey !== '' && substr(strtolower($tidioKey), -3) === '.js') {
        $tidioKey = substr($tidioKey, 0, -3);
    }
@endphp

@if ($provider === 'tidio' && $tidioKey !== '')
<script src="//code.tidio.co/{{ $tidioKey }}.js" async></script>
@elseif ($provider === 'smartsupp' && $smartsuppKey !== '')
<script type="text/javascript">
var _smartsupp = _smartsupp || {};
_smartsupp.key = @json($smartsuppKey);
window.smartsupp||(function(d) {
  var s,c,o=smartsupp=function(){ o._.push(arguments)};o._=[];
  s=d.getElementsByTagName('script')[0];c=d.createElement('script');
  c.type='text/javascript';c.charset='utf-8';c.async=true;
  c.src='https://www.smartsuppchat.com/loader.js?';s.parentNode.insertBefore(c,s);
})(document);
</script>
@elseif ($provider === 'chatway' && $chatwayId !== '')
<script id="chatway" async="true" src="https://cdn.chatway.app/widget.js?id={{ $chatwayId }}"></script>
@endif
