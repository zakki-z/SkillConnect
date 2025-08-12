<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign In</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex justify-content-center align-items-center vh-100">
<div class="card p-4 shadow" style="width: 400px;">
    <h2 class="text-center mb-4">Sign In</h2>
    <form method="POST" action="/SkillConnect/index.php/submit-signin">
        <div class="mb-3">
            <label for="name" class="form-label">Full Name</label>
            <input type="text" class="form-control" name="name" id="name" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email address</label>
            <input type="email" class="form-control" name="email" id="email" required>
        </div>
        <div class="mb-3">
            <label for="role" class="form-label">Register as</label>
            <select class="form-select" name="role" id="role" required>
                <option value="freelancer">Freelancer</option>
                <option value="client">Client</option>
            </select>
        </div>

        <div class="mb-4">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" name="password" id="password" required>
        </div>
        <button type="submit" class="btn btn-success w-100">Create Account</button>
    </form>
    <p class="text-center mt-3">Already have an account? <a href="/SkillConnect/index.php/login">Login</a></p>
</div>
</body>
</html>
