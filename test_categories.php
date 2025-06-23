<?php
require_once 'config/database.php';

echo "<h2>Teste de Categorias</h2>";

try {
    // Verificar se a tabela existe
    $stmt = $conn->query("SELECT name FROM sqlite_master WHERE type='table' AND name='categories'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Tabela 'categories' existe<br>";
    } else {
        echo "❌ Tabela 'categories' não existe<br>";
    }

    // Listar todas as categorias
    $stmt = $conn->query("SELECT * FROM categories ORDER BY name");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Categorias encontradas: " . count($categories) . "</h3>";
    
    if (count($categories) > 0) {
        echo "<ul>";
        foreach ($categories as $category) {
            echo "<li>ID: {$category['id']} - Nome: {$category['name']} - Criado em: {$category['created_at']}</li>";
        }
        echo "</ul>";
    } else {
        echo "Nenhuma categoria encontrada.<br>";
    }

    // Testar inserção de uma categoria
    echo "<h3>Testando inserção...</h3>";
    $test_name = "Categoria Teste " . date('H:i:s');
    $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
    if ($stmt->execute([$test_name])) {
        echo "✅ Categoria '$test_name' inserida com sucesso<br>";
        
        // Verificar se foi inserida
        $stmt = $conn->query("SELECT COUNT(*) FROM categories");
        $count = $stmt->fetchColumn();
        echo "Total de categorias agora: $count<br>";
    } else {
        echo "❌ Erro ao inserir categoria<br>";
    }

} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "<br>";
}

echo "<br><a href='admin/categories.php'>Ir para Gerenciar Categorias</a>";
?> 