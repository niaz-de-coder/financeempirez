<?php
// Database Configuration
$host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'finance'; // Update this to your actual database name

$conn = new mysqli($host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Check if email already exists
    $checkEmail = "SELECT email FROM User WHERE email = '$email'";
    $result = $conn->query($checkEmail);

    if ($result->num_rows > 0) {
        $error = "This email is already registered.";
    } else {
        $sql = "INSERT INTO User (Name, email, password) VALUES ('$name', '$email', '$password')";
        
        if ($conn->query($sql) === TRUE) {
            // Start session and redirect to dashboard
            session_start();
            $_SESSION['user_email'] = $email;
            $_SESSION['user_name'] = $name;
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up | Finance Empirez</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .auth-gradient { background: radial-gradient(circle at bottom left, #eff6ff 0%, #ffffff 100%); }
    </style>
</head>
<body class="bg-slate-50 auth-gradient min-h-screen flex items-center justify-center p-6">

    <div class="w-full max-w-md">
        <div class="text-center mb-10">
            <a href="index.php" class="text-2xl font-extrabold tracking-tighter inline-flex items-center gap-2">
                <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white text-lg">F</div>
                <span>FINANCE<span class="text-blue-600">Empirez</span></span>
            </a>
            <h2 class="mt-6 text-3xl font-bold text-slate-900">Create your account</h2>
            <p class="text-slate-500 mt-2">Start automating your  finance today.</p>
        </div>

        <div class="bg-white p-8 rounded-[2.5rem] shadow-xl shadow-blue-100 border border-slate-100">
            <?php if($error): ?>
                <div class="mb-6 p-4 bg-red-50 text-red-600 text-sm rounded-2xl border border-red-100 flex items-center gap-3">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form action="signup.php" method="POST" class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2 ml-1">Company / Full Name</label>
                    <div class="relative">
                        <i class="fa-solid fa-building absolute left-5 top-4 text-slate-400"></i>
                        <input type="text" name="name" required placeholder="Niaz IT Solutions" 
                               class="w-full pl-12 pr-6 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-50 transition">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2 ml-1">Email Address</label>
                    <div class="relative">
                        <i class="fa-solid fa-envelope absolute left-5 top-4 text-slate-400"></i>
                        <input type="email" name="email" required placeholder="niaz@example.com" 
                               class="w-full pl-12 pr-6 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-50 transition">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2 ml-1">Password</label>
                    <div class="relative">
                        <i class="fa-solid fa-lock absolute left-5 top-4 text-slate-400"></i>
                        <input type="password" name="password" required placeholder="••••••••" 
                               class="w-full pl-12 pr-6 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-50 transition">
                    </div>
                </div>

                <button type="submit" 
                        class="w-full py-4 bg-blue-600 text-white rounded-2xl font-bold hover:bg-blue-700 transition-all shadow-lg shadow-blue-200 mt-4">
                    Create Account
                </button>
            </form>
        </div>

        <p class="text-center mt-8 text-slate-500 text-sm">
            Already have an account? <a href="login.php" class="text-blue-600 font-bold hover:underline">Sign In</a>
        </p>
    </div>

</body>
</html>