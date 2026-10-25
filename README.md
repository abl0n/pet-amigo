
# 🐾 PetAmigo

Sistema de adoção de animais desenvolvido como projeto acadêmico, com foco em segurança, boas práticas de desenvolvimento e aplicação de conceitos DevOps.

## 📋 Pré-requisitos

- [XAMPP](https://www.apachefriends.org/pt_br/index.html) (Apache + MySQL)
- [Git](https://git-scm.com/)
- VS Code (recomendado)
- [Composer](https://getcomposer.org/) (para rodar os testes automatizados)

## 🚀 Como rodar o projeto

### 1. Clonar o repositório
```bash
git clone https://github.com/SEU_USUARIO/pet-amigo.git
2. Mover para o XAMPP
Copie a pasta para:

text
C:\xampp\htdocs\pet-amigo-main\
3. Iniciar o XAMPP
Abra o XAMPP Control Panel e clique em Start no Apache e MySQL.

4. Criar o banco de dados
Abra http://localhost/phpmyadmin

Clique em SQL no topo

Abra o arquivo database/99_setup_completo.sql

Cole TODO o conteúdo na caixa SQL

Clique em Executar

Você verá a mensagem de sucesso do banco petamigo.

5. Configurar a base URL
Se o seu projeto estiver em uma pasta diferente de pet-amigo-main, verifique a variável $base nos arquivos .php (ex: $base = '/pet-amigo-main';).

6. Acessar o site
text
http://localhost/pet-amigo-main/

🔑 Credenciais de teste
Administrador:

E-mail: admin@petamigo.com

Senha: password

(Para testar como usuário comum, basta criar uma nova conta pela tela de cadastro).

📁 Estrutura

pet-amigo-main/
├── assets/          # CSS, JS e imagens
├── config/          # Configuração do banco
├── database/        # Script SQL
├── includes/        # Header, footer, auth e admin_auth
├── src/             # Páginas do sistema (Home, Admin, Cadastro, etc)
├── tests/           # Testes automatizados com PHPUnit
├── phpunit.xml      # Configuração do PHPUnit
└── index.php
🛠️ Tecnologias
PHP 8.2+

MySQL / MariaDB

HTML5 / CSS3 / JavaScript

Font Awesome

PHPUnit (Testes)

GitHub Actions (CI)

✨ Funcionalidades
Usuário Comum:

☑ Cadastro, Login e Logout
☑ Edição e exclusão de conta (Minha Conta)
☑ Cadastro de doadores
☑ Cadastro, edição e exclusão de animais (com upload)
☑ Listagem de animais
☑ Pedido de adoção
☑ Alteração de senha
Administrador (Painel Admin):

☑ Dashboard com estatísticas do sistema
☑ Gerenciamento de usuários (exclusão de contas)
☑ Gerenciamento de animais (exclusão)
☑ Aprovação e rejeição de pedidos de adoção
🛠️ Práticas de DevOps Aplicadas
Este projeto adota princípios de DevOps visando a qualidade, segurança e padronização do código:

Segurança: Uso de password_hash() e password_verify() para senhas; Prepared Statements (PDO) em todas as consultas para evitar SQL Injection.

Controle de Acesso (RBAC): Sistema de cargos (comum e admin), com proteção de rotas administrativas via admin_auth.php.

Testes Automatizados (CI): Implementação de testes básicos com PHPUnit (validação de banco e hash de senha).

Automação: Pipeline configurado no GitHub Actions (.github/workflows/ci.yml) para rodar os testes a cada push ou pull request.

Database as Code: O banco é recriado facilmente através do script SQL versionado.