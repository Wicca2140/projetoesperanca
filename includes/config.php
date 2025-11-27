<?php
session_start();

// Configurações do banco de dados
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'projetoesperanca');
define('DB_USER', 'root');
define('DB_PASS', '');

// Conexão com o banco de dados
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("set names utf8");
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}

// Verificar se o usuário está logado
function isLoggedIn() {
    return isset($_SESSION['usuario_id']);
}

// Obter informações do usuário logado
function getUserInfo($pdo, $usuario_id) {
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE usuario_id = ?");
    $stmt->execute([$usuario_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Verificar se usuário é administrador
function isAdmin() {
    return isset($_SESSION['nivel_acesso']) && $_SESSION['nivel_acesso'] == 1;
}

// Verificar se usuário é gerente
function isGerente() {
    return isset($_SESSION['nivel_acesso']) && $_SESSION['nivel_acesso'] == 2;
}

// Função para verificar se endereço já existe
function enderecoExiste($pdo, $endereco, $cidade_id, $ponto_id = null) {
    $sql = "SELECT COUNT(*) FROM pontos_ajuda WHERE endereco = ? AND cidade_id = ?";
    $params = [$endereco, $cidade_id];
    
    if ($ponto_id) {
        $sql .= " AND ponto_id != ?";
        $params[] = $ponto_id;
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchColumn() > 0;
}

// Adicione no config.php
putenv('SMTP_USER=rodrigordg04299@gmail.com');
putenv('SMTP_PASS=nklolsdaumsstgbm');

?>

