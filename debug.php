<?php
echo "Debug do Sistema<br><br>";

// Verificar se o arquivo de configuração existe
if (file_exists('config/database.php')) {
    echo "✅ Arquivo config/database.php existe<br>";
    
    try {
        require_once 'config/database.php';
        echo "✅ Arquivo de configuração carregado<br>";
        
        // Testar conexão
        $stmt = $conn->query("SELECT 1");
        echo "✅ Conexão com banco funcionando<br>";
        
        // Verificar tabelas
        $stmt = $conn->query("SELECT name FROM sqlite_master WHERE type='table'");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "📊 Tabelas encontradas: " . implode(', ', $tables) . "<br>";
        
        // Verificar categorias
        if (in_array('categories', $tables)) {
            $stmt = $conn->query("SELECT COUNT(*) FROM categories");
            $count = $stmt->fetchColumn();
            echo "📂 Total de categorias: $count<br>";
            
            if ($count > 0) {
                $stmt = $conn->query("SELECT * FROM categories LIMIT 5");
                $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo "📋 Primeiras categorias:<br>";
                foreach ($categories as $cat) {
                    echo "- ID: {$cat['id']}, Nome: {$cat['name']}<br>";
                }
            }
        }
        
        // Verificar usuários
        if (in_array('users', $tables)) {
            $stmt = $conn->query("SELECT COUNT(*) FROM users");
            $count = $stmt->fetchColumn();
            echo "👥 Total de usuários: $count<br>";
            
            $stmt = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'admin'");
            $admin_count = $stmt->fetchColumn();
            echo "👑 Administradores: $admin_count<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ Erro: " . $e->getMessage() . "<br>";
    }
} else {
    echo "❌ Arquivo config/database.php não encontrado<br>";
}

echo "<br><br>";
echo "<a href='index.php'>Ir para página principal</a><br>";
echo "<a href='login.php'>Ir para login</a><br>";
echo "<a href='admin/categories.php'>Ir para categorias (admin)</a><br>";
?> 