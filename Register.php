<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="./Style/elements.css">
    <link rel="stylesheet" href="./Style/forms.css">
    <script src="./Script/app.js"></script>
</head>

<body>

    <div class="form-container">

        <form id="RegisterForm" class="form" method="post">

            <div class="form-group">
                <label for="FullName">Full Name</label>
                <input 
                    type="text" 
                    id="FullName" 
                    name="FullName" 
                    class="input"
                    autofocus>
                <div id="nameError" class="error-message"></div>
            </div>

            <div class="form-group">
                <label for="Email">Email Address</label>
                <input 
                    type="text" 
                    id="Email" 
                    name="Email" 
                    class="input"
                    placeholder="example@example.com">
                <div id="emailError" class="error-message"></div>
            </div>

            <div class="form-group">
                <label for="Number">Phone Number</label>
                <input 
                    type="number" 
                    id="Number" 
                    name="Number" 
                    class="input"
                    placeholder="70860816">
                <div id="numberError" class="error-message"></div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input 
                    type="password" 
                    id="Password" 
                    name="Password" 
                    class="input"
                    placeholder="********">
                <div id="passwordError" class="error-message"> </div>
            </div>

            <button class="submit" type="submit">Register</button>

            <div class="link">
                <p>Already have an account?<a href="./Login.php"><h4> Login</h4></a></p>
            </div>

        </form>

    </div>


</body>

</html>