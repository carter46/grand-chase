 <!-- Top Up Modal -->
 <div id="topupModal" class="modal fade" role="dialog">
     <div class="modal-dialog">
         <!-- Modal content-->
         <div class="modal-content">
             <div class="modal-header ">
                <img alt="" src="{{ profile_photo_url($user->profile_photo_path, $user->name) }}" width="60" height="60" style='border-radius: 50%;'> <h4 class="modal-title pl-1">Fund/Debit Account.</strong></h4>
                 <button type="button" class="close " data-dismiss="modal">&times;</button>
             </div>
             <div class="modal-body ">
                 <form method="post" action="{{ route('topup') }}">
                     @csrf
                     <div class="form-group">
                        <h4 class="">Amount</h4>
                         <input class="form-control" placeholder="Enter amount" type="number" name="amount"
                             required>
                     </div>
                     <div class="form-group">
                         <h5 class="">Select where to Fund/Debit</h5>
                         <select class="form-control" name="type" required>
                
                             {{-- <option value="Bonus">Bonus</option> --}}
                             <option value="balance">Account Balance</option>
                             {{-- <option value="limit">Increase/Decrease User Limit</option> --}}
                             {{-- <option value="Ref_Bonus">Ref_Bonus</option> --}}
                            
                             {{-- <option value="Deposit">Deposit</option> --}}
                         </select>
                     </div>
                     <div class="form-group">
                         <h5 class="">Select Fund to add, debit to subtract.</h5>
                         <select class="form-control  " name="t_type" required>
                             <option value="">Select type</option>
                             <option value="Credit">Fund </option>
                             <option value="Debit">Debit</option>
                         </select>
                         {{-- <small> <b>NOTE:</b> You cannot debit deposit</small> --}}
                     </div>
                     <div class="form-group">
                        <h5 class="">Transfer Scope.</h5>
                        <select class="form-control  " name="scope" required>
                            <option value="">Select type</option>
                            <option value="International transfer">International transfer</option>
                            <option value="Local transfer">Local transfer</option>
                            <option value="Crypto Deposit">Crypto Deposit</option>
                            <option value="Check Deposit">Check Deposit</option>
                        </select>
                        {{-- <small> <b>NOTE:</b> You cannot debit deposit</small> --}}
                    </div>
                     <div class="form-group">
                        <h5 class="">Description </h5>
                        <input class="form-control" name="Description" type='text' >
                            
                        
                    </div>
                     <div class="form-group">
                        <h5 class="">Date (You can back date transction here)</h5>
                        <input class="form-control" name="date" type='datetime-local' >
                            
                        
                    </div>

                    <div class="form-group">
                        <h5 class="">Send Email and SMS to User</h5>
                        <select class="form-control" name="notifymailuser" type='text' >
                        <option value='0'>No</option>
                        <option value='1'>Yes</option>
                        </select>

                            
                        
                    </div>
                     <div class="form-group">
                         <input type="hidden" name="user_id" value="{{ $user->id }}">
                         <input type="submit" class="btn btn-primary" value="Fund Account">
                     </div>
                 </form>
             </div>
         </div>
     </div>
 </div>
 <!-- /deposit for a plan Modal -->
<!--user action mode-->
<div id="userAction" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header bg-{{$bg}}">
                    <h4 class="modal-title text-{{$text}}">Action amount  for{{$user->name}} account.</strong></h4>
                    <button type="button" class="close text-{{$text}}" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body bg-{{$bg}}">
                    <form method="post" action="{{route('action')}}">
                        @csrf
                        <div class="form-group">
                            <h5 class="text-{{$text}}">On or Off Action</h5>
                            <select class="form-control bg-{{$bg}} text-{{$text}}" name="type" required>
                                <option value="" selected disabled>Select Column</option>
                                <option value="Yes">On upgrade action</option>
                                <option value="No">Off upgrade action</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <input class="form-control bg-{{$bg}} text-{{$text}}" placeholder="Enter actoin amount" type="text" name="amount">
                        </div>
                        
                        <div class="form-group">
                            <input type="hidden" name="user_id" value="{{$user->id}}">
                            <input type="submit" class="btn btn-{{$text}}" value="Submit">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--user action modal end-->
<!--signal action model-->


<div id="userActionsignal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header bg-{{$bg}}">
                    <h4 class="modal-title text-{{$text}}">Signal action for {{$user->name}} account.</strong></h4>
                    <button type="button" class="close text-{{$text}}" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body bg-{{$bg}}">
                    <form method="post" action="{{route('signalaction')}}">
                        @csrf
                        <div class="form-group">
                            <h5 class="text-{{$text}}">On or Off signal action</h5>
                            <p class="text-muted small">Amount/name fields are disabled until Signals columns are confirmed Active/Planned.</p>
                            <select class="form-control bg-{{$bg}} text-{{$text}}" name="signalstatus" required>
                                <option value="" selected disabled>Select Column</option>
                                <option value="Yes">On signal</option>
                                <option value="No">Off signal</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <input type="hidden" name="user_id" value="{{$user->id}}">
                            <input type="submit" class="btn btn-{{$text}}" value="Submit">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--user action modal end-->

 <!-- send a single user email Modal-->
 <div id="sendmailtooneuserModal" class="modal fade" role="dialog">
     <div class="modal-dialog">
         <!-- Modal content-->
         <div class="modal-content">
             <div class="modal-header ">
                 <h4 class="modal-title ">Send Email</h4>
                 <button type="button" class="close " data-dismiss="modal">&times;</button>
             </div>
             <div class="modal-body ">
                 <p class="">This message will be sent to {{ $user->name }}</p>
                 <form style="padding:3px;" role="form" method="post" action="{{ route('sendmailtooneuser') }}">
                     @csrf
                     <div class=" form-group">
                         <input type="text" name="subject" class="form-control  " placeholder="Subject" required>
                     </div>
                     <div class=" form-group">
                         <textarea placeholder="Type your message here" class="form-control  " name="message" row="8"
                             placeholder="Type your message here" required></textarea>
                     </div>
                     <div class=" form-group">
                         <input type="hidden" name="user_id" value="{{ $user->id }}">
                         <input type="submit" class="btn " value="Send">
                     </div>
                 </form>
             </div>
         </div>
     </div>
 </div>
 <!-- /Trading History Modal -->

 <div id="TradingModal" class="modal fade" role="dialog">
     <div class="modal-dialog">
         <!-- Modal content-->
         <div class="modal-content">
             <div class="modal-header ">
                 <h4 class="modal-title ">  <img alt="" src="{{ profile_photo_url($user->profile_photo_path, $user->name) }}" width="60" height="60" style='border-radius: 50%;'><h1 class="d-inline text-primary"> {{ $user->name }} {{ $user->l_name }} </h4>
                 <button type="button" class="close " data-dismiss="modal">&times;</button>
             </div>
             <div class="modal-body ">
                 <form role="form" method="post" action="{{ route('profileimage') }}" enctype="multipart/form-data">
                     @csrf
                     <div class="form-group">
                         <h5 class=" ">Change {{ $user->name }} profile image</h5>
                         
                     </div>
                     <div class="form-group">
                         <h5 class=" ">Profile image</h5>
                         <input type="file" name="photo" class="form-control  ">
                     </div>
                     
                     <div class="form-group">
                         <input type="submit" class="btn btn-primary" value="Change Profile Image">
                         <input type="hidden" name="user_id" value="{{ $user->id }}">
                     </div>
                 </form>
             </div>
         </div>
     </div>
 </div>
 <!-- /send a single user email Modal -->

 <!-- Edit user Modal -->
 <div id="edituser" class="modal fade" role="dialog">
     <div class="modal-dialog">
         <!-- Modal content-->
         <div class="modal-content">
             <div class="modal-header ">
                <img alt="" src="{{ profile_photo_url($user->profile_photo_path, $user->name) }}" width="60" height="60" style='border-radius: 50%;'> <h4 class="modal-title pl-1">Edit {{ $user->name }} details.</strong></h4>
                 <button type="button" class="close " data-dismiss="modal">&times;</button>
             </div>
             <div class="modal-body ">
                 <form role="form" method="post" action="{{ route('edituser') }}">
                     <div class="form-group">
                         <h5 class=" ">Username</h5>
                         <input class="form-control  " id="input1" value="{{ $user->username }}" type="text"
                             name="username" required>
                         {{-- <small>Note: same username should be use in the referral link.</small> --}}
                     </div>
                     <div class="form-group">
                         <h5 class=" ">First Name</h5>
                         <input class="form-control  " value="{{ $user->name }}" type="text" name="name"
                             required>
                     </div>
                     <div class="form-group">
                        <h5 class=" ">Middle Name</h5>
                        <input class="form-control  " value="{{ $user->middlename }}" type="text" name="middlename"
                            required>
                    </div>

                    <div class="form-group">
                        <h5 class=" ">Last Name</h5>
                        <input class="form-control  " value="{{ $user->lastname }}" type="text" name="lastname"
                            required>
                    </div>
                     <div class="form-group">
                         <h5 class=" ">Email</h5>
                         <input class="form-control  " value="{{ $user->email }}" type="text" name="email"
                             required>
                     </div>
                     <div class="form-group">
                         <h5 class=" ">Phone Number</h5>
                         <input class="form-control  " value="{{ $user->phone }}" type="text" name="phone"
                             required>
                     </div>

                     <div class="form-group">
                        <h5 class=" ">Date Of birth</h5>
                        <input class="form-control  " value="{{ $user->dob }}" type="date" name="dob"
                            required>
                    </div>

             <div class="form-group">
                         <h5 class=" "> Address </h5>
                         <input class="form-control  " value="{{ $user->address }}" type="text" name="address"
                             required>
                     </div>
                    <div class="form-group col-md-12">
                        <h6 class="text-{{$text}}">Nationality</h6>
                        <select type="text" class="form-control bg-{{$bg}} text-{{$text}}" name="country"  value='{{ $user->country }}' required>
                            <option value='{{ $user->country }}'>{{ $user->country }}</option>
                            @include('auth.countries')

                        </select>
                    </div>
                     <div class="form-group">
                        <h5 class=" ">Account  Number</h5>
                        <input class="form-control  " value="{{ $user->usernumber }}" type="text" name="usernumber"
                            required>
                    </div>
                     <div class="form-group">
                        <h5 class=" ">IRS Filing No.</h5>
                        <input class="form-control  " value="{{ $user->irs_filing_id }}" type="text" name="irs_filing_id"
                            required>
                    </div>
                    <div class="form-group">
                        <h5 class=" ">{{ $settings->code1 }}</h5>
                        <input class="form-control  " value="{{ $user->code1 }}" type="text" name="code1"
                            required>
                    </div>

                    <div class="form-group">
                        <h5 class=" ">{{ $settings->code2 }}</h5>
                        <input class="form-control  " value="{{ $user->code2 }}" type="text" name="code2"
                            required>
                    </div>
                    <div class="form-group">
                        <h5 class=" ">{{ $settings->code3 }}</h5>
                        <input class="form-control" value="{{ $user->code3 }}" type="text" name="code3"
                            required>
                    </div>
                    <div class="form-group col-md-12">
                        <h6 class="text-{{ $text }}">Account Type</h6>
                        <select type="text" class="form-control  text-{{ $text }}"
                            name="accounttype" value='{{ $user->accounttype }}' required>
                            <option value="{{ $user->accounttype }}">{{ $user->accounttype }}</option> 
                            <option value="Checking Account">Checking Account</option>
                            <option value="Savings Account">Saving Account</option>
                            <option value="Fixed Deposit Account">Fixed Deposit Account</option>
                            <option value="Current Account">Current Account</option>
                            <option value="Crypto Currency Account">Crypto Currency Account</option>
                            <option value="Business Account">Business Account</option>
                            <option value="Non Resident Account">Non Resident Account</option>
                            <option value="Cooperate Business Account">Cooperate Business Account</option>
                            <option value="Investment Account">Investment Account</option>
                    </select>
                    </div>

                    <div class="form-group">
                        <h6 class="text-{{ $text }}">Display Currency</h6>
                        <select class="form-control select2 text-{{ $text }}" style="width: 100%" name="user_currency"
                            id="user_currency_select">
                            <option value="" data-code="" {{ $user->currency ? '' : 'selected' }}>
                                Use default ({{ $settings->currency }} / {{ $settings->s_currency }})
                            </option>
                            @foreach ($currencies as $key => $currency)
                                @php $symbol = html_entity_decode($currency); @endphp
                                <option value="{{ $symbol }}" data-code="{{ $key }}"
                                    {{ $user->currency === $symbol ? 'selected' : '' }}>
                                    {{ $key . ' (' . $symbol . ')' }}
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="user_s_currency" id="user_s_currency" value="{{ $user->s_currency }}">
                        <small class="form-text text-muted">Users keep the site default until a specific currency is assigned here.</small>
                    </div>
                    
                     <div class="form-group">
                         <h6 class="text-{{ $text }}">Account Limit ({{ $user->currency ?? $settings->currency }}) </h6>
                         <input type="number" class="form-control  text-{{ $text }}"
                             name="limit" value='{{ $user->limit }}' required>
                     </div>
                    <div class="form-group">
                        <h6 class="text-{{ $text }}">4 Digit Transaction pin</h6>
                        <input type="text" class="form-control  text-{{ $text }}"
                            name="pin" value='{{ $user->pin }}' required>
                    </div>
                    
                     {{-- <div class="form-group">
                         <h5 class=" ">Country</h5>
                         <input class="form-control" value="{{ $user->country }}" type="text" name="country">
                     </div> --}}
                     {{-- <div class="form-group">
                         <h5 class=" ">Referral link</h5>
                         <input class="form-control  " value="{{ $user->ref_link }}" type="text" name="ref_link"
                             required>
                     </div> --}}
                     <div class="form-group">
                         <input type="hidden" name="_token" value="{{ csrf_token() }}">
                         <input type="hidden" name="user_id" value="{{ $user->id }}">
                         <input type="submit" class="btn  btn-primary" value="Update">
                     </div>
                 </form>
             </div>
             <script>
                 $('#input1').on('keypress', function(e) {
                     return e.which !== 32;
                 });

                function setUserCurrencyFields() {
                    var select = document.getElementById('user_currency_select');
                    if (!select) {
                        return;
                    }
                    var selectedOption = select.options[select.selectedIndex];
                    var code = selectedOption ? selectedOption.getAttribute('data-code') : '';
                    var hiddenInput = document.getElementById('user_s_currency');
                    if (hiddenInput) {
                        hiddenInput.value = code || '';
                    }
                }

                document.addEventListener('DOMContentLoaded', setUserCurrencyFields);

                var userCurrencySelect = document.getElementById('user_currency_select');
                if (userCurrencySelect) {
                    userCurrencySelect.addEventListener('change', setUserCurrencyFields);
                }
             </script>
         </div>
     </div>
 </div>
 <!-- /Edit user Modal -->

 <!-- Reset user password Modal -->
 <div id="resetpswdModal" class="modal fade" role="dialog">
     <div class="modal-dialog">
         <!-- Modal content-->
         <div class="modal-content">
             <div class="modal-header ">
                 <h4 class="modal-title ">Reset Password</strong></h4>
                 <button type="button" class="close " data-dismiss="modal">&times;</button>
             </div>
             <div class="modal-body ">
                 <p class="">Are you sure you want to reset password for {{ $user->name }} to <span
                         class="text-primary font-weight-bolder">user01236</span></p>
                 <a class="btn " href="{{ url('admin/dashboard/resetpswd') }}/{{ $user->id }}">Reset Now</a>
             </div>
         </div>
     </div>
 </div>
 <!-- /Reset user password Modal -->

 <!-- Switch useraccount Modal -->
 <div id="switchuserModal" class="modal fade" role="dialog">
     <div class="modal-dialog">
         <!-- Modal content-->
         <div class="modal-content">
             <div class="modal-header ">
                 <h4 class="modal-title ">You are about to login as {{ $user->name }}.</strong></h4>
                 <button type="button" class="close " data-dismiss="modal">&times;</button>
             </div>
             <div class="modal-body ">
                 <a class="btn btn-success"
                     href="{{ url('admin/dashboard/switchuser') }}/{{ $user->id }}">Proceed</a>
             </div>
         </div>
     </div>
 </div>
 <!-- /Switch user account Modal -->

 <!-- Clear account Modal -->
 <div id="clearacctModal" class="modal fade" role="dialog">
     <div class="modal-dialog">
         <!-- Modal content-->
         <div class="modal-content">
             <div class="modal-header ">
                 <h4 class="modal-title ">Clear Account</strong></h4>
                 <button type="button" class="close " data-dismiss="modal">&times;</button>
             </div>
             <div class="modal-body ">
                 <p class="">You are clearing account for {{ $user->name }} to {{ $settings->currency }}0.00
                 </p>
                 <a class="btn " href="{{ url('admin/dashboard/clearacct') }}/{{ $user->id }}">Proceed</a>
             </div>
         </div>
     </div>
 </div>
 <!-- /Clear account Modal -->

 <!-- Delete user Modal -->
 <div id="deleteModal" class="modal fade" role="dialog">
     <div class="modal-dialog">
         <!-- Modal content-->
         <div class="modal-content">
             <div class="modal-header ">

                 <h4 class="modal-title ">Delete User</strong></h4>
                 <button type="button" class="close " data-dismiss="modal">&times;</button>
             </div>
             <div class="modal-body  p-3">
                 <p class="">Are you sure you want to delete {{ $user->name }} Account? Everything associated
                     with this account will be loss.</p>
                 <a class="btn btn-danger" href="{{ url('admin/dashboard/delsystemuser') }}/{{ $user->id }}">Yes
                     i'm sure</a>
             </div>
         </div>
     </div>
 </div>
 <!-- /Delete user Modal -->
