<?php
// languages/pt.php - Arquivo de idioma Português

if (defined('FUTPEDIA_ACCESS') && !FUTPEDIA_ACCESS && php_sapi_name() !== 'cli') {
    die('Acesso direto não permitido ao arquivo de idioma.');
}

return [
    // Geral
    'site_name' => 'Futpedia',
    'toggle_navigation' => 'Alternar navegação',
    'home' => 'Início',
    'login' => 'Login', // o Entrar
    'register' => 'Registar', // o Cadastro
    'logout' => 'Sair',
    'my_profile' => 'Meu Perfil',
    'admin_panel' => 'Painel Admin',
    'search' => 'Pesquisar', // o Buscar
    'go' => 'Ir',
    'yes' => 'Sim',
    'no' => 'Não',
    'save' => 'Salvar', // o Guardar
    'edit' => 'Editar',
    'delete' => 'Excluir', // o Apagar
    'cancel' => 'Cancelar',
    'error' => 'Erro',
    'success' => 'Sucesso',
    'page_not_found' => 'Página não encontrada',
    'oops_error_occurred' => 'Opa! Ocorreu um erro.',
    'welcome_message' => 'Bem-vindo ao Coração da Futpedia!',
    'current_datetime_label' => 'Data e hora atuais (formatadas):',
    'db_connection_ok' => 'A conexão com o banco de dados parece estar configurada e funcionando corretamente.',
    'db_connection_error' => 'Erro: DB_HOST está definido, mas a instância do Banco de Dados não foi criada ou a conexão falhou.',
    'under_construction_title' => 'Bem-vindo à Futpedia',
    'under_construction_message' => 'Este é o ponto de entrada principal da aplicação, agora com um design básico.',
    'under_construction_info' => 'Em breve você verá aqui conteúdo dinâmico sobre o mundo do futebol.',

    // Específico do cabeçalho/rodapé (exemplos)
    'main_navigation' => 'Navegação Principal',
    'copyright_notice' => '&copy; %year% %site_name%. Todos os direitos reservados.',

    // Formulários (exemplos)
    'username' => 'Nome de usuário',
    'password' => 'Senha',
    'email' => 'Endereço de e-mail',
    'remember_me' => 'Lembrar-me',
    
    // Mensagens flash (exemplos)
    'flash_config_loaded_successfully' => 'Configuração e sessão carregadas com sucesso!',
    'flash_test_error_message' => 'Esta é uma mensagem de erro de teste.',
];