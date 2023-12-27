<div class="row">
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="site-card">
            <div class="site-card-header">
                <h3 class="title">{{ __('Profile Settings') }}</h3>
            </div>
            <div class="site-card-body">
                <form action="{{ route('user.setting.profile-update') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-xl-3">
                            <div class="mb-3">
                                <div class="body-title">{{ __('Avatar:') }}</div>
                                <div class="wrap-custom-file">
                                    <input
                                        name="avatar"
                                        id="avatar"
                                        type="file"                                        
                                        accept=".gif, .jpg, .png"
                                        @if ($user->editable_profile == 0) disabled @endif
                                    />
                                    <label for="avatar" 
                                        @if($user->avatar && file_exists('assets/'.$user->avatar)) class="file-ok"
                                        style="background-image: url({{ asset($user->avatar) }})" 
                                        @endif
                                    >
                                        <img
                                            class="upload-icon"
                                            src="{{ asset('global/materials/upload.svg') }}"
                                            alt=""
                                        />
                                        <span>{{ __('Update Avatar') }}</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="progress-steps-form">
                        <div class="row">
                            <div class="col-xl-6 col-md-12">
                                <label for="first_name" class="form-label">{{ __('First Name') }}</label>
                                <div class="input-group">
                                    <input
                                        type="text"
                                        class="form-control @if ($user->editable_profile == 0) disabled @endif"
                                        name="first_name"
                                        id="first_name"
                                        value="{{ $user->first_name }}"
                                        placeholder="First Name"
                                        @if ($user->editable_profile == 0) disabled @endif
                                    />
                                </div>
                            </div>
                            <div class="col-xl-6 col-md-12">
                                <label for="last_name" class="form-label">{{ __('Last Name') }}</label>
                                <div class="input-group">
                                    <input
                                        type="text"
                                        class="form-control @if ($user->editable_profile == 0) disabled @endif"
                                        name="last_name"
                                        id="last_name"
                                        value="{{ $user->last_name }}"
                                        placeholder="Last Name"
                                        @if ($user->editable_profile == 0) disabled @endif
                                    />
                                </div>
                            </div>
                            <div class="col-xl-6 col-md-12">
                                <label for="username" class="form-label">{{ __('Username') }}</label>
                                <div class="input-group">
                                    <input
                                        name="username"
                                        id="username"
                                        type="text"
                                        class="form-control @if ($user->editable_profile == 0) disabled @endif"                                        
                                        value="{{ $user->username }}"
                                        placeholder="Username"
                                        @if ($user->editable_profile == 0) disabled @endif
                                    />
                                </div>
                            </div>
                            <div class="col-xl-6 col-md-12">
                                <label for="gender" class="form-label">{{ __('Gender') }}</label>
                                <div class="input-group">
                                    <select 
                                        name="gender" 
                                        id="gender" 
                                        class="nice-select site-nice-select @if ($user->editable_profile == 0) disabled @endif"
                                        @if ($user->editable_profile == 0) disabled @endif
                                    >
                                        @foreach(['male','female','other'] as $gender)
                                        <option @if($user->gender == $gender) selected @endif value="{{$gender}}">
                                            {{ $gender }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-xl-6 col-md-12">
                                <label for="date_of_birth" class="form-label">{{ __('Date of Birth') }}</label>
                                <div class="input-group">
                                    <input
                                        type="date"
                                        name="date_of_birth"
                                        id="date_of_birth"
                                        class="form-control @if ($user->editable_profile == 0) disabled @endif"
                                        value="{{ $user->date_of_birth }}"
                                        placeholder="Date of Birth"
                                        @if ($user->editable_profile == 0) disabled @endif
                                    />
                                </div>
                            </div>

                            <div class="col-xl-6 col-md-12">
                                <label for="email"
                                       class="form-label">{{ __('Email Address') }}</label>
                                <div class="input-group">
                                    <input 
                                        type="email"
                                        id="email"
                                        class="form-control disabled"
                                        value="{{ $user->email }}" 
                                        placeholder="Email Address"
                                        disabled
                                    />
                                </div>
                            </div>
                            <div class="col-xl-6 col-md-12">
                                <label for="phone" class="form-label">{{ __('Phone') }}</label>
                                <div class="input-group">
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="phone"
                                        id="phone"
                                        value="{{ $user->phone }}"
                                        placeholder="Phone"
                                    />
                                </div>
                            </div>
                            <div class="col-xl-6 col-md-12">
                                <label for="country" class="form-label">{{ __('Country') }}</label>
                                <div class="input-group">
                                    <input
                                        type="text"
                                        id="country"
                                        class="form-control disabled"
                                        value="{{ $user->country }}"
                                        placeholder="Country"
                                        disabled
                                    />
                                </div>
                            </div>

                            <div class="col-xl-6 col-md-12">
                                <label for="city" class="form-label">{{ __('City') }}</label>
                                <div class="input-group">
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="city"
                                        id="city"
                                        value="{{ $user->city }}"
                                        placeholder="City"
                                        @if ($user->editable_profile == 0) disabled @endif
                                    />
                                </div>
                            </div>
                            <div class="col-xl-6 col-md-12">
                                <label for="zip_code" class="form-label">{{ __('Zip') }}</label>
                                <div class="input-group">
                                    <input
                                        type="text"
                                        class="form-control @if ($user->editable_profile == 0) disabled @endif"
                                        name="zip_code"
                                        id="zip_code"
                                        value="{{ $user->zip_code }}"
                                        placeholder="Zip"
                                        @if ($user->editable_profile == 0) disabled @endif
                                    />
                                </div>
                            </div>
                            <div class="col-xl-6 col-md-12">
                                <label for="address" class="form-label">{{ __('Address') }}</label>
                                <div class="input-group">
                                    <input
                                        type="text"
                                        class="form-control @if ($user->editable_profile == 0) disabled @endif"
                                        name="address"
                                        id="address"
                                        value="{{ $user->address }}"
                                        placeholder="Address"
                                        @if ($user->editable_profile == 0) disabled @endif
                                    />
                                </div>
                            </div>
                            <div class="col-xl-6 col-md-12">
                                <label for="joining_date" class="form-label">{{ __('Joining Date') }}</label>
                                <div class="input-group">
                                    <input
                                        type="text"
                                        id="joining_date"
                                        class="form-control disabled"
                                        value="{{ carbonInstance($user->created_at)->toDayDateTimeString() }}"
                                        placeholder="Joining Date"
                                        disabled
                                    />
                                </div>
                            </div>

                            <div class="col-xl-6 col-md-12">
                                <button type="submit" class="site-btn blue-btn">{{ __('Save Changes') }}</button>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
