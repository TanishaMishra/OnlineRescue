<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Register</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="./assets/css/register.css" />
  </head>

<head>
  <title>Add Registration</title>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" href="./assets/css/register.css" />
</head>

<body>
  <form name="registerForm" action="./insertRegistration.php" method="post" class="card">
    <h2 class="header">Sign Up as a Patient</h2>
    <h6 class="info" id="info">Please enter your details</h6>
    <input type="text" name="pname" id="pname" placeholder="Full Name" required />
    <div class="grp">
      <input type="number" name="page" id="page" placeholder="Age" min="0" max="100" required />
      <select id="pgender" name="pgender">
        <option value="m">Male</option>
        <option value="f">Female</option>
        <option value="t">Others</option>
      </select>
    </div>
    <input type="email" name="pemail" id="pemail" placeholder="Email Address" required />
    <input type="number" name="pphone" id="pphone" placeholder="Mobile Number" required />
    <input type="text" name="puser" id="puser" placeholder="Username" required />
    <input type="password" name="ppwd" id="ppwd" class="form-control" placeholder="Password" min="3" max="15" required />
    <input type="password" name="ppwd1" id="ppwd1" placeholder="Confirm Password" min="3" max="15" required />
    <button type="button" name="register" onclick="return onSubmit();">
      Register
    </button>
  </form>
</body>
<script>
  const onSubmit = () => {
    let error = ''

    /** Validate password */
    const pass = document.querySelector('#ppwd')
    const pass2 = document.querySelector('#ppwd1')
    const regEx = new RegExp(/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d).*$/)
    if (!pass.checkValidity() || !regEx.test(pass.value)) {
      error =
        'Password should have one lowercase, one uppercase, one digit (3-15 characters)'
    } else if (pass.value !== pass2.value) {
      /** Check for matching passwords */
      error = 'Your passwords do not match'
    }

    /** Validate username */
    const uname = document.querySelector('#puser')
    if (!uname.checkValidity()) {
      error = 'Username should be between 3-15 characters'
    }

    /** Validate phone number */
    const pNo = document.querySelector('#pphone')
    if (!pNo.checkValidity()) {
      error = 'Mobile number should be 10 digits'
    }

    /** Validate email */
    const email = document.querySelector('#pemail')
    if (!email.checkValidity()) {
      error = 'Invalid email entered'
    }

    /** Validate age */
    const age = document.querySelector('#page')
    if (!age.checkValidity()) {
      error = 'Invalid age entered'
    }

    /** Validate Patient Name */
    const fullName = document.querySelector('#pname')
    if (fullName.value.split(/\W+/).length !== 2) {
      error = 'Please enter your full name'
    }

    if (error === '') document.registerForm.submit()
    else {
      const info = document.querySelector('#info')
      info.textContent = error
      info.style.color = 'red'
    }
  }
</script>

</html>