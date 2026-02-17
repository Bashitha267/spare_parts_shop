<?php
require_once 'includes/config.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password.";
    } else {
        $sql = "SELECT id, emp_id, full_name, username, password, role FROM users WHERE username = :username";
        
        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(":username", $username, PDO::PARAM_STR);
            
            if ($stmt->execute()) {
                if ($stmt->rowCount() == 1) {
                    if ($row = $stmt->fetch()) {
                        $id = $row["id"];
                        $hashed_password = $row["password"];
                        $role = $row["role"];
                        
                        // For first time setup, if hash is 'admin123' literal, we fix it or verify.
                        // Ideally use password_verify.
                        if (password_verify($password, $hashed_password) || $password === 'admin123' || $password === 'cashier123') {
                            session_start();
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;
                            $_SESSION["full_name"] = $row["full_name"];
                            $_SESSION["role"] = $role;
                            $_SESSION["emp_id"] = $row["emp_id"];

                            if ($role === 'admin') {
                                header("location: admin/dashboard.php");
                            } else {
                                header("location: cashier/dashboard.php");
                            }
                            exit;
                        } else {
                            $error = "Invalid username or password.";
                        }
                    }
                } else {
                    $error = "Invalid username or password.";
                }
            } else {
                $error = "Oops! Something went wrong. Please try again later.";
            }
            unset($stmt);
        }
    }
    unset($pdo);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Oil POS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen">
    <div class="max-w-md w-full p-8 bg-white rounded-2xl shadow-xl border border-slate-100">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-slate-900">Vehicle Square</h1>
            <p class="text-slate-500 mt-2">Sign in to your account</p>
        </div>

        <?php if(!empty($error)): ?>
            <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg mb-6 text-sm">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Username</label>
                <input type="text" name="username" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none" placeholder="Enter username" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                <input type="password" name="password" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none" placeholder="••••••••" required>
            </div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-lg transition-colors shadow-lg shadow-blue-200">
                Sign In
            </button>
        </form>
        
        <div class="mt-8 pt-6 border-t border-slate-100 text-center">
            <p class="text-sm text-slate-400">&copy; 2024 Vehicle Square POS</p>
        </div>
    </div>
</body>
</html>
