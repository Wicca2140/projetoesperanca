<?php
$pagina_titulo = isset($pagina_titulo) ? $pagina_titulo : "Projeto Esperança";
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pagina_titulo; ?></title>
    <link rel="stylesheet" href="css/estilo.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Projeto Esperança</h1>
            <nav>
                <ul>
                    <li><a href="index.php"><i class="fas fa-home"></i> Início</a></li>
                    <li><a href="mapa.php"><i class="fas fa-map"></i> Mapa</a></li>
                    <?php if (isLoggedIn()): ?>
                        <li><a href="pontos.php"><i class="fas fa-list"></i> Pontos</a></li>
                        <li><a href="perfil.php"><i class="fas fa-user"></i> Meu Perfil</a></li>
                        <li><a href="adicionar-ponto.php"><i class="fas fa-plus"></i> Adicionar Ponto</a></li>
                        <?php if (isAdmin() || isGerente()): ?>
                            <li><a href="admin.php"><i class="fas fa-cog"></i> Administração</a></li>
                        <?php endif; ?>
                        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a></li>
                    <?php else: ?>
                        <li><a href="login.php"><i class="fas fa-sign-in-alt"></i> Entrar</a></li>
                        <li><a href="registro.php"><i class="fas fa-user-plus"></i> Registrar</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
    <main class="container">