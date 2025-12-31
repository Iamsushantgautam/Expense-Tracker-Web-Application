<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register - Expense Tracker</title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap');

* { box-sizing: border-box; }

body {
    margin:0;
    height:100vh;
    display:flex;
    align-items:flex-start; /* for mobile keyboards */
    justify-content:center;
    font-family:'Inter', sans-serif;
    background: linear-gradient(135deg,#4a90e2,#50e3c2);
    overflow-x:hidden;
    padding-top:50px;
    position:relative;
}

/* Centered Card */
.card {
    background:#fff;
    padding:50px 35px;
    border-radius:15px;
    width:100%;
    max-width:400px;
    text-align:center;
    box-shadow:0 20px 50px rgba(0,0,0,0.15);
    animation: fadeIn 1s ease;
    position:relative;
    z-index:1;
}

/* Fade-in */
@keyframes fadeIn {
    0% { opacity:0; transform: translateY(-30px);}
    100% { opacity:1; transform: translateY(0);}
}

.card h2 {
    margin-bottom:25px;
    font-weight:600;
    color:#111;
}

/* Inputs */
input {
    width:100%;
    padding:14px 18px;
    margin:12px 0;
    border:1px solid #e2e8f0;
    border-radius:8px;
    font-size:16px;
    transition:0.3s;
}
input:focus {
    border-color:#4a90e2;
    box-shadow:0 0 10px rgba(74,144,226,0.3);
    outline:none;
}

/* Button */
button {
    width:100%;
    padding:14px;
    margin-top:15px;
    border:none;
    border-radius:8px;
    background:#4a90e2;
    color:#fff;
    font-size:16px;
    font-weight:600;
    cursor:pointer;
    transition:0.3s;
}
button:hover {
    background:#357ab8;
    transform: translateY(-2px);
    box-shadow:0 6px 12px rgba(0,0,0,0.15);
}

/* Error */
.error {
    background:#ffe7e7;
    color:#b42318;
    padding:10px;
    border-radius:6px;
    margin-bottom:10px;
    font-size:14px;
}

/* Link */
.card p {
    margin-top:18px;
    font-size:14px;
}
.card p a {
    color:#4a90e2;
    text-decoration:none;
    font-weight:500;
}
.card p a:hover { text-decoration:underline; }

/* Subtle background circles */
body::before, body::after {
    content:"";
    position:absolute;
    border-radius:50%;
    background: rgba(255,255,255,0.08);
    animation: float 8s infinite ease-in-out alternate;
}
body::before { width:200px; height:200px; top:-80px; left:-60px; }
body::after { width:300px; height:300px; bottom:-120px; right:-100px; animation-delay:4s; }

@keyframes float {
    0% { transform: translateY(0) rotate(0deg);}
    100% { transform: translateY(-25px) rotate(45deg);}
}

/* ----------------- Responsive ----------------- */

/* Mobile < 480px */
@media(max-width:480px){
    .card { padding:35px 25px; max-width:95%; }
    input, button { font-size:15px; padding:12px 15px; }
    h2 { font-size:22px; }
}

/* Tablets 481px - 768px */
@media(min-width:481px) and (max-width:768px){
    .card { padding:40px 30px; max-width:90%; }
    input, button { font-size:15px; padding:13px 16px; }
    h2 { font-size:24px; }
}

/* Large screens > 1200px */
@media(min-width:1200px){
    .card { padding:50px 35px; max-width:400px; }
    input, button { font-size:16px; padding:14px 18px; }
    h2 { font-size:26px; }
}
</style>
</head>
<body>
<div class="card">
    <h2>Create Account</h2>
    <?php if(!empty($error)): ?><div class="error"><?=htmlspecialchars($error)?></div><?php endif; ?>
    <form method="post">
        <input name="name" placeholder="Full Name" required>
        <input name="email" type="email" placeholder="Email" required>
        <input name="password" type="password" placeholder="Password" required>
        <input name="confirm_password" type="password" placeholder="Confirm Password" required>
        <button type="submit">Register</button>
    </form>
    <p>Have an account? <a href="index.php">Login</a></p>
</div>
</body>
</html>