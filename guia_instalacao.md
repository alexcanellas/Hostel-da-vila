# 🪟 Guia de Instalação Completo - Windows 11

Guia detalhado para configurar o ambiente de desenvolvimento do Hostel da Vila no Windows 11 com VSCode.

---

## 📋 Índice de Instalação

1. WSL 2 (Windows Subsystem for Linux)
2. Docker Desktop
3. Git
4. VSCode + Extensões
5. Composer (opcional, rodará no Docker)
6. Cliente MySQL (opcional)
7. Postman/Insomnia
8. Configuração do Projeto

---

## 1️⃣ WSL 2 - Windows Subsystem for Linux

**O que é:** Permite rodar Linux nativamente dentro do Windows. O Docker Desktop precisa dele.

**Por que:** Docker funciona melhor com WSL 2, performance superior e compatibilidade.

### Instalação

```powershell
# Abra PowerShell como ADMINISTRADOR e execute:

# 1. Habilitar WSL
wsl --install

# Se der erro, tente:
dism.exe /online /enable-feature /featurename:Microsoft-Windows-Subsystem-Linux /all /norestart
dism.exe /online /enable-feature /featurename:VirtualMachinePlatform /all /norestart

# 2. Reiniciar o computador (OBRIGATÓRIO)

# 3. Após reiniciar, definir WSL 2 como padrão
wsl --set-default-version 2

# 4. Instalar Ubuntu (ou outra distro)
wsl --install -d Ubuntu

# 5. Verificar instalação
wsl --list --verbose
```

### Configuração Inicial do Ubuntu

```bash
# Ao abrir Ubuntu pela primeira vez:
# - Criar usuário e senha
# - Anotar a senha, você vai usar bastante!

# Atualizar pacotes
sudo apt update && sudo apt upgrade -y
```

**Status:** ✅ WSL 2 instalado e configurado

---

## 2️⃣ Docker Desktop para Windows

**O que é:** Plataforma para rodar containers (aplicações isoladas).

**Por que:** Roda PHP, MySQL, Nginx, Redis sem instalar nada na sua máquina.

### Download e Instalação

1. **Baixar:** https://www.docker.com/products/docker-desktop/
2. **Executar** o instalador Docker Desktop Installer.exe
3. **Marcar opção:** "Use WSL 2 instead of Hyper-V"
4. **Instalar** e aguardar
5. **Reiniciar** o computador

### Configuração Inicial

```powershell
# Abrir Docker Desktop (ícone na bandeja)

# Configurações recomendadas:
# - Settings > General > Use WSL 2 based engine ✓
# - Settings > Resources > WSL Integration > Enable Ubuntu ✓
# - Settings > Resources > Advanced:
#   - CPUs: 4 (ou metade dos seus cores)
#   - Memory: 4 GB (mínimo)
#   - Swap: 2 GB
```

### Verificar Instalação

```powershell
# No PowerShell ou CMD:
docker --version
# Saída esperada: Docker version 24.x.x

docker-compose --version
# Saída esperada: Docker Compose version v2.x.x

# Testar Docker
docker run hello-world
# Deve baixar e executar um container de teste
```

**Status:** ✅ Docker Desktop funcionando

---

## 3️⃣ Git para Windows

**O que é:** Sistema de controle de versão.

**Por que:** Gerenciar código, versionar alterações, trabalhar em equipe.

### Instalação

1. **Baixar:** https://git-scm.com/download/win
2. **Executar** Git-2.x.x-64-bit.exe
3. **Configurações recomendadas durante instalação:**
   - Editor: Use Visual Studio Code as Git's default editor
   - PATH: Git from the command line and also from 3rd-party software
   - Line endings: Checkout Windows-style, commit Unix-style
   - Terminal: Use Windows' default console window
   - Resto: opções padrão

### Configuração Inicial

```bash
# Abrir Git Bash (ou PowerShell/CMD) e configurar:

# Seu nome (aparecerá nos commits)
git config --global user.name "Seu Nome"

# Seu email
git config --global user.email "seu@email.com"

# Editor padrão
git config --global core.editor "code --wait"

# Verificar configuração
git config --list
```

**Status:** ✅ Git configurado

---

## 4️⃣ Visual Studio Code + Extensões

**O que é:** Editor de código profissional e gratuito.

**Por que:** Melhor editor para PHP, com suporte a Docker, Git e debugging.

### Instalação do VSCode

1. **Baixar:** https://code.visualstudio.com/
2. **Executar** VSCodeUserSetup-x64-1.x.x.exe
3. **Marcar todas as opções:**
   - ✓ Add "Open with Code" to context menu
   - ✓ Register Code as an editor for supported file types
   - ✓ Add to PATH

### Extensões Essenciais

Abra VSCode e instale as seguintes extensões (Ctrl+Shift+X):

#### **PHP Development** (Obrigatórias)

```bash
# No VSCode, pressione Ctrl+P e cole cada linha:

# 1. PHP Intelephense - Autocomplete, syntax, linting
ext install bmewburn.vscode-intelephense-client

# 2. PHP Debug - Debugging com Xdebug
ext install xdebug.php-debug

# 3. PHP Namespace Resolver - Auto-import de classes
ext install MehediDracula.php-namespace-resolver

# 4. PHP DocBlocker - Gerar comentários automáticos
ext install neilbrayfield.php-docblocker
```

#### **Docker & DevOps** (Obrigatórias)

```bash
# 5. Docker - Gerenciar containers pelo VSCode
ext install ms-azuretools.vscode-docker

# 6. Remote - Containers - Desenvolver dentro de containers
ext install ms-vscode-remote.remote-containers

# 7. WSL - Integração com WSL
ext install ms-vscode-remote.remote-wsl
```

#### **Git & Controle de Versão** (Recomendadas)

```bash
# 8. GitLens - Superpoderes para Git
ext install eamodio.gitlens

# 9. Git Graph - Visualizar histórico de commits
ext install mhutchie.git-graph
```

#### **Produtividade** (Recomendadas)

```bash
# 10. REST Client - Testar APIs sem sair do VSCode
ext install humao.rest-client

# 11. Todo Tree - Gerenciar TODOs no código
ext install Gruntfuggly.todo-tree

# 12. Error Lens - Mostrar erros inline
ext install usernamehw.errorlens

# 13. Better Comments - Comentários coloridos
ext install aaron-bond.better-comments

# 14. Path Intellisense - Autocomplete de caminhos
ext install christian-kohler.path-intellisense
```

#### **Aparência** (Opcional)

```bash
# 15. Material Icon Theme - Ícones bonitos
ext install PKief.material-icon-theme

# 16. Dracula Theme - Tema dark popular
ext install dracula-theme.theme-dracula
```

### Configurações Recomendadas do VSCode

Pressione `Ctrl+Shift+P` → digite "Open Settings (JSON)" e adicione:

```json
{
  "editor.fontSize": 14,
  "editor.tabSize": 4,
  "editor.insertSpaces": true,
  "editor.formatOnSave": true,
  "editor.wordWrap": "on",
  "files.autoSave": "afterDelay",
  "files.autoSaveDelay": 1000,
  "files.eol": "\n",
  "php.suggest.basic": false,
  "intelephense.files.maxSize": 5000000,
  "docker.dockerComposeBuild": true,
  "terminal.integrated.defaultProfile.windows": "Git Bash"
}
```

**Status:** ✅ VSCode configurado com extensões

---

## 5️⃣ Composer (Opcional - Rodará no Docker)

**O que é:** Gerenciador de dependências do PHP.

**Por que:** Instalar bibliotecas PHP (Slim, Monolog, JWT, etc).

**Nota:** Como usaremos Docker, o Composer rodará DENTRO do container. Mas se quiser ter instalado localmente:

### Opção 1: Usar no Docker (Recomendado)

```bash
# Você executará assim:
docker-compose exec app composer install
docker-compose exec app composer update
```

### Opção 2: Instalar Localmente (Opcional)

1. **Baixar:** https://getcomposer.org/Composer-Setup.exe
2. **Executar** e seguir instalação
3. **Verificar:**

```powershell
composer --version
```

**Status:** ⏭️ Usaremos Composer no Docker

---

## 6️⃣ Cliente MySQL (Opcional mas Recomendado)

**O que é:** Interface gráfica para gerenciar banco de dados.

**Por que:** Visualizar tabelas, executar queries, gerenciar dados facilmente.

### Opções:

#### **Opção A: PHPMyAdmin (já vem no Docker)**
- URL: `http://localhost:8081`
- Usuário: `hosteldavila_user`
- Senha: `hosteldavila_pass`
- **Vantagem:** Já configurado, sem instalação
- **Desvantagem:** Só funciona com containers rodando

#### **Opção B: DBeaver (Recomendado)**

**Grátis, open-source, poderoso**

1. **Baixar:** https://dbeaver.io/download/
2. **Instalar** dbeaver-ce-x.x.x-x86_64-setup.exe
3. **Configurar conexão:**
   - Host: `localhost`
   - Port: `3306`
   - Database: `hosteldavila`
   - Username: `hosteldavila_user`
   - Password: `hosteldavila_pass`

#### **Opção C: TablePlus**

**Bonito, rápido, pago (mas tem trial)**

1. **Baixar:** https://tableplus.com/windows
2. **Instalar** e configurar conexão similar ao DBeaver

#### **Opção D: MySQL Workbench**

**Oficial da Oracle, gratuito**

1. **Baixar:** https://dev.mysql.com/downloads/workbench/
2. **Instalar** e configurar

**Recomendação:** Use PHPMyAdmin (já vem pronto) + DBeaver (para trabalhos pesados)

**Status:** ✅ Cliente MySQL escolhido

---

## 7️⃣ Postman ou Insomnia (Para Testar APIs)

**O que é:** Ferramentas para testar requisições HTTP/APIs.

**Por que:** Testar endpoints sem precisar do frontend pronto.

### Opção A: Postman (Mais Popular)

1. **Baixar:** https://www.postman.com/downloads/
2. **Instalar** Postman-win64-Setup.exe
3. **Criar conta** (gratuito)
4. **Importar** coleções de requisições

### Opção B: Insomnia (Mais Simples)

1. **Baixar:** https://insomnia.rest/download
2. **Instalar** Insomnia.Core-x.x.x.exe
3. **Mais leve** que Postman

### Opção C: REST Client (Extensão VSCode - Já Instalamos!)

**Criar arquivo `.http` no VSCode:**

```http
### Health Check
GET http://localhost:8080/api/v1/health

### Login
POST http://localhost:8080/admin/login
Content-Type: application/json

{
  "email": "admin@hosteldavila.com.br",
  "password": "admin123"
}

### Listar Reservas (Com Token)
GET http://localhost:8080/api/v1/reservas
Authorization: Bearer SEU_TOKEN_AQUI
```

**Recomendação:** Postman para times, REST Client do VSCode para uso pessoal.

**Status:** ✅ Ferramenta de teste escolhida

---

## 8️⃣ Ferramentas Adicionais (Opcionais)

### A. Node.js (Se for desenvolver frontend React/Next)

```powershell
# Baixar: https://nodejs.org/ (versão LTS)
# Instalar e verificar:
node --version
npm --version
```

### B. Windows Terminal (Terminal Moderno)

```powershell
# Baixar da Microsoft Store: "Windows Terminal"
# Ou: https://aka.ms/terminal
```

### C. Notepad++ (Editor leve para arquivos de config)

```powershell
# Baixar: https://notepad-plus-plus.org/downloads/
```

---

## 9️⃣ Configurar o Projeto Hostel da Vila

Agora que tudo está instalado, vamos criar o projeto!

### Passo 1: Criar Pasta do Projeto

```powershell
# No PowerShell ou Git Bash:
cd C:\Users\SeuUsuario\Documents
mkdir hosteldavila-backend
cd hosteldavila-backend
```

### Passo 2: Estrutura de Pastas

```bash
# Criar estrutura completa
mkdir -p docker/nginx docker/php docker/mysql
mkdir -p public
mkdir -p src/App/Controllers/Admin src/App/Controllers/Api
mkdir -p src/App/Models src/App/Services src/App/Repositories
mkdir -p src/App/Middleware src/App/Integrations/Cloudbeds
mkdir -p src/App/Integrations/Getnet src/App/Integrations/Kommo
mkdir -p src/App/Integrations/HSystem
mkdir -p src/Config src/Routes
mkdir -p storage/logs storage/cache storage/uploads
mkdir -p tests/Unit tests/Integration
mkdir -p database/migrations database/seeds

# Criar arquivos .gitkeep para manter pastas vazias no Git
touch storage/logs/.gitkeep
touch storage/cache/.gitkeep
touch storage/uploads/.gitkeep
```

### Passo 3: Copiar Arquivos Fornecidos

Copie TODOS os arquivos que criei anteriormente para as respectivas pastas:

```
hosteldavila-backend/
├── docker-compose.yml          (copiar para raiz)
├── .env.example                (copiar para raiz)
├── .gitignore                  (copiar para raiz)
├── composer.json               (copiar para raiz)
├── README.md                   (copiar para raiz)
├── Makefile                    (copiar para raiz - opcional no Windows)
├── docker/
│   ├── Dockerfile
│   ├── nginx/default.conf
│   ├── php/local.ini
│   └── mysql/init.sql
├── public/
│   └── index.php
└── src/
    └── ... (todos os arquivos PHP)
```

### Passo 4: Configurar .env

```bash
# Copiar exemplo
cp .env.example .env

# Editar .env no VSCode
code .env
```

Configurações mínimas iniciais:
```env
APP_ENV=development
APP_DEBUG=true
DB_HOST=mysql
DB_DATABASE=hosteldavila
DB_USERNAME=hosteldavila_user
DB_PASSWORD=hosteldavila_pass
JWT_SECRET=mude_este_secret_em_producao_12345
```

### Passo 5: Iniciar Docker

```bash
# No terminal (PowerShell, CMD ou Git Bash):
docker-compose up -d

# Aguardar containers iniciarem (20-30 segundos)

# Verificar se está rodando
docker-compose ps

# Deve mostrar:
# hosteldavila_app       running
# hosteldavila_nginx     running
# hosteldavila_mysql     running
# hosteldavila_redis     running
# hosteldavila_phpmyadmin running
```

### Passo 6: Instalar Dependências PHP

```bash
# Entrar no container PHP
docker-compose exec app bash

# Dentro do container:
composer install

# Sair do container
exit
```

### Passo 7: Testar Aplicação

```bash
# Testar health check
curl http://localhost:8080/api/v1/health

# Ou abrir no navegador:
# http://localhost:8080/api/v1/health
```

**Resposta esperada:**
```json
{
  "success": true,
  "data": {
    "status": "ok",
    "timestamp": "2025-01-15 10:30:00",
    "version": "1.0.0"
  }
}
```

---

## ✅ Checklist Final de Instalação

```
[  ] WSL 2 instalado e funcionando
[  ] Docker Desktop instalado e rodando
[  ] Git configurado com nome e email
[  ] VSCode instalado
[  ] Extensões PHP instaladas no VSCode
[  ] Extensões Docker instaladas no VSCode
[  ] Cliente MySQL escolhido (PHPMyAdmin/DBeaver)
[  ] Postman ou Insomnia instalado
[  ] Projeto criado e estruturado
[  ] docker-compose.yml no lugar
[  ] .env configurado
[  ] Containers rodando (docker-compose ps)
[  ] Dependências instaladas (composer install)
[  ] API respondendo (health check)
```

---

## 🎯 Próximos Passos

1. **Abrir projeto no VSCode:**
   ```bash
   code C:\Users\SeuUsuario\Documents\hosteldavila-backend
   ```

2. **Explorar código:** Entenda a estrutura

3. **Seguir GUIA_DESENVOLVIMENTO.md:** Implementar funcionalidades

4. **Usar Makefile (via Git Bash):**
   ```bash
   make logs      # Ver logs
   make shell     # Entrar no container
   make db        # Acessar MySQL
   ```

---

## 🆘 Troubleshooting Comum

### Docker não inicia
```powershell
# Verificar se WSL 2 está rodando
wsl --list --verbose

# Reiniciar Docker Desktop
# Botão direito no ícone > Restart
```

### Porta 3306 ou 8080 já em uso
```bash
# Parar serviços conflitantes
# MySQL local, Apache, etc

# Ou alterar porta no docker-compose.yml:
ports:
  - "3307:3306"  # Ao invés de 3306:3306
```

### Permission denied no WSL
```bash
# Ajustar permissões
sudo chmod -R 777 storage/
```

### Composer muito lento
```bash
# Dentro do container:
composer config --global process-timeout 2000
composer install --prefer-dist
```

---

## 📞 Recursos Úteis

- **Docker Docs:** https://docs.docker.com/
- **PHP Docs:** https://www.php.net/manual/pt_BR/
- **Slim Framework:** https://www.slimframework.com/docs/v4/
- **VSCode Docs:** https://code.visualstudio.com/docs

---

**Pronto! Ambiente completamente configurado!** 🎉

Qualquer dúvida em algum passo específico, me avise!

