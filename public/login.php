<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Login - Turismo</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="card">
    <h3>Entrar</h3>
    <form method="POST" action="../src/login_action.php">
        <input type="email" name="email" placeholder="E-mail" required>
        <input type="password" name="senha" placeholder="Senha" required>
        <button type="submit">Login</button>
    </form>
</div>

</body>
</html>
