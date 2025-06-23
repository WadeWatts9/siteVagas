<?php
require_once 'config/database.php';

echo "Limpando categorias de teste...<br><br>";

try {
    // Remover categorias que começam com "Categoria Teste"
    $stmt = $conn->prepare("DELETE FROM categories WHERE name LIKE 'Categoria Teste%'");
    $deleted = $stmt->execute();
    $count = $stmt->rowCount();
    
    if ($count > 0) {
        echo "✅ $count categoria(s) de teste removida(s) com sucesso!<br>";
    } else {
        echo "ℹ️ Nenhuma categoria de teste encontrada para remover.<br>";
    }
    
    // Listar categorias restantes
    $stmt = $conn->query("SELECT * FROM categories ORDER BY name");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Categorias restantes (" . count($categories) . "):</h3>";
    if (count($categories) > 0) {
        echo "<ul>";
        foreach ($categories as $category) {
            echo "<li>ID: {$category['id']} - {$category['name']}</li>";
        }
        echo "</ul>";
    } else {
        echo "Nenhuma categoria encontrada.<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "<br>";
}

echo "<br><a href='index.php'>Voltar para página inicial</a>";
?> 