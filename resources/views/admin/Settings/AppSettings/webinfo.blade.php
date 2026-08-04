<div class="row">
    <div class="col-12">
        <form method="post" action="{{ route('updatewebinfo') }}" id="appinfoform" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class=" form-row">
                <div class="form-group col-md-6">
                    <h5 class="text-{{ $text }}">Website Name</h5>
                    <input type="text" name="site_name" class="form-control " value="{{ $settings->site_name }}"
                        required>
                </div>
                <div class="form-group col-md-6">
                    <h5 class="text-{{ $text }}">Website Title</h5>
                    <input type="text" name="site_title" class="form-control " value="{{ $settings->site_title }}"
                        required>
                </div>
                <div class="form-group col-md-6">
                    <h5 class="text-{{ $text }}">Website Keywords</h5>
                    <input type="text" name="keywords" class="form-control " value="{{ $settings->keywords }}"
                        required>
                </div>
                <div class="form-group col-md-6">
                    <h5 class="text-{{ $text }}">Website Url (https://yoursite.com)</h5>
                    <input type="text" placeholder="https://yoursite.com" name="site_address" class="form-control "
                        value="{{ $settings->site_address }}" required>
                </div>
                <!--<div class="form-group col-md-12">-->
                <!--    <h5 class="text-{{ $text }}">Website Description</h5>-->
                <!--    <textarea name="description" class="form-control " rows="4">{{ $settings->description }}</textarea>-->
                <!--</div>-->
            </div>

            <div class=" form-row">
                <!--<div class="form-group col-md-12">-->
                <!--    <h5 class="text-{{ $text }}">Announcement</h5>-->
                <!--    <textarea name="update" class="form-control " rows="2">{{ $settings->newupdate }}</textarea>-->
                <!--</div>-->
                <div class="form-group col-md-6">
                    <div class="form-group col-md-12">
                    <h5 class="text-{{ $text }}">Bank Address</h5>
                    <textarea name="address" class="form-control " rows="2">{{ $settings->address }}</textarea>
                </div>
                    <!--<h5 class="text-{{ $text }}">Welcome messages for new registered users</h5>-->
                    <!--<textarea name="welcome_message" class="form-control " rows="2">{{ $settings->welcome_message }}</textarea>-->
                    <!--<small class="text-{{ $text }}">This message will be displayed to users whose registration-->
                    <!--    date is less than or equal to 3 days</small>-->
                </div>
                 <div class="form-group col-md-6">
                    <h5 class="text-{{ $text }}">whatsapp number</h5>
                    <input name="whatsapp" class="form-control " type="text"
                        value="{{ $settings->whatsapp }}">
                </div>
            </div>

            <div class="form-row border-top pt-4 mt-2">
                <div class="form-group col-12">
                    <h5 class="text-{{ $text }} mb-1">Live Chat Provider</h5>
                    <p class="small text-muted mb-3">Choose one active widget. Leave unused keys blank. Only the selected provider is loaded on the site.</p>
                </div>
                <div class="form-group col-md-6">
                    <h5 class="text-{{ $text }}">Active Provider</h5>
                    <select name="livechat_provider" id="livechat_provider" class="form-control">
                        @php $lc = $settings->livechat_provider ?? 'none'; @endphp
                        <option value="none" {{ $lc === 'none' ? 'selected' : '' }}>None</option>
                        <option value="tidio" {{ $lc === 'tidio' ? 'selected' : '' }}>Tidio</option>
                        <option value="smartsupp" {{ $lc === 'smartsupp' ? 'selected' : '' }}>Smartsupp</option>
                        <option value="chatway" {{ $lc === 'chatway' ? 'selected' : '' }}>Chatway</option>
                    </select>
                </div>
                <div class="form-group col-md-6 livechat-field" data-provider="tidio">
                    <h5 class="text-{{ $text }}">Tidio Public Key</h5>
                    <input name="tido" class="form-control" type="text"
                        value="{{ $settings->tido }}"
                        placeholder="e.g. fouwfr0cnygz4sj8kttyv0cz1rpaayva">
                    <small class="text-muted">From Tidio → Settings → Live Chat → Installation. Use the key from <code>code.tidio.co/KEY.js</code> (with or without <code>.js</code>).</small>
                </div>
                <div class="form-group col-md-6 livechat-field" data-provider="smartsupp">
                    <h5 class="text-{{ $text }}">Smartsupp Key</h5>
                    <input name="smartsupp_key" class="form-control" type="text"
                        value="{{ $settings->smartsupp_key ?? '' }}"
                        placeholder="Your Smartsupp chat key">
                    <small class="text-muted">From <a href="https://www.smartsupp.com/" target="_blank" rel="noopener">Smartsupp</a> → Settings → Chat box → Chat code (<code>_smartsupp.key</code>).</small>
                </div>
                <div class="form-group col-md-6 livechat-field" data-provider="chatway">
                    <h5 class="text-{{ $text }}">Chatway Widget ID</h5>
                    <input name="chatway_widget_id" class="form-control" type="text"
                        value="{{ $settings->chatway_widget_id ?? '' }}"
                        placeholder="id from cdn.chatway.app/widget.js?id=...">
                    <small class="text-muted">From <a href="https://chatway.app/help/how-to-install-chatway/how-to-install-chatway-on-any-website" target="_blank" rel="noopener">Chatway install</a>: <code>widget.js?id=YOUR-ID</code>.</small>
                </div>
            </div>

            <div class="form-row">
                <!-- removed hardcoded live-chat-widget file notice -->
                <div class="form-group col-md-6">
                    <h5 class="text-{{ $text }}">Timezone</h5>
                    <select name="timezone" class="form-control  select2">
                        <option>{{ $settings->timezone }}</option>
                        @foreach ($timezones as $list)
                            <option value="{{ $list }}">{{ $list }}</option>
                        @endforeach
                    </select>
                    <div class="mt-4">
                        <h5 class="text-{{ $text }}">Installation Type</h5>
                        <select name="install_type" class="form-control ">
                            <option>{{ $settings->install_type }}</option>
                            <option>Main-Domain</option>
                            <option>Sub-Domain</option>
                            <option>Sub-Folder</option>
                        </select>
                    </div>

                </div>
            </div>
             <div class="form-group col-md-6 mb-3">
                    <h5 class="">Turn On/Off SMS</h5>
                    <div class="selectgroup">
                        <label class="selectgroup-item">
                            <input type="radio" name="sms" value="1" class="selectgroup-input"
                                {{ $settings->sms == '1' ? 'checked' : '' }}>
                            <span class="selectgroup-button">On</span>
                        </label>
                        <label class="selectgroup-item">
                            <input type="radio" name="sms"
                                {{ $settings->sms != '1' ? 'checked' : '' }} value="0"
                                class="selectgroup-input">
                            <span class="selectgroup-button">Off</span>
                        </label>
                    </div>
                </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <h5 class="text-{{ $text }}">Logo (Recommended size; max width, 200px and max height
                        100px.)</h5>
                    @if (!empty($settings->logo))
                        <div class="mb-2 p-3 bg-light border rounded d-inline-block">
                            <img src="{{ public_storage_url($settings->logo) }}" alt="Current logo" style="max-height: 64px; max-width: 200px; object-fit: contain;">
                        </div>
                        <p class="small text-muted mb-2">Current: {{ $settings->logo }}</p>
                    @endif
                    <input name="logo" class="form-control " type="file" accept=".jpg,.jpeg,.png,image/png,image/jpeg">
                </div>
                <div class="form-group col-md-6">
                    <h5 class="text-{{ $text }}">Favicon (Recommended type: png, size: max width, 32px and max
                        height 32px.)</h5>
                    @if (!empty($settings->favicon))
                        <div class="mb-2 p-3 bg-light border rounded d-inline-block">
                            <img src="{{ public_storage_url($settings->favicon) }}" alt="Current favicon" style="max-height: 32px; max-width: 32px; object-fit: contain;">
                        </div>
                        <p class="small text-muted mb-2">Current: {{ $settings->favicon }}</p>
                    @endif
                    <input name="favicon" class="form-control " type="file" accept=".jpg,.jpeg,.png,.ico,image/png,image/jpeg,image/x-icon">
                </div>
            </div>
            <div class="mt-3 form-row">
                <div class="col-12">
                    <input type="submit" class="px-5 btn btn-primary btn-lg" value="Update">
                </div>

            </div>

        </form>
    </div>
</div>
<script>
(function () {
    var select = document.getElementById('livechat_provider');
    if (!select) return;
    function syncLivechatFields() {
        var provider = select.value;
        document.querySelectorAll('.livechat-field').forEach(function (el) {
            el.style.display = el.getAttribute('data-provider') === provider ? '' : 'none';
        });
    }
    select.addEventListener('change', syncLivechatFields);
    syncLivechatFields();
})();
</script>
