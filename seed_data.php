<?php
require_once 'config/database.php';

echo "Criando dados de exemplo...\n";

try {
    // Criar categorias
    $categories = [
        'Tecnologia',
        'Marketing',
        'Vendas',
        'Recursos Humanos',
        'Financeiro',
        'Design',
        'Engenharia',
        'Educação',
        'Saúde',
        'Administração'
    ];

    echo "Inserindo categorias...\n";
    foreach ($categories as $category) {
        $stmt = $conn->prepare("INSERT OR IGNORE INTO categories (name) VALUES (?)");
        $stmt->execute([$category]);
    }

    // Criar usuários comuns de exemplo
    $users = [
        [
            'name' => 'João Silva',
            'email' => 'joao.silva@email.com',
            'password' => password_hash('123456', PASSWORD_DEFAULT),
            'linkedin_url' => 'https://linkedin.com/in/joao-silva',
            'role' => 'user'
        ],
        [
            'name' => 'Maria Santos',
            'email' => 'maria.santos@email.com',
            'password' => password_hash('123456', PASSWORD_DEFAULT),
            'linkedin_url' => 'https://linkedin.com/in/maria-santos',
            'role' => 'user'
        ],
        [
            'name' => 'Pedro Oliveira',
            'email' => 'pedro.oliveira@email.com',
            'password' => password_hash('123456', PASSWORD_DEFAULT),
            'linkedin_url' => 'https://linkedin.com/in/pedro-oliveira',
            'role' => 'user'
        ],
        [
            'name' => 'Ana Costa',
            'email' => 'ana.costa@email.com',
            'password' => password_hash('123456', PASSWORD_DEFAULT),
            'linkedin_url' => 'https://linkedin.com/in/ana-costa',
            'role' => 'user'
        ],
        [
            'name' => 'Carlos Ferreira',
            'email' => 'carlos.ferreira@email.com',
            'password' => password_hash('123456', PASSWORD_DEFAULT),
            'linkedin_url' => 'https://linkedin.com/in/carlos-ferreira',
            'role' => 'user'
        ]
    ];

    echo "Inserindo usuários comuns...\n";
    foreach ($users as $user) {
        $stmt = $conn->prepare("INSERT OR IGNORE INTO users (name, email, password, linkedin_url, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $user['name'],
            $user['email'],
            $user['password'],
            $user['linkedin_url'],
            $user['role']
        ]);
    }

    // Buscar IDs das categorias
    $stmt = $conn->query("SELECT id, name FROM categories");
    $category_map = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $category_map[$row['name']] = $row['id'];
    }

    // Criar vagas de exemplo
    $jobs = [
        [
            'title' => 'Desenvolvedor Full Stack',
            'description' => 'Buscamos um desenvolvedor experiente em tecnologias web modernas para integrar nossa equipe de desenvolvimento. O profissional será responsável por desenvolver aplicações web completas, desde o front-end até o back-end, utilizando as melhores práticas de desenvolvimento.',
            'requirements' => '• Experiência com PHP, JavaScript, HTML5, CSS3
• Conhecimento em frameworks como Laravel, Vue.js ou React
• Experiência com bancos de dados MySQL/PostgreSQL
• Conhecimento em Git e metodologias ágeis
• Inglês intermediário
• Experiência mínima de 3 anos',
            'company_name' => 'TechSolutions Ltda',
            'location' => 'São Paulo, SP',
            'salary_range' => 'R$ 8.000 - R$ 12.000',
            'contact_email' => 'rh@techsolutions.com.br',
            'contact_phone' => '(11) 99999-1234',
            'category_id' => $category_map['Tecnologia']
        ],
        [
            'title' => 'Analista de Marketing Digital',
            'description' => 'Oportunidade para profissional criativo e analítico para gerenciar campanhas de marketing digital. Responsável por estratégias de SEO, SEM, redes sociais e análise de métricas para otimização de resultados.',
            'requirements' => '• Graduação em Marketing, Publicidade ou áreas afins
• Experiência com Google Ads, Facebook Ads, Instagram Ads
• Conhecimento em Google Analytics e ferramentas de SEO
• Habilidades em criação de conteúdo
• Experiência mínima de 2 anos
• Conhecimento em design gráfico será um diferencial',
            'company_name' => 'Digital Marketing Pro',
            'location' => 'Rio de Janeiro, RJ',
            'salary_range' => 'R$ 5.000 - R$ 8.000',
            'contact_email' => 'vagas@digitalmarketingpro.com.br',
            'contact_phone' => '(21) 98888-5678',
            'category_id' => $category_map['Marketing']
        ],
        [
            'title' => 'Vendedor Externo',
            'description' => 'Vaga para vendedor experiente em vendas B2B. O profissional será responsável por prospectar novos clientes, manter relacionamento com carteira existente e atingir metas de vendas estabelecidas.',
            'requirements' => '• Experiência comprovada em vendas externas
• Carteira de habilitação categoria B
• Facilidade de comunicação e relacionamento
• Orientação para resultados
• Conhecimento do mercado local
• Disponibilidade para viagens',
            'company_name' => 'Comercial Santos & Cia',
            'location' => 'Belo Horizonte, MG',
            'salary_range' => 'R$ 3.000 + comissões',
            'contact_email' => 'vendas@santosecia.com.br',
            'contact_phone' => '(31) 97777-9012',
            'category_id' => $category_map['Vendas']
        ],
        [
            'title' => 'Designer UX/UI',
            'description' => 'Procuramos designer talentoso para criar experiências digitais incríveis. Responsável por pesquisa de usuário, prototipagem, design de interfaces e testes de usabilidade.',
            'requirements' => '• Portfolio sólido em UX/UI Design
• Proficiência em Figma, Sketch ou Adobe XD
• Conhecimento em Design Thinking e metodologias ágeis
• Experiência com prototipagem e testes de usabilidade
• Conhecimento básico em HTML/CSS será um diferencial
• Inglês intermediário',
            'company_name' => 'Creative Studio',
            'location' => 'Porto Alegre, RS',
            'salary_range' => 'R$ 6.000 - R$ 10.000',
            'contact_email' => 'jobs@creativestudio.com.br',
            'contact_phone' => '(51) 96666-3456',
            'category_id' => $category_map['Design']
        ],
        [
            'title' => 'Analista de Recursos Humanos',
            'description' => 'Oportunidade para profissional de RH atuar em processos de recrutamento, seleção, treinamento e desenvolvimento. Ambiente colaborativo e oportunidades de crescimento.',
            'requirements' => '• Graduação em Psicologia, Administração ou RH
• Experiência em recrutamento e seleção
• Conhecimento em legislação trabalhista
• Habilidades de comunicação e relacionamento interpessoal
• Experiência com sistemas de RH
• Inglês básico/intermediário',
            'company_name' => 'Empresa ABC Ltda',
            'location' => 'Curitiba, PR',
            'salary_range' => 'R$ 4.500 - R$ 6.500',
            'contact_email' => 'rh@empresaabc.com.br',
            'contact_phone' => '(41) 95555-7890',
            'category_id' => $category_map['Recursos Humanos']
        ],
        [
            'title' => 'Contador Sênior',
            'description' => 'Vaga para contador experiente para liderar equipe contábil. Responsável por demonstrações financeiras, compliance fiscal e assessoria à diretoria.',
            'requirements' => '• Graduação em Ciências Contábeis com CRC ativo
• Experiência mínima de 5 anos em contabilidade
• Conhecimento avançado em legislação fiscal e tributária
• Experiência com sistemas ERP (SAP, TOTVS)
• Liderança de equipe
• Pós-graduação será um diferencial',
            'company_name' => 'Contabilidade Moderna',
            'location' => 'Brasília, DF',
            'salary_range' => 'R$ 8.000 - R$ 12.000',
            'contact_email' => 'vagas@contabilidademoderna.com.br',
            'contact_phone' => '(61) 94444-2345',
            'category_id' => $category_map['Financeiro']
        ],
        [
            'title' => 'Engenheiro de Software',
            'description' => 'Oportunidade para engenheiro de software trabalhar em projetos inovadores. Desenvolvimento de sistemas escaláveis, arquitetura de software e mentoria técnica.',
            'requirements' => '• Graduação em Engenharia de Software, Ciência da Computação ou similar
• Experiência com arquitetura de software e design patterns
• Conhecimento em cloud computing (AWS, Azure)
• Experiência com metodologias ágeis
• Inglês avançado
• Experiência mínima de 4 anos',
            'company_name' => 'Innovation Tech',
            'location' => 'São Paulo, SP',
            'salary_range' => 'R$ 12.000 - R$ 18.000',
            'contact_email' => 'careers@innovationtech.com.br',
            'contact_phone' => '(11) 93333-6789',
            'category_id' => $category_map['Engenharia']
        ],
        [
            'title' => 'Professor de Inglês',
            'description' => 'Escola de idiomas busca professor de inglês dinâmico e experiente. Aulas para diferentes níveis e idades, ambiente estimulante e equipe colaborativa.',
            'requirements' => '• Graduação em Letras - Inglês ou áreas afins
• Certificação internacional (TOEFL, IELTS, Cambridge)
• Experiência em ensino de inglês
• Metodologias ativas de ensino
• Disponibilidade para horários variados
• Paixão pelo ensino',
            'company_name' => 'English Academy',
            'location' => 'Florianópolis, SC',
            'salary_range' => 'R$ 3.500 - R$ 5.500',
            'contact_email' => 'rh@englishacademy.com.br',
            'contact_phone' => '(48) 92222-4567',
            'category_id' => $category_map['Educação']
        ],
        [
            'title' => 'Enfermeiro(a)',
            'description' => 'Hospital busca enfermeiro(a) para atuar em UTI. Ambiente hospitalar moderno, equipe multidisciplinar e oportunidades de especialização.',
            'requirements' => '• Graduação em Enfermagem com COREN ativo
• Experiência em UTI será um diferencial
• Conhecimento em protocolos hospitalares
• Disponibilidade para plantões
• Curso de ACLS/BLS atualizado
• Pós-graduação em UTI será um diferencial',
            'company_name' => 'Hospital São Lucas',
            'location' => 'Salvador, BA',
            'salary_range' => 'R$ 5.500 - R$ 8.000',
            'contact_email' => 'rh@hospitalsaolucas.com.br',
            'contact_phone' => '(71) 91111-8901',
            'category_id' => $category_map['Saúde']
        ],
        [
            'title' => 'Assistente Administrativo',
            'description' => 'Vaga para assistente administrativo organizado e proativo. Atividades diversas de apoio administrativo, atendimento ao cliente e suporte às demais áreas.',
            'requirements' => '• Ensino médio completo
• Experiência em rotinas administrativas
• Conhecimento em pacote Office
• Boa comunicação e organização
• Proatividade e trabalho em equipe
• Curso técnico será um diferencial',
            'company_name' => 'Escritório Central',
            'location' => 'Recife, PE',
            'salary_range' => 'R$ 2.500 - R$ 3.500',
            'contact_email' => 'vagas@escritoriocentral.com.br',
            'contact_phone' => '(81) 90000-1234',
            'category_id' => $category_map['Administração']
        ]
    ];

    echo "Inserindo vagas...\n";
    foreach ($jobs as $job) {
        $stmt = $conn->prepare("INSERT INTO job_postings (title, description, requirements, company_name, location, salary_range, contact_email, contact_phone, category_id, created_by, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1, 1)");
        
        $stmt->execute([
            $job['title'],
            $job['description'],
            $job['requirements'],
            $job['company_name'],
            $job['location'],
            $job['salary_range'],
            $job['contact_email'],
            $job['contact_phone'],
            $job['category_id']
        ]);
    }

    // Criar algumas candidaturas de exemplo
    echo "Criando candidaturas de exemplo...\n";
    $stmt = $conn->query("SELECT id FROM users WHERE role = 'user' LIMIT 3");
    $user_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $stmt = $conn->query("SELECT id FROM job_postings LIMIT 5");
    $job_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (!empty($user_ids) && !empty($job_ids)) {
        // Criar algumas candidaturas aleatórias
        for ($i = 0; $i < 8; $i++) {
            $user_id = $user_ids[array_rand($user_ids)];
            $job_id = $job_ids[array_rand($job_ids)];
            $status = ['pending', 'reviewed', 'accepted', 'rejected'][array_rand(['pending', 'reviewed', 'accepted', 'rejected'])];
            
            $stmt = $conn->prepare("INSERT OR IGNORE INTO job_applications (job_id, user_id, status) VALUES (?, ?, ?)");
            $stmt->execute([$job_id, $user_id, $status]);
        }
    }

    echo "✅ Dados criados com sucesso!\n";
    echo "📊 Categorias: " . count($categories) . "\n";
    echo "👥 Usuários: " . count($users) . "\n";
    echo "💼 Vagas: " . count($jobs) . "\n";
    echo "📝 Candidaturas: 8 exemplos\n";
    echo "\n🌐 Acesse: http://localhost:8000\n";
    echo "👤 Admin: admin@example.com / password\n";
    echo "👤 Usuários: joao.silva@email.com / 123456\n";
    echo "👤         maria.santos@email.com / 123456\n";
    echo "👤         pedro.oliveira@email.com / 123456\n";
    echo "👤         ana.costa@email.com / 123456\n";
    echo "👤         carlos.ferreira@email.com / 123456\n";

} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
?> 