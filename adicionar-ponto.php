<?php
// Assumindo que 'includes/config.php' contém a conexão $pdo, isLoggedIn() e enderecoExiste()
require_once 'includes/config.php';

// Redirecionar se não estiver logado
if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

// Inicialização de variáveis de sessão
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$erro = "";
$sucesso = "";
$mostrar_modal_aprovacao = false; // Nova variável para controlar o modal

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and trim inputs
    $titulo = trim($_POST['titulo'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $endereco = trim($_POST['endereco'] ?? '');
    // Garante que cidade_id é um inteiro
    $cidade_id = filter_var($_POST['cidade_id'] ?? null, FILTER_VALIDATE_INT);
    // Garante que latitude e longitude são floats
    $latitude = filter_var($_POST['latitude'] ?? null, FILTER_VALIDATE_FLOAT);
    $longitude = filter_var($_POST['longitude'] ?? null, FILTER_VALIDATE_FLOAT);
    $tipo_ajuda = trim($_POST['tipo_ajuda'] ?? '');
    
    // Validações
    if (empty($titulo) || empty($descricao) || empty($endereco) || $cidade_id === false || empty($tipo_ajuda)) {
        $erro = "Todos os campos obrigatórios devem ser preenchidos e a cidade deve ser selecionada.";
    } elseif ($latitude === false || $longitude === false) {
        $erro = "Coordenadas geográficas inválidas ou não encontradas. Por favor, use o botão 'Buscar coordenadas' ou 'Usar Minha Localização'.";
    } else {
        // Verificar se endereço já existe (função deve estar em config.php)
        if (function_exists('enderecoExiste') && enderecoExiste($pdo, $endereco, $cidade_id)) {
            $erro = "Já existe um ponto de ajuda cadastrado neste endereço.";
        } else {
            try {
                // Preparar e executar a inserção no banco de dados - AGORA COMO INATIVO (0)
                $stmt = $pdo->prepare("INSERT INTO pontos_ajuda (titulo, descricao, endereco, cidade_id, latitude, longitude, tipo_ajuda, usuario_id, ativo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0)");
                $stmt->execute([$titulo, $descricao, $endereco, $cidade_id, $latitude, $longitude, $tipo_ajuda, $usuario_id]);
                
                $sucesso = "Ponto de ajuda cadastrado com sucesso! Aguarde a aprovação do administrador.";
                $mostrar_modal_aprovacao = true; // Ativa o modal
                
                // Limpar formulário após sucesso para evitar reenvio
                $_POST = array();
            } catch (PDOException $e) {
                if ($e->getCode() == '42S02') {
                     $erro = "ERRO CRÍTICO: Tabela 'pontos' não encontrada no banco de dados. Crie a tabela e tente novamente.";
                } else {
                     $erro = "Erro ao cadastrar ponto: " . $e->getMessage();
                }
            }
        }
    }
}

$pagina_titulo = "Adicionar Ponto de Ajuda";
require_once 'includes/header.php';
?>

<div class="form-container">
    <div class="card">
        <h2><i class="fas fa-plus"></i> Adicionar Novo Ponto de Ajuda</h2>
        
        <?php if ($erro): ?>
            <div class="alert alert-error"><?php echo $erro; ?></div>
        <?php endif; ?>
        
        <?php if ($sucesso): ?>
            <div class="alert alert-success"><?php echo $sucesso; ?></div>
        <?php endif; ?>
        
        <form method="post" action="adicionar-ponto.php" id="pontoForm" class="form">
            <div class="form-group">
                <label for="titulo">Título do Ponto *:</label>
                <input type="text" id="titulo" name="titulo" required maxlength="125" value="<?php echo htmlspecialchars($_POST['titulo'] ?? ''); ?>" placeholder="Ex: Doação de Alimentos">
            </div>
            
            <div class="form-group">
                <label for="descricao">Descrição *:</label>
                <textarea id="descricao" name="descricao" required rows="4" placeholder="Descreva o tipo de ajuda oferecida, horários de funcionamento, etc."><?php echo htmlspecialchars($_POST['descricao'] ?? ''); ?></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="tipo_ajuda">Tipo de Ajuda *:</label>
                    <select id="tipo_ajuda" name="tipo_ajuda" required>
                        <option value="">Selecione o tipo</option>
                        <option value="alimento" <?php echo (($_POST['tipo_ajuda'] ?? '') == 'alimento') ? 'selected' : ''; ?>>Alimento</option>
                        <option value="roupa" <?php echo (($_POST['tipo_ajuda'] ?? '') == 'roupa') ? 'selected' : ''; ?>>Roupa</option>
                        <option value="medicamento" <?php echo (($_POST['tipo_ajuda'] ?? '') == 'medicamento') ? 'selected' : ''; ?>>Medicamento</option>
                        <option value="abrigo" <?php echo (($_POST['tipo_ajuda'] ?? '') == 'abrigo') ? 'selected' : ''; ?>>Abrigo</option>
                        <option value="outros" <?php echo (($_POST['tipo_ajuda'] ?? '') == 'outros') ? 'selected' : ''; ?>>Outros</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="cidade_id">Cidade *:</label>
                    <select id="cidade_id" name="cidade_id" required>
                        <option value="">Selecione uma cidade</option>
                        <?php
                        try {
                            $stmt = $pdo->query("SELECT cidade_id, cidade_nome FROM cidades ORDER BY cidade_nome");
                            while ($cidade = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                $selected = (($_POST['cidade_id'] ?? '') == $cidade['cidade_id']) ? 'selected' : '';
                                // Adicionamos 'data-nome' para uso no JavaScript
                                echo '<option value="' . htmlspecialchars($cidade['cidade_id']) . '" ' . $selected . ' data-nome="' . htmlspecialchars($cidade['cidade_nome']) . '">' . htmlspecialchars($cidade['cidade_nome']) . '</option>';
                            }
                        } catch (PDOException $e) {
                            echo '<option value="">Erro ao carregar cidades</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label for="endereco">Endereço Completo (Rua, Número, Bairro) *:</label>
                <input type="text" id="endereco" name="endereco" required value="<?php echo htmlspecialchars($_POST['endereco'] ?? ''); ?>" placeholder="Ex: Rua das Flores, 123 - Centro">
                <button type="button" id="btnBuscarCoordenadas" class="btn btn-small btn-secondary" style="margin-top: 5px;">
                    <i class="fas fa-search"></i> Buscar coordenadas
                </button>
                <small id="coordenadasStatus" style="display: block; margin-top: 5px;"></small>
                
                <div id="enderecoEncontrado" style="display: none; margin-top: 10px; padding: 10px; background-color: #e0f7fa; border-radius: 5px; border-left: 4px solid #00bcd4; color: #006064; font-size: 0.9em;">
                    <strong><i class="fas fa-info-circle"></i> Sugestão de Endereço Encontrado (Nominatim):</strong>
                    <p style="margin-top: 5px; margin-bottom: 5px;">O endereço encontrado é: <strong id="enderecoCompleto"></strong></p>
                    <button type="button" id="usarEnderecoEncontrado" class="btn btn-small btn-primary" style="margin-top: 5px; background-color: #198754; border-color: #198754;"><i class="fas fa-check"></i> Usar Endereço Sugerido</button>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="latitude">Latitude *:</label>
                    <input type="number" id="latitude" name="latitude" step="any" required value="<?php echo htmlspecialchars($_POST['latitude'] ?? ''); ?>" placeholder="Ex: -23.18015230">
                </div>
                
                <div class="form-group">
                    <label for="longitude">Longitude *:</label>
                    <input type="number" id="longitude" name="longitude" step="any" required value="<?php echo htmlspecialchars($_POST['longitude'] ?? ''); ?>" placeholder="Ex: -45.85841420">
                </div>
            </div>
            
            <div class="form-actions">
                <button type="button" id="btnLocalizar" class="btn btn-secondary"><i class="fas fa-map-marker-alt"></i> Usar Minha Localização</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Cadastrar Ponto</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal de Aprovação -->
<div id="modalAprovacao" class="modal" style="<?php echo $mostrar_modal_aprovacao ? 'display: flex;' : 'display: none;'; ?>">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-clock"></i> Aguardando Aprovação</h3>
            <button type="button" onclick="fecharModalAprovacao()">&times;</button>
        </div>
        <div class="modal-body">
            <div style="text-align: center; padding: 20px;">
                <div style="font-size: 48px; color: #4361ee; margin-bottom: 15px;">
                    <i class="fas fa-hourglass-half"></i>
                </div>
                <h4 style="color: #2c3e50; margin-bottom: 15px;">Ponto Cadastrado com Sucesso!</h4>
                <p style="color: #6c757d; line-height: 1.6;">
                    Seu ponto de ajuda foi cadastrado e está <strong>aguardando aprovação</strong> da equipe administrativa.
                </p>
                <p style="color: #6c757d; line-height: 1.6;">
                    Você receberá uma notificação quando o ponto for ativado no sistema.
                </p>
                <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-top: 15px;">
                    <p style="margin: 0; color: #495057; font-size: 0.9rem;">
                        <i class="fas fa-info-circle" style="color: #4361ee;"></i>
                        <strong>Tempo estimado:</strong> 24-48 horas
                    </p>
                </div>
            </div>
        </div>
        <div class="modal-footer" style="text-align: center; padding: 15px;">
            <button onclick="fecharModalAprovacao()" class="btn btn-primary">Entendido</button>
        </div>
    </div>
</div>

<style>
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
    justify-content: center;
    align-items: center;
}

.modal-content {
    background: white;
    border-radius: 12px;
    width: 90%;
    max-width: 500px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    animation: slideIn 0.3s ease-out;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    border-bottom: 1px solid #e9ecef;
}

.modal-header h3 {
    margin: 0;
    color: #2c3e50;
    display: flex;
    align-items: center;
    gap: 10px;
}

.modal-header button {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: #6c757d;
}

.modal-body {
    padding: 20px;
}

.modal-footer {
    padding: 15px 20px;
    border-top: 1px solid #e9ecef;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-50px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // API ALTERNATIVA: Nominatim (OpenStreetMap) - Não requer chave API
    const NOMINATIM_GEOCODING_URL = 'https://nominatim.openstreetmap.org/search';
    const NOMINATIM_REVERSE_URL = 'https://nominatim.openstreetmap.org/reverse';
    // O Nominatim exige um cabeçalho User-Agent para respeitar a política de uso.
    // Lembre-se de EDITAR AQUI com seu email REAL!
    const USER_AGENT = 'PontoDeAjudaApp/1.0 (seuemail@exemplo.com)'; 

    // VARIÁVEIS DE COOLDOWN
    const COOLDOWN_TIME = 5000; // 5 segundos em milissegundos
    let lastSearchTime = 0; // Armazena o timestamp da última busca

    const btnLocalizar = document.getElementById('btnLocalizar');
    const btnBuscarCoordenadas = document.getElementById('btnBuscarCoordenadas');
    const latitudeInput = document.getElementById('latitude');
    const longitudeInput = document.getElementById('longitude');
    const enderecoInput = document.getElementById('endereco');
    const coordenadasStatus = document.getElementById('coordenadasStatus');
    const cidadeSelect = document.getElementById('cidade_id');
    const enderecoEncontradoDiv = document.getElementById('enderecoEncontrado');
    const enderecoCompletoSpan = document.getElementById('enderecoCompleto');
    const usarEnderecoEncontradoBtn = document.getElementById('usarEnderecoEncontrado');
    
    let enderecoCompletoEncontrado = '';
    
    // Função para mostrar a caixa de sugestão
    function mostrarEnderecoEncontrado(enderecoSugerido, enderecoOriginal) {
        enderecoCompletoEncontrado = enderecoSugerido;
        enderecoCompletoSpan.textContent = enderecoSugerido;
        
        const usuarioDigitou = enderecoOriginal.toLowerCase().trim();
        const encontradoLimpo = enderecoSugerido.toLowerCase().trim();
        
        if (encontradoLimpo.length > usuarioDigitou.length + 10 || !encontradoLimpo.includes(usuarioDigitou.split(',')[0].trim().toLowerCase())) {
            enderecoEncontradoDiv.style.display = 'block';
        } else {
            enderecoInput.value = enderecoSugerido;
            esconderEnderecoEncontrado();
        }
    }
    
    // Função para esconder a caixa de sugestão
    function esconderEnderecoEncontrado() {
        enderecoEncontradoDiv.style.display = 'none';
        enderecoCompletoEncontrado = '';
    }
    
    // Botão para usar o endereço encontrado (Mantido)
    usarEnderecoEncontradoBtn.addEventListener('click', function() {
        if (enderecoCompletoEncontrado) {
            let partes = enderecoCompletoEncontrado.split(', ');
            // Remove o final que costuma ser cidade, estado, país etc.
            partes.splice(partes.length - 4); 
            enderecoInput.value = partes.join(', ').trim();
            
            esconderEnderecoEncontrado();
            coordenadasStatus.textContent = 'Endereço atualizado com a sugestão e coordenadas prontas para salvar!';
            coordenadasStatus.style.color = 'green';
        }
    });

    // ====================================================================
    // FUNÇÃO PRINCIPAL: BUSCAR COORDENADAS (NOMINATIM) - COM COOLDOWN
    // ====================================================================
    async function buscarCoordenadas(e) {
        // Evita que o evento de blur acione o cooldown se o botão foi clicado
        if (e && e.type === 'click') {
            const now = Date.now();
            const timeSinceLastSearch = now - lastSearchTime;
            
            // Verifica o cooldown
            if (timeSinceLastSearch < COOLDOWN_TIME) {
                const timeLeft = Math.ceil((COOLDOWN_TIME - timeSinceLastSearch) / 1000);
                
                // Mostra o contador de tempo
                coordenadasStatus.innerHTML = `⏳ Por favor, aguarde **${timeLeft} segundos** para buscar novamente.`;
                coordenadasStatus.style.color = 'orange';
                return; // Impede a execução da busca
            }
        }
        
        const endereco = enderecoInput.value.trim();
        const cidadeOption = cidadeSelect.options[cidadeSelect.selectedIndex];
        const cidadeTexto = cidadeOption.getAttribute('data-nome') || cidadeOption.text;
        
        if (!endereco || !cidadeSelect.value) {
            coordenadasStatus.textContent = 'Por favor, digite o endereço e selecione a cidade.';
            coordenadasStatus.style.color = 'red';
            esconderEnderecoEncontrado();
            return false;
        }
        
        coordenadasStatus.textContent = 'Buscando coordenadas via Nominatim (OpenStreetMap)...';
        coordenadasStatus.style.color = 'blue';
        esconderEnderecoEncontrado();
        
        try {
            const enderecoCompleto = `${endereco}, ${cidadeTexto}, Brasil`;
            
            const params = new URLSearchParams({
                q: enderecoCompleto,
                format: 'json',
                addressdetails: 1, 
                limit: 1, 
                'accept-language': 'pt-BR',
                countrycodes: 'br' 
            });
            
            const url = `${NOMINATIM_GEOCODING_URL}?${params.toString()}`;
            
            const response = await fetch(url, { headers: { 'User-Agent': USER_AGENT } });

            if (!response.ok) {
                 throw new Error(`Erro HTTP: ${response.status}`);
            }

            const data = await response.json();
            
            if (data.length > 0) {
                const resultado = data[0];
                
                latitudeInput.value = parseFloat(resultado.lat).toFixed(8);
                longitudeInput.value = parseFloat(resultado.lon).toFixed(8);
                
                const enderecoSugerido = resultado.display_name;
                
                mostrarEnderecoEncontrado(enderecoSugerido, endereco);
                
                if (enderecoEncontradoDiv.style.display === 'block') {
                    coordenadasStatus.textContent = 'Coordenadas encontradas! Verifique o endereço sugerido e clique em "Usar".';
                } else {
                    coordenadasStatus.textContent = 'Coordenadas e endereço confirmados com sucesso! Pronto para salvar.';
                }
                coordenadasStatus.style.color = 'green';
                
                lastSearchTime = Date.now(); // ATUALIZA O TIMESTAMP DE SUCESSO
                return true;
            } else {
                coordenadasStatus.textContent = 'Endereço não encontrado. Tente refinar a busca (Ex: Rua A, 100).';
                coordenadasStatus.style.color = 'orange';
                latitudeInput.value = '';
                longitudeInput.value = '';
                return false;
            }
        } catch (error) {
            console.error('Erro ao buscar coordenadas:', error);
            coordenadasStatus.innerHTML = `⚠️ **API BLOQUEDA, VOLTE MAIS TARDE**. ou use "Usar Minha Localização".`;
            coordenadasStatus.style.color = 'red';
            return false;
        }
    }
    
    // Event Listeners
    // Adiciona o evento do botão principal, passando o objeto evento
    btnBuscarCoordenadas.addEventListener('click', buscarCoordenadas); 
    
    // Busca automática ao preencher endereço ou mudar cidade (sem cooldown estrito)
    const autoSearch = function() {
        if (enderecoInput.value.trim() && cidadeSelect.value && (!latitudeInput.value || !longitudeInput.value)) {
            // A busca automática é mais permissiva, mas o Nominatim pode bloquear.
            // Aqui mantemos um pequeno delay para evitar requisições desnecessárias.
            setTimeout(buscarCoordenadas, 500); 
        }
    };

    enderecoInput.addEventListener('blur', autoSearch);
    cidadeSelect.addEventListener('change', autoSearch);

    // ====================================================================
    // FUNÇÃO PRINCIPAL: USAR MINHA LOCALIZAÇÃO - COM COOLDOWN
    // ====================================================================
    btnLocalizar.addEventListener('click', function(e) {
        const now = Date.now();
        const timeSinceLastSearch = now - lastSearchTime;
        
        // Verifica o cooldown
        if (timeSinceLastSearch < COOLDOWN_TIME) {
            const timeLeft = Math.ceil((COOLDOWN_TIME - timeSinceLastSearch) / 1000);
            coordenadasStatus.innerHTML = `⏳ Por favor, aguarde **${timeLeft} segundos** para buscar novamente.`;
            coordenadasStatus.style.color = 'orange';
            return; 
        }

        // Se passar no cooldown, prossegue com a busca de localização
        if (navigator.geolocation) {
            coordenadasStatus.textContent = 'Obtendo localização...';
            coordenadasStatus.style.color = 'blue';
            esconderEnderecoEncontrado();
            
            navigator.geolocation.getCurrentPosition(
                async function(position) {
                    // ... (Lógica de obtenção de LAT/LNG e Reverse Geocoding)
                    
                    // Adicione a atualização do lastSearchTime após o sucesso
                    lastSearchTime = Date.now(); // ATUALIZA O TIMESTAMP DE SUCESSO
                    
                    // (O restante do código de Reverse Geocoding do Nominatim permanece o mesmo)
                    // ...
                    
                    // CÓDIGO DO REVERSE GEOCODING (MANTIDO DO ANTERIOR)
                    const lat = position.coords.latitude.toFixed(8);
                    const lng = position.coords.longitude.toFixed(8);
                    
                    latitudeInput.value = lat;
                    longitudeInput.value = lng;
                    
                    coordenadasStatus.textContent = 'Localização obtida. Buscando endereço aproximado...';
                    
                    try {
                        const params = new URLSearchParams({
                            lat: lat,
                            lon: lng,
                            format: 'json',
                            addressdetails: 1, 
                            'accept-language': 'pt-BR'
                        });
                        
                        const url = `${NOMINATIM_REVERSE_URL}?${params.toString()}`;
                        
                        const response = await fetch(url, { headers: { 'User-Agent': USER_AGENT } });
                        const data = await response.json();
                        
                        if (data && data.display_name) {
                            enderecoInput.value = data.display_name;
                            coordenadasStatus.textContent = 'Localização obtida e endereço preenchido com sucesso!';
                            coordenadasStatus.style.color = 'green';
                            
                            const address = data.address;
                            let cityFound = address.city || address.town || address.village || address.suburb;

                            if (cityFound) {
                                for (let i = 0; i < cidadeSelect.options.length; i++) {
                                    const optionText = cidadeSelect.options[i].getAttribute('data-nome') || cidadeSelect.options[i].text;
                                    if (optionText.toLowerCase().includes(cityFound.toLowerCase())) {
                                        cidadeSelect.value = cidadeSelect.options[i].value;
                                        break;
                                    }
                                }
                            }
                        } else {
                            coordenadasStatus.textContent = 'Localização obtida, mas insira o endereço completo e selecione a cidade manualmente.';
                            coordenadasStatus.style.color = 'orange';
                        }
                    } catch (error) {
                        console.error('Erro ao obter endereço a partir da localização:', error);
                        coordenadasStatus.textContent = 'Localização obtida, mas insira o endereço completo manualmente.';
                        coordenadasStatus.style.color = 'orange';
                    }
                },
                function(error) {
                    // Lidar com erros de geolocalização do navegador
                    coordenadasStatus.textContent = 'Permissão negada ou erro ao obter localização.';
                    coordenadasStatus.style.color = 'red';
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 60000 }
            );
        } else {
            coordenadasStatus.textContent = 'Geolocalização não é suportada pelo seu navegador.';
            coordenadasStatus.style.color = 'red';
        }
    });
    
    // Validação antes do envio (mantida)
    document.getElementById('pontoForm').addEventListener('submit', function(e) {
        const latitude = document.getElementById('latitude').value.trim();
        const longitude = document.getElementById('longitude').value.trim();
        
        if (!latitude || !longitude) {
            e.preventDefault();
            alert('Atenção: Latitude e Longitude são obrigatórias. Por favor, use "Buscar coordenadas" ou "Usar Minha Localização" antes de cadastrar.');
            return;
        }
        
        if (!confirm('Deseja confirmar o cadastro deste ponto de ajuda?')) {
            e.preventDefault();
        }
    });
    
    if (enderecoInput.value && cidadeSelect.value && !latitudeInput.value && !longitudeInput.value) {
        setTimeout(autoSearch, 500);
    }
});

// Função para fechar o modal de aprovação
function fecharModalAprovacao() {
    document.getElementById('modalAprovacao').style.display = 'none';
}

// Fechar modal ao clicar fora dele
window.onclick = function(event) {
    const modal = document.getElementById('modalAprovacao');
    if (event.target === modal) {
        fecharModalAprovacao();
    }
}
</script>

<?php require_once 'includes/footer.php'; ?>