<?php
require_once 'includes/config.php';

// Redirecionar se não estiver logado
if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$erro = "";
$sucesso = "";

// Verificar se o ID do ponto foi fornecido
if (!isset($_GET['id'])) {
    header("Location: pontos.php");
    exit();
}

$ponto_id = (int)$_GET['id'];

// Obter informações do ponto
try {
    $stmt = $pdo->prepare("
        SELECT p.*, u.nome_completo, c.cidade_nome 
        FROM pontos_ajuda p 
        JOIN usuarios u ON p.usuario_id = u.usuario_id 
        JOIN cidades c ON p.cidade_id = c.cidade_id 
        WHERE p.ponto_id = ?
    ");
    $stmt->execute([$ponto_id]);
    $ponto = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$ponto) {
        header("Location: pontos.php");
        exit();
    }
    
    // Verificar permissões (apenas dono do ponto, admin ou gerente podem editar)
    if ($ponto['usuario_id'] != $usuario_id && !isAdmin() && !isGerente()) {
        header("Location: pontos.php");
        exit();
    }
} catch (PDOException $e) {
    header("Location: pontos.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulo = trim($_POST['titulo']);
    $descricao = trim($_POST['descricao']);
    $endereco = trim($_POST['endereco']);
    $cidade_id = $_POST['cidade_id'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $tipo_ajuda = $_POST['tipo_ajuda'];
    $ativo = isset($_POST['ativo']) ? 1 : 0;
    
    // Validações
    if (empty($titulo) || empty($descricao) || empty($endereco) || empty($cidade_id)) {
        $erro = "Todos os campos obrigatórios devem ser preenchidos.";
    } elseif (!is_numeric($latitude) || !is_numeric($longitude)) {
        $erro = "Coordenadas geográficas inválidas.";
    } else {
        // Verificar se endereço já existe (excluindo o ponto atual)
        if (enderecoExiste($pdo, $endereco, $cidade_id, $ponto_id)) {
            $erro = "Já existe um ponto de ajuda cadastrado neste endereço.";
        } else {
            try {
                $stmt = $pdo->prepare("UPDATE pontos_ajuda SET titulo = ?, descricao = ?, endereco = ?, cidade_id = ?, latitude = ?, longitude = ?, tipo_ajuda = ?, ativo = ?, dt_atualizacao = NOW() WHERE ponto_id = ?");
                $stmt->execute([$titulo, $descricao, $endereco, $cidade_id, $latitude, $longitude, $tipo_ajuda, $ativo, $ponto_id]);
                
                $sucesso = "Ponto de ajuda atualizado com sucesso!";
                
                // Atualizar dados do ponto
                $stmt = $pdo->prepare("SELECT p.*, u.nome_completo, c.cidade_nome FROM pontos_ajuda p JOIN usuarios u ON p.usuario_id = u.usuario_id JOIN cidades c ON p.cidade_id = c.cidade_id WHERE p.ponto_id = ?");
                $stmt->execute([$ponto_id]);
                $ponto = $stmt->fetch(PDO::FETCH_ASSOC);
                
            } catch (PDOException $e) {
                $erro = "Erro ao atualizar ponto: " . $e->getMessage();
            }
        }
    }
}

$pagina_titulo = "Editar Ponto de Ajuda";
require_once 'includes/header.php';
?>

<div class="form-container">
    <div class="card">
        <h2><i class="fas fa-edit"></i> Editar Ponto de Ajuda</h2>
        
        <?php if ($erro): ?>
            <div class="alert alert-error"><?php echo $erro; ?></div>
        <?php endif; ?>
        
        <?php if ($sucesso): ?>
            <div class="alert alert-success"><?php echo $sucesso; ?></div>
        <?php endif; ?>
        
        <form method="post" action="editar-ponto.php?id=<?php echo $ponto_id; ?>" class="form">
            <div class="form-group">
                <label for="titulo">Título do Ponto *:</label>
                <input type="text" id="titulo" name="titulo" required maxlength="125" value="<?php echo htmlspecialchars($ponto['titulo']); ?>" placeholder="Ex: Doação de Alimentos">
            </div>
            
            <div class="form-group">
                <label for="descricao">Descrição *:</label>
                <textarea id="descricao" name="descricao" required rows="4" placeholder="Descreva o tipo de ajuda oferecida, horários de funcionamento, etc."><?php echo htmlspecialchars($ponto['descricao']); ?></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="tipo_ajuda">Tipo de Ajuda *:</label>
                    <select id="tipo_ajuda" name="tipo_ajuda" required>
                        <option value="alimento" <?php echo $ponto['tipo_ajuda'] == 'alimento' ? 'selected' : ''; ?>>Alimento</option>
                        <option value="roupa" <?php echo $ponto['tipo_ajuda'] == 'roupa' ? 'selected' : ''; ?>>Roupa</option>
                        <option value="medicamento" <?php echo $ponto['tipo_ajuda'] == 'medicamento' ? 'selected' : ''; ?>>Medicamento</option>
                        <option value="abrigo" <?php echo $ponto['tipo_ajuda'] == 'abrigo' ? 'selected' : ''; ?>>Abrigo</option>
                        <option value="outros" <?php echo $ponto['tipo_ajuda'] == 'outros' ? 'selected' : ''; ?>>Outros</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="cidade_id">Cidade *:</label>
                    <select id="cidade_id" name="cidade_id" required>
                        <?php
                        try {
                            $stmt = $pdo->query("SELECT cidade_id, cidade_nome FROM cidades ORDER BY cidade_nome");
                            while ($cidade = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                $selected = $cidade['cidade_id'] == $ponto['cidade_id'] ? 'selected' : '';
                                echo '<option value="' . $cidade['cidade_id'] . '" ' . $selected . '>' . htmlspecialchars($cidade['cidade_nome']) . '</option>';
                            }
                        } catch (PDOException $e) {
                            echo '<option value="">Erro ao carregar cidades</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label for="endereco">Endereço Completo *:</label>
                <input type="text" id="endereco" name="endereco" required value="<?php echo htmlspecialchars($ponto['endereco']); ?>" placeholder="Ex: Rua das Flores, 123 - Centro">
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="latitude">Latitude *:</label>
                    <input type="number" id="latitude" name="latitude" step="any" required value="<?php echo htmlspecialchars($ponto['latitude']); ?>" placeholder="Ex: -23.18015230">
                </div>
                
                <div class="form-group">
                    <label for="longitude">Longitude *:</label>
                    <input type="number" id="longitude" name="longitude" step="any" required value="<?php echo htmlspecialchars($ponto['longitude']); ?>" placeholder="Ex: -45.85841420">
                </div>
            </div>
            
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="ativo" value="1" <?php echo $ponto['ativo'] ? 'checked' : ''; ?>>
                    <span class="checkmark"></span>
                    Ponto ativo
                </label>
            </div>
            
            <div class="form-info">
                <p><strong>Criado por:</strong> <?php echo htmlspecialchars($ponto['nome_completo']); ?></p>
                <p><strong>Data de criação:</strong> <?php echo date('d/m/Y H:i', strtotime($ponto['dt_criacao'])); ?></p>
                <?php if ($ponto['dt_atualizacao']): ?>
                    <p><strong>Última atualização:</strong> <?php echo date('d/m/Y H:i', strtotime($ponto['dt_atualizacao'])); ?></p>
                <?php endif; ?>
            </div>
            
            <div class="form-actions">
                <button type="button" id="btnLocalizar" class="btn btn-secondary"><i class="fas fa-map-marker-alt"></i> Usar Minha Localização</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar Alterações</button>
                <a href="pontos.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancelar</a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnLocalizar = document.getElementById('btnLocalizar');
    const latitudeInput = document.getElementById('latitude');
    const longitudeInput = document.getElementById('longitude');
    const enderecoInput = document.getElementById('endereco');
    
    btnLocalizar.addEventListener('click', function() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    latitudeInput.value = position.coords.latitude.toFixed(8);
                    longitudeInput.value = position.coords.longitude.toFixed(8);
                    
                    // Tentar obter endereço aproximado
                    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${position.coords.latitude}&lon=${position.coords.longitude}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.display_name) {
                                enderecoInput.value = data.display_name;
                            }
                        })
                        .catch(error => {
                            console.error('Erro ao obter endereço:', error);
                        });
                },
                function(error) {
                    alert('Não foi possível obter sua localização. Verifique as permissões do navegador.');
                    console.error('Erro de geolocalização:', error);
                }
            );
        } else {
            alert('Geolocalização não é suportada pelo seu navegador.');
        }
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>