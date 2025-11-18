<?php
header("Content-Type: application/json");

$pdo = new PDO("mysql:host=localhost;dbname=ecoleta", "root", "");

// Busca coletores + seus endereços
$sql = $pdo->query("
    SELECT 
        c.id,
        c.nome_completo,
        c.created_at,
        e.rua,
        e.numero,
        e.complemento,
        e.bairro,
        e.cidade,
        e.estado,
        e.cep
    FROM coletores c
    INNER JOIN enderecos e ON e.id = c.id_endereco
");

$coletores = $sql->fetchAll(PDO::FETCH_ASSOC);

// Monta endereço completo
foreach ($coletores as &$c) {
    $endereco = "{$c['rua']} {$c['numero']}, {$c['bairro']}, {$c['cidade']} - {$c['estado']}, {$c['cep']}";
    if (!empty($c['complemento'])) {
        $endereco .= " ({$c['complemento']})";
    }
    $c['endereco_completo'] = $endereco;
}

echo json_encode($coletores);