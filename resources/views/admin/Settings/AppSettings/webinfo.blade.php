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
                <div class="form-group col-md-6">
                    <h5 class="text-{{ $text }}">Tidio Livechat ID</h5>
                    <input name="tido" class="form-control " type="text"
                        value="{{ $settings->tido }}"
                        placeholder="e.g., abcd1234efgh5678">
                    <small class="text-muted">Enter your Tidio chat widget ID (optional)</small>
                </div>
                <div class="form-group col-md-12">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> <strong>Live Chat Widget:</strong> 
                        To update your live chat script, edit this file directly:
                        <br><code>resources/views/includes/live-chat-widget.blade.php</code>
                        <br><small>Upload the file to your server and clear cache to apply changes.</small>
                    </div>
                </div>
                <!--<div class="form-group col-md-6">-->
                <!--    <h5 class="text-{{ $text }}">Purchase/License Code</h5>-->
                <!--    <input name="purchase_code" class="form-control " type="text"-->
                <!--        value="{{ $moresettings->purchase_code }}">-->
                <!--</div>-->
                <!--<div class="form-group col-md-6">-->
                <!--    <h5 class="text-{{ $text }}">Personal Access Token</h5>-->
                <!--    <input name="merchant_key" class="form-control " type="text"-->
                <!--        value="{{ $settings->merchant_key }}">-->
                <!--</div>-->
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
