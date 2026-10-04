<main class="app-content">
    <div class="app-title">
        <div>Transfer Code Settings</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="row">
                    <div class="col-lg-12">
                        <form method="post" action="{{ route('updatertransfercodes') }}" id="appinfoform" enctype="multipart/form-data" class="form-horizontal form-bordered">
                            @method('PUT')
                            @csrf
                        
                            <div class="row">
                                <div class="form-group col-md-4 mb-3">
                                  <h5>  <label for="exampleInputEmail1">Code 1</label></h5>
                                    <input class="form-control" type="text" name="code1" value="{{$settings->code1}}"  placeholder="Enter code1 name">
                                </div>
                                <div class="form-group col-md-4 mb-3">
                                  <h5>  <label for="exampleInputEmail1">Code 2</label></h5>
                                    <input class="form-control" type="text"  name="code2" value="{{$settings->code2}}"  placeholder="Enter code2 name">
                                </div>
                                  <div class="form-group col-md-4 mb-3">
                                  <h5>  <label for="exampleInputEmail1">Code 3</label></h5>
                                    <input class="form-control" type="text"  name="code3" value="{{$settings->code3}}"  placeholder="Enter code3 name">
                                </div>
                                 </div>
                                 <div class="row">
                                    <div class="form-group col-md-4 mb-3">
                                      <h5>  <label for="exampleInputEmail1">Code 1 Message</label></h5>
                                      <textarea name="code1message" class="form-control " rows="2">{{ $settings->code1message }}</textarea>
                                      <small class="text-{{ $text }}">This message will be displayed to users when code1 is required on their transfer</small>
                                    </div>
                                    <div class="form-group col-md-4 mb-3">
                                      <h5>  <label for="exampleInputEmail1">Code  2 message</label></h5>
                                      <textarea name="code2message" class="form-control " rows="2">{{ $settings->code2message }}</textarea>
                                      <small class="text-{{ $text }}">This message will be displayed to users when code2 is required on their transfer</small>
                                    </div>
                                      <div class="form-group col-md-4 mb-3">
                                      <h5>  <label for="exampleInputEmail1">Code3 message</label></h5>
                                      <textarea name="code3message" class="form-control " rows="2">{{ $settings->code3message }}</textarea>
                                      <small class="text-{{ $text }}">This message will be displayed to users when code3 is required on their transfer</small>
                                    </div>
                                     </div>
                                 <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <small class="text-{{ $text }}">Turn each code and the transfer OTP on or off per user from Manage Users &rarr; user details.</small>
                                    </div>
                                 </div>
                            <div class="tile-footer">
                                <button class="btn btn-primary" style="width: 100%!important;" type="submit">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
