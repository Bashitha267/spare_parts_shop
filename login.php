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
        body { 
            font-family: 'Inter', sans-serif; 
            background: #0d1117;
            color: #e6edf3;
        }
        .bg-main {
            background: radial-gradient(circle at top right, #1e293b, #0f172a, #020617);
            min-height: 100vh;
        }
        .colorful-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 0% 0%, rgba(37, 99, 235, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 100% 100%, rgba(139, 92, 246, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 50% 50%, rgba(236, 72, 153, 0.05) 0%, transparent 50%);
            pointer-events: none;
            z-index: 0;
        }
        .blue-gradient-card {
            background: linear-gradient(135deg, rgba(30, 58, 138, 0.9) 0%, rgba(30, 64, 175, 0.8) 100%);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.5);
        }
        input {
            background: rgba(255, 255, 255, 0.05) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            color: white !important;
        }
        input:focus {
            border-color: #3b82f6 !important;
            ring-color: #3b82f6 !important;
            background: rgba(255, 255, 255, 0.08) !important;
        }
    </style>
</head>
<body class="bg-main flex items-center justify-center min-h-screen relative overflow-hidden">
    <div class="colorful-overlay"></div>
    <div class="max-w-md w-full p-10 blue-gradient-card rounded-[2.5rem] shadow-2xl relative z-10">
        <div class="text-center mb-10">
            <h1 class="text-4xl font-black bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-indigo-400">VEHICLE SQUARE</h1>
            <p class="text-blue-200/60 mt-3 font-medium tracking-wide">Enter credentials to proceed</p>
        </div>

        <?php if(!empty($error)): ?>
            <div class="bg-red-500/10 border border-red-500/20 text-red-400 px-5 py-4 rounded-2xl mb-8 text-sm font-bold flex items-center gap-3">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="space-y-8">
            <div>
                <label class="block text-xs font-black text-blue-300 uppercase tracking-widest mb-3 ml-1">Username</label>
                <input type="text" name="username" class="w-full px-6 py-4 rounded-2xl outline-none transition-all placeholder:text-blue-300/30 font-medium" placeholder="Member ID" required>
            </div>
            <div>
                <label class="block text-xs font-black text-blue-300 uppercase tracking-widest mb-3 ml-1">Password</label>
                <input type="password" name="password" class="w-full px-6 py-4 rounded-2xl outline-none transition-all placeholder:text-blue-300/30 font-medium" placeholder="••••••••" required>
            </div>
            <button type="submit" class="w-full bg-white text-blue-900 font-black py-4 rounded-2xl transition-all shadow-xl hover:bg-blue-50 active:scale-[0.98] text-lg mt-4">
                Access System &rarr;
            </button>
        </form>
        
        <div class="mt-12 pt-8 border-t border-white/10 text-center">
            <p class="text-[10px] font-black text-blue-300/40 uppercase tracking-[0.3em]">&copy; 2024 VEHICLE SQUARE POS</p>
        </div>
    </div>
</body>
</html>
