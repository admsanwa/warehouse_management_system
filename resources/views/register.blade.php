<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Warehouse Resource Management System | Register</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ url('backend/plugins/fontawesome-free/css/all.min.css')}}">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="{{ url('backend/plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ url('backend/dist/css/adminlte.min.css')}}">
</head>

<body class="hold-transition login-page">
  <div class="login-box">
    <div class="login-logo">
      <a href=""><b>Warehouse</b> Management System</a>
    </div>
    <!-- /.login-logo -->
    <div class="card">

      <div class="card-body login-card-body">
        <p class="login-box-msg">Sign in to start your session</p>

        <form action="{{ url('register_post')}}" method="post">
          {{ csrf_field() }}
          <span style="color: red">{{ $errors->first('username')}}</span>
          <div class="input-group mb-3">
            <input type="text" class="form-control" placeholder="Username" name="username" required value="{{ old('username')}}">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-user"></span>
              </div>
            </div>
          </div>
          <span style="color: red">{{ $errors->first("fullname")}}</span>
          <div class="input-group mb-3">
            <input type="text" class="form-control" placeholder="Fullname" name="fullname" value="{{ old('fullname')}}">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-user"></span>
              </div>
            </div>
          </div>
           <span style="color: red">{{ $errors->first("nik")}}</span>
          <div class="input-group mb-3">
            <input type="text" class="form-control" placeholder="NIK" name="nik" value="{{ old('nik')}}">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-address-card"></span>
              </div>
            </div>
          </div>
          <span style="color: red" class="duplicate_message">{{ $errors->first('email')}}</span>
          <div class="input-group mb-3">
            <input type="text" class="form-control" placeholder="Email" name="email" required value="{{ old('email')}}" onblur="duplicateEmail(this)">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-envelope"></span>
              </div>
            </div>
          </div>
          <span style="color: red">{{ $errors->first('department')}}</span>
          <div class="input-group mb-3">
            <input type="text" class="form-control" placeholder="Department" name="department" required value="{{ old('department')}}">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-user-tie"></span>
              </div>
            </div>
          </div>
          <span style="color: red">{{ $errors->first('level')}}</span>
          <div class="input-group mb-3">
            <input type="text" class="form-control" placeholder="Level" name="level" required value="{{ old('level')}}">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fa fa-user-tie"></span>
              </div>
            </div>
          </div>
          <span style="color: red">{{ $errors->first('password')}}</span>
          <div class="input-group mb-3">
            <input type="password" class="form-control" placeholder="Password" name="password" required>
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-lock"></span>
              </div>
            </div>
          </div>
          <span style="color: red">{{ $errors->first('confirm_password')}}</span>
          <div class="input-group mb-3">
            <input type="password" class="form-control" placeholder="Confirm Password" name="confirm_password" required>
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-lock"></span>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-8">
              <div class="icheck-primary">
              </div>
            </div>
            <!-- /.col -->
            <div class="col-4">
              <button type="submit" class="btn btn-primary btn-block">Register</button>
            </div>
            <!-- /.col -->
          </div>
        </form>

        <!-- /.social-auth-links -->
        <p class="mb-0">
          <a href="{{ url('/')}}" class="text-center">Sign In</a>
        </p>
      </div>
      <!-- /.login-card-body -->
    </div>
  </div>
  <!-- /.login-box -->

  <!-- jQuery -->
  <script src="{{ url('backend/plugins/jquery/jquery.min.js')}}"></script>
  <!-- Bootstrap 4 -->
  <script src="{{ url('backend/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
  <!-- AdminLTE App -->
  <script src="{{ url('backend/dist/js/adminlte.min.js')}}"></script>
  <script type="text/javascript">
    function duplicateEmail(element) {
      var email = $(element).val();
      // alert(email);
      $.ajax({
        type: "POST",
        url: "{{ url('check_email')}}",
        data: {
          email: email,
          _token: "{{ csrf_token()}}",
        },
        dataType: "json",
        success: function(res) {
          if (res.exists) {
            $('.duplicate_message').html("That email is taken, try another.");
          } else {
            $('.duplicate_message').html("");
          }
        },
        error: function(jqXHR, exception) {

        }
      });
    }
  </script>
</body>

</html>