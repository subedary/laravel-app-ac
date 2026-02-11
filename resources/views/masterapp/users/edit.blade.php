@extends('masterapp.layouts.app')
@section('title', 'User Edit', 'bold')
@section('content')

    <div class="container-fluid">
        <div class="row">
          <!-- left column -->
          <div class="col-md-12">
            <!-- general form elements -->
            <div class="card card-primary shadow-md">
                <div class="card-header">
                <h3 class="card-title">User Edit</h3>
              </div> 
              <!-- /.card-header -->
              <!-- form start -->
              <form id="userEditForm" action="{{ route('masterapp.users.update', $user->id) }}" method="POST">
                    @csrf
                     @method('PUT')
                    <div class="card-body">
                        <div class="col-lg-10 bordar">
                            <div class="row">
                                <div class="col-sm-6">
                                <!-- text input -->
                                    <div class="form-group">
                                        <label for="InputFirstName">First Name <span class="text-danger">*</span></label>
                                        <input type="text" name="first_name" class="form-control" id="InputFirstName" placeholder="Enter first name" value="{{ old('first_name', $user->first_name) }}">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="InputLastName">Last Name <span class="text-danger">*</span></label>
                                        <input type="text" name="last_name" class="form-control" id="InputLastName" placeholder="Enter last name" value="{{ old('last_name', $user->last_name) }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                    <label for="InputEmail">Email address <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control" id="InputEmail" placeholder="Enter email" value="{{ old('email', $user->email) }}" pattern="^[^\s@]+@[^\s@]+\.[^\s@]+$">                                </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="InputPhone">Phone number</label>
                                        <input type="tel" name="phone" class="form-control" id="InputPhone" placeholder="Enter phone number" value="{{ old('phone', $user->phone) }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="InputPassword">Password</label>
                                        <div class="input-group">
                                            <input type="password" name="password" class="form-control" id="InputPassword" placeholder="Enter password" autocomplete="new-password">
                                            <div class="input-group-append">
                                                <span class="input-group-text" id="toggleEditPassword" style="cursor: pointer;">
                                                    <i class="fas fa-eye"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="InputConfirmPassword">Confirm Password</label>
                                        <div class="input-group">
                                            <input type="password" name="password_confirmation" class="form-control" id="InputConfirmPassword" placeholder="Confirm password">
                                            <div class="input-group-append">
                                                <span class="input-group-text" id="toggleEditConfirmPassword" style="cursor: pointer;">
                                                    <i class="fas fa-eye"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <span id="editPasswordMatchMessage" class="small"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                <!-- Select multiple-->
                                <div class="form-group">
                                    <label for="InputRoles">Assign Role(s)<span class="text-danger">*</span></label>
                                    <select name="roles[]" multiple required class="form-control select2" id="InputRoles">
                                         @foreach($roles as $id => $name)
                                            <option value="{{ $id }}" {{ (in_array($id, old("roles", $user->roles->pluck('id')->toArray()))) ? 'selected' : '' }}>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                </div>
                            {{-- Publications --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                <label for="InputPublications">Publications</label>
                                    @php
                                    $selectedPublications = old('publications', $user->publications->pluck('id')->toArray());
                                @endphp
                                <select name="publications[]" class="form-control select2" multiple id="InputPublications">
                                    @foreach ($publications as $id => $name)
                                        <option value="{{ $id }}" {{ in_array($id, $selectedPublications) ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                                </div>
                            </div>
                            </div>
                            
                             {{-- STATUS  --}}
                            <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="InputContributor">Contributor Status</label>
                                            {{-- <select name="contributor_status" class="form-control">
                                                <option value="No" {{ old('contributor_status', 'No') === 'No' ? 'selected' : '' }}>
                                                    No
                                                </option>
                                                <option value="Current" {{ old('contributor_status') === 'Current' ? 'selected' : '' }}>
                                                    Current
                                                </option>
                                                <option value="Past" {{ old('contributor_status') === 'Past' ? 'selected' : '' }}>
                                                    Past
                                                </option>
                                            </select> --}}
                                            <select name="contributor_status" class="form-control">
                                            @foreach (['no', 'current', 'past'] as $status)
                                                <option
                                                    value="{{ $status }}"
                                                    {{ old('contributor_status', $user->contributor_status) === $status ? 'selected' : '' }}>
                                                    {{ $status }}
                                                </option>
                                            @endforeach
                                            </select>

                                        </div>
                                    </div>



                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="InputWordpressUser">WordPress User</label>
                                        {{-- <select name="is_wordpress_user" class="form-control">
                                            <option value="0" {{ old('is_wordpress_user', '0') == '0' ? 'selected' : '' }}>No</option>
                                            <option value="1" {{ old('is_wordpress_user') == '1' ? 'selected' : '' }}>Yes</option>
                                        </select> --}}
                                        <select name="is_wordpress_user" class="form-control">
                                        <option value="0"
                                            {{ old('is_wordpress_user', $user->is_wordpress_user) == 0 ? 'selected' : '' }}>
                                            No
                                        </option>

                                        <option value="1"
                                            {{ old('is_wordpress_user', $user->is_wordpress_user) == 1 ? 'selected' : '' }}>
                                            Yes
                                        </option>
                                    </select>

                                    </div>
                                </div>
                            </div>

                            {{-- PUBLICATIONS --}}
                        {{-- <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                <label for="InputPublications">Publications</label>
                                    @php
                                    $selectedPublications = old('publications', $user->publications->pluck('id')->toArray());
                                @endphp
                                <select name="publications[]" class="form-control select2" multiple id="InputPublications">
                                    @foreach ($publications as $id => $name)
                                        <option value="{{ $id }}" {{ in_array($id, $selectedPublications) ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                                </div>
                            </div>
                        </div> --}}
                                                   
                               <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="InputDepartment">Department</label>
                                        {{-- <select name="department_id" class="form-control">
                                            <option value="">Select department</option>
                                            @foreach($departments as $department)
                                                <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                                    {{ $department->name }}</option>
                                            @endforeach
                                        </select> --}}
                                        <select name="department_id" class="form-control">
                                            <option value="">Select department</option>

                                        @foreach ($departments as $department)
                                            <option
                                                value="{{ $department->id }}"
                                                {{ old('department_id', $user->department_id) == $department->id ? 'selected' : '' }}
                                            >
                                                {{ $department->name }}
                                            </option>
                                        @endforeach
                                        </select>

                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="InputDriver">Driver</label>
                                        <select name="driver" class="form-control">
                                            <option value="0" {{ old('driver', $user->driver) == 0 ? 'selected' : '' }}>No</option>
                                            <option value="1" {{ old('driver', $user->driver) == 1 ? 'selected' : '' }}>Yes</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Active -->
                             {{-- STATUS --}}
                            <div class="row">
                                 <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="InputActive">Active Status</label>
                                        <select name="active" class="form-control">
                                            <option value="1" {{ old('active', $user->active) == '1' ? 'selected' : '' }}>Active</option>
                                            <option value="0" {{ old('active', $user->active) == '0' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                    </div>
                                </div>
                            <div class="col-md-6">
                            <div class="form-group">
                                <label for="InputStatus">Status</label>
                                {{-- <select name="status_id" class="form-control">
                                    <option value="">Select status</option>
                                    @foreach($statusesList as $status)
                                        <option value="{{ $status->id }}" {{ old('status_id') == $status->id ? 'selected' : '' }}>{{ $status->label }}</option>
                                    @endforeach
                                </select> --}}
                                <select name="status_id" class="form-control">
                                        <option value="">Select status</option>

                                    @foreach ($statusesList as $status)
                                        <option
                                            value="{{ $status->id }}"
                                            {{ old('status_id', $user->status_id) == $status->id ? 'selected' : '' }}
                                        >
                                            {{ $status->label }}
                                        </option>
                                    @endforeach
                                    </select>

                            </div>
                        </div>
                    </div>
                         {{-- STATUS NOTES --}}
                            <div class="row">
                            <div class="col-md-6">
                            <div class="form-group">
                                <label for="status_notes">Status Notes</label>
                                <textarea name="status_notes" id="status_notes" class="form-control" rows="4" maxlength="200">{{ old('status_notes', $user->status_notes) }}</textarea>
                                <small class="text-muted">
                                    <span id="statusNotesCount">{{ strlen(old('status_notes', $user->status_notes ?? '')) }}</span> / 200 characters
                                </small>
                            </div>

                            </div>
                        </div>
                    </div>
                        <!-- Change Password (for new user always current) -->
                        <input type="hidden" name="change_password" value="1">
                    </div>
                    <!-- /.card-body -->

                    <div class="card-footer">
                        <a href="{{ url()->previous() }}" class="btn btn-secondary">
                        Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <span id="btn-edit-text">Submit</span>
                            <span id="btn-edit-spinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        </button>
                    </div>
                </form>
            </div>
            <!-- /.card -->
          </div>
        
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->


@endsection


@push('scripts')

<!-- Select2 CSS -->

<!-- Select2 JS -->
<script>

$(document).ready(function () {
     $('.select2').select2({
        theme: 'bootstrap4',
         width: '100%'
       
     });
 });
// $(document).ready(function () {
//     //Initialize Select2 Elements
//     $('.select2').select2();
//     $('.select2bs4').select2({
//         theme: 'bootstrap'
//     });
//   });

  $(function () {
    $('#publicationsSelect').select2({
        placeholder: 'Select publication(s)',
        allowClear: true,
        width: '100%'
    });
});
$('#status_notes').on('input', function () {
    const len = $(this).val().length;
    $('#statusNotesCount').text(len);
});

</script>



<script>
// Allow only letters + spaces (no numbers, no symbols)
$.validator.addMethod(
    'lettersOnly',
    function (value, element) {
        return this.optional(element) || /^[A-Za-z\s]+$/.test(value);
    },
    'Only letters are allowed'
);

// Allow only digits
$.validator.addMethod(
    'digitsOnly',
    function (value, element) {
        return this.optional(element) || /^[0-9]+$/.test(value);
    },
    'Only numbers are allowed'
);

$(function () {

    // Toast (GLOBAL)
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 5000,
        timerProgressBar: true,
        showClass: { popup: 'animate__animated animate__fadeInUp' },
        hideClass: { popup: 'animate__animated animate__fadeOutDown' }
    });

    // Select2
    $('.select2').select2({ width: '100%' });

    // jQuery Validate + AJAX
    $('#userEditForm').validate({
        submitHandler: function (form) {

            // sync status notes
            // $('#status_notes_input').val(
            //     $('#status_notes_editor').html().trim()
            // );

            const $form = $(form);
            const $btn = $form.find('button[type="submit"]');
            $btn.prop('disabled', true);
            $('#btn-edit-text').addClass('d-none');
            $('#btn-edit-spinner').removeClass('d-none');
            $.ajax({
                url: $form.attr('action'),
                type: 'POST',
                data: $form.serialize(),
                dataType: 'json',

                success: function (res) {
                    $('#btn-edit-text').removeClass('d-none');
                    $('#btn-edit-spinner').addClass('d-none');
                    window.location.href =
                        "{{ route('masterapp.users.index') }}" +
                        "?created=1&message=" + encodeURIComponent(res.message);
                },

                error: function (xhr) {
                    $btn.prop('disabled', false);
                    $('#btn-edit-text').removeClass('d-none');
                    $('#btn-edit-spinner').addClass('d-none');

                    if (xhr.status === 422 && xhr.responseJSON?.errors) {
                        let msg = '';
                        $.each(xhr.responseJSON.errors, (field, arr) => {
                            // Skip password confirmation mismatch
                            if (
                                field === 'password' &&
                                arr.some(e => e.toLowerCase().includes('confirmation'))
                            ) {
                                return; // continue
                            }
                            msg += arr.join('<br>') + '<br>';
                        });

                        Toast.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            html: msg
                        });
                    } else {
                        Toast.fire({
                            icon: 'error',
                            title: 'Something went wrong'
                        });
                    }
                }
            });

            return false;
        },

        rules: {
            first_name: { 
                required: true, 
                lettersOnly: true 
            },
            last_name:  { 
                required: true, 
                lettersOnly: true 
            },

            email: {
                required: true,
                email: true
            },
            password: {
                required: false,
                minlength: 6
            },
            roles: {
                required:true,
                
            },
            'roles[]': {
                required: true
            },
            phone: {
                digitsOnly: true,  
                minlength: 7,
                maxlength: 10
            },
            status_notes: {
                maxlength: 200
            },
        },

        messages: {
            first_name: {
                required: "Please enter a first name", 
                lettersOnly: "First name cannot contain numbers or symbols",
            },
            last_name: {
                required: "Please enter a last name",
                lettersOnly: "Last name cannot contain numbers or symbols",
            },
            email: {
                required: "Please enter an email address",
                email: "Please enter a valid email address"
            },
            password: {
                required: "Please provide a password",
                minlength: "Password must be at least 6 characters"
            },
            roles: {
                required: "Please select at least one role",
            },
             'roles[]': {
                required: "Please select at least one role"
            },
             status_notes: {
                maxlength: "Status notes cannot exceed 200 characters"
            },
        },

        errorElement: 'span',
        errorPlacement: function (error, element) {
            error.addClass('invalid-feedback');
            element.closest('.form-group').append(error);
        },

        highlight: function (element) {
            $(element).addClass('is-invalid');
        },

        unhighlight: function (element) {
            $(element).removeClass('is-invalid');
        }
    });
// The global ajaxError handler will catch any 403s from them.
    // handleAjaxForm("#userEditForm", {
    //   loadingIndicator: 'button',
    //   buttonTextSelector: '#btn-text',
    //   buttonSpinnerSelector: '#btn-spinner',
    //   modalToClose: "#genericModal",
    //   reloadOnSuccess: true
    // });

   

});
</script>

<script>
$(document).ready(function() {
    // Toggle password visibility for edit form
    $('#toggleEditPassword').on('click', function() {
        const input = $('#InputPassword');
        const icon = $(this).find('i');
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    $('#toggleEditConfirmPassword').on('click', function() {
        const input = $('#InputConfirmPassword');
        const icon = $(this).find('i');
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    // Password match validation for edit form
    $('#InputPassword, #InputConfirmPassword').on('input', function() {
        const password = $('#InputPassword').val();
        const confirm = $('#InputConfirmPassword').val();
        const message = $('#editPasswordMatchMessage');
        if (password && confirm) {
            if (password === confirm) {
                message.text('Passwords match').removeClass('text-danger').addClass('text-success');
            } else {
                message.text('Passwords do not match').removeClass('text-success').addClass('text-danger');
            }
        } else {
            message.text('').removeClass('text-success text-danger');
        }
    });
});
</script>

@endpush
