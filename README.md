# 🐾 PetAmigo

Sistema de adoção de animais desenvolvido como projeto acadêmico.

## 📋 Pré-requisitos

- [XAMPP](https://www.apachefriends.org/pt_br/index.html) (Apache + MySQL)
- [Git](https://git-scm.com/)
- VS Code (recomendado)

## 🚀 Como rodar o projeto

### 1. Clonar o repositório
```bash
git clone https://github.com/SEU_USUARIO/pet-amigo.git
```

### 2. Mover para o XAMPP
Copie a pasta para:
```
C:\xampp\htdocs\petAmigo\
```

### 3. Iniciar o XAMPP
Abra o XAMPP Control Panel e clique em **Start** no Apache e MySQL.

### 4. Criar o banco de dados
1. Abra `http://localhost/phpmyadmin`
2. Clique em **SQL** no topo
3. Abra o arquivo `database/99_setup_completo.sql`
4. Cole TODO o conteúdo na caixa SQL
5. Clique em **Executar**

Você verá:
```
✅ Banco petamigo criado com sucesso!
total_usuarios: 1
total_animais: 12
```

### 5. Acessar o site
```
http://localhost/petAmigo/
```

## 🔑 Credenciais de teste

- **E-mail:** `admin@petamigo.com`
- **Senha:** `password`

## 📁 Estrutura

```
petAmigo/
├── assets/          # CSS, JS e imagens
├── config/          # Configuração do banco
├── database/        # Script SQL
├── includes/        # Header, footer e auth
├── src/             # Páginas do sistema
└── index.php
```

## 🛠️ Tecnologias

- PHP 8.2+
- MySQL / MariaDB
- HTML5 / CSS3 / JavaScript
- Font Awesome

## ✨ Funcionalidades

- [x] Cadastro de usuários
- [x] Login / Logout
- [x] Cadastro de doadores
- [x] Cadastro de animais (com upload)
- [x] Edição de animais
- [x] Exclusão de animais
- [x] Listagem de animais
- [x] Pedido de adoção

## 👥 Equipe

- Seu Nome — [@seu-github](https://github.com/seu-github)
- Colega 1
- Colega 2

## 📄 Licença

Projeto acadêmico — uso livre para fins educacionais.