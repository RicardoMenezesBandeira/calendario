<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quem Somos - Calendário UFF</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/about.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-custom">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <img src="assets/img/logo-calendario-branco.svg" alt="Logo">
                <span class="text-white font-weight-bold">Calendário</span>
            </a>
            <div class="ml-auto">
                <a href="auth_login.php" class="btn btn-login">Login</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero-section">
        <h1>Quem Somos</h1>
        <p>Conheça a plataforma que revoluciona a gestão de equipes</p>
    </div>

    <!-- Conteúdo Principal -->
    <div class="container">
        <div class="content-section">
            <h2><i class="fas fa-info-circle"></i> Sobre o Calendário</h2>
            <p>
                O <strong>Calendário</strong> é uma plataforma inovadora desenvolvida para facilitar a gestão e organização 
                de equipes dentro da sua empresa. Nossa missão é simplificar a comunicação, agendamento 
                e coordenação de atividades entre colaboradores, líderes e gerentes.
            </p>
            <p>
                Com uma interface intuitiva e ferramentas poderosas, oferecemos uma solução completa para marcação de eventos, 
                gerenciamento de equipes e sincronização de atividades, tudo em um único lugar.
            </p>
        </div>

        <!-- Features -->
        <div class="content-section">
            <h2><i class="fas fa-star"></i> Nossas Funcionalidades</h2>
            <div class="features-section">
                <div class="feature-card">
                    <i class="fas fa-calendar-alt"></i>
                    <h4>Calendário Inteligente</h4>
                    <p>Visualize e gerencie todos os eventos da sua equipe em um calendário integrado e interativo.</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-users"></i>
                    <h4>Gestão de Equipes</h4>
                    <p>Organize colaboradores em equipes e facilite a comunicação entre membros.</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-tasks"></i>
                    <h4>Marcação de Eventos</h4>
                    <p>Crie, edite e compartilhe eventos com toda a sua equipe de forma simples e rápida.</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-shield-alt"></i>
                    <h4>Segurança Garantida</h4>
                    <p>Seus dados são protegidos com criptografia e controle de acesso por perfil de usuário.</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-mobile-alt"></i>
                    <h4>Responsivo</h4>
                    <p>Acesse de qualquer dispositivo - desktop, tablet ou smartphone.</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-chart-bar"></i>
                    <h4>Relatórios</h4>
                    <p>Acompanhe métricas e gere relatórios sobre atividades das equipes.</p>
                </div>
            </div>
        </div>

        <!-- Missão, Visão, Valores -->
        <div class="content-section">
            <h2><i class="fas fa-compass"></i> Missão, Visão e Valores</h2>
            
            <div style="margin-top: 30px;">
                <h4 style="color: var(--cor-base); font-weight: 600; margin-top: 20px;">🎯 Missão</h4>
                <p>
                    Fornecer uma plataforma de gestão de calendário e equipes que simplifique processos, 
                    aumente a produtividade e melhore a comunicação dentro da sua empresa.
                </p>

                <h4 style="color: var(--cor-base); font-weight: 600; margin-top: 20px;">👁️ Visão</h4>
                <p>
                    Ser a solução de referência para gestão de equipes e eventos na sua empresa, 
                    promovendo excelência operacional e colaboração eficaz.
                </p>

                <h4 style="color: var(--cor-base); font-weight: 600; margin-top: 20px;">💎 Valores</h4>
                <ul style="margin-left: 20px; color: #333;">
                    <li>Excelência em qualidade de serviço</li>
                    <li>Inovação contínua</li>
                    <li>Transparência e confiabilidade</li>
                    <li>Simplicidade na usabilidade</li>
                    <li>Segurança dos dados</li>
                </ul>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="cta-section">
            <h2>Pronto para começar?</h2>
            <p>Junte-se à nossa plataforma e transforme a forma como sua equipe trabalha!</p>
            <a href="auth_login.php" class="btn btn-custom">
                <i class="fas fa-sign-in-alt"></i> Fazer Login
            </a>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2025 Agendas. Todos os direitos reservados.</p>
        <p>Desenvolvido com <i class="fas fa-heart" style="color: #e74c3c;"></i></p>
    </footer>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
</body>
</html>
