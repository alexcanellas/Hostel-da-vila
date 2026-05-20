# Instalação e Configuração — Backend Hostel da Vila

Este guia parte do zero: apenas o diretório `Hostel-da-Vila` clonado/copiado na sua máquina, sem nenhuma dependência instalada localmente além do Docker.

---

## Pré-requisito: Docker Desktop

O único programa que você precisa ter instalado é o **Docker Desktop**.

- Download: https://www.docker.com/products/docker-desktop/
- Após instalar, abra o Docker Desktop e aguarde o ícone ficar verde (status "Running") na bandeja do sistema antes de continuar.

> O Docker vai rodar PHP, MySQL, Redis e Nginx dentro de containers isolados. Você **não** precisa instalar PHP, MySQL nem Composer na sua máquina.

---

## Passo 1 — Copiar o arquivo de variáveis de ambiente

O arquivo `.env` contém todas as configurações sensíveis (senhas, chaves de API, etc.).  
Ele nunca é versionado no git — por isso existe o `.env.example` como template.

```powershell
# No terminal, dentro da pasta do projeto:
Copy-Item .env.example .env
```

O `.env` gerado já vem com valores padrão que funcionam localmente. Para desenvolvimento você não precisa alterar nada agora. As seções que precisarão ser preenchidas no futuro são as de integrações externas (Cloudbeds, Getnet, Kommo, HSystem).

**Variáveis principais do `.env` e o que significam:**

```dotenv
# Ambiente da aplicação: development | production
APP_ENV=development

# Exibir erros detalhados no navegador (true apenas em desenvolvimento)
APP_DEBUG=true

# Configurações do banco de dados
# DB_HOST=db  →  "db" é o nome do container MySQL no Docker (não "localhost")
DB_HOST=db
DB_DATABASE=hosteldavila
DB_USERNAME=hosteldavila
DB_PASSWORD=senha_segura

# Chave secreta usada para assinar os tokens JWT
# Em produção, troque por uma string longa e aleatória
JWT_SECRET=sua_chave_secreta_muito_segura_aqui
```

---

## Passo 2 — Instalar as dependências PHP (Composer)

O projeto usa o **Composer** para gerenciar as bibliotecas PHP.  
Como não temos PHP instalado localmente, usamos a imagem Docker oficial do Composer para executar a instalação. Ela baixa todos os pacotes e cria a pasta `vendor/`.

```powershell
# Este comando:
#  - Baixa a imagem "composer:latest" do Docker Hub (só na primeira vez)
#  - Monta a pasta do projeto dentro do container em /app
#  - Executa "composer install" e os arquivos ficam na sua máquina local
docker run --rm -v "${PWD}:/app" composer:latest install --no-interaction --prefer-dist
```

> Após a execução você verá a pasta `vendor/` criada no projeto com todos os pacotes.  
> O arquivo `composer.lock` também será criado — ele registra as versões exatas instaladas.

---

## Passo 3 — Subir os containers Docker

O arquivo `docker-compose.yml` define todos os serviços que compõem o ambiente:

| Container | Imagem | Função | Porta local |
|---|---|---|---|
| `hosteldavila_app` | PHP 8.2-FPM (customizado) | Executa o PHP | — |
| `hosteldavila_nginx` | nginx:1.25-alpine | Servidor web, recebe requisições HTTP | **8080** |
| `hosteldavila_db` | mysql:8.0 | Banco de dados MySQL | 3306 |
| `hosteldavila_redis` | redis:7-alpine | Cache e sessões | 6379 |
| `hosteldavila_phpmyadmin` | phpmyadmin:latest | Interface visual para o banco | **8081** |

```powershell
# --build  → constrói a imagem do container PHP (só necessário na primeira vez ou após mudanças no Dockerfile)
# -d       → sobe os containers em background (libera o terminal)
docker compose up -d --build
```

> Na **primeira execução**, o MySQL vai criar o banco de dados e rodar o script `docker/mysql/init.sql`,
> que cria todas as tabelas e insere o usuário administrador padrão.

**Verificar se todos os containers subiram corretamente:**

```powershell
docker compose ps
```

A saída esperada é todos os containers com status `Up`:

```
NAME                      STATUS
hosteldavila_app          Up
hosteldavila_db           Up (healthy)
hosteldavila_nginx        Up
hosteldavila_phpmyadmin   Up
hosteldavila_redis        Up
```

---

## Passo 4 — Verificar no navegador

Abra os links abaixo no navegador para confirmar que tudo está funcionando:

### API — Health Check
**http://localhost:8080/api/v1/health**

Resposta esperada:
```json
{ "status": "ok", "timestamp": "2026-05-19T21:19:40-03:00" }
```

### PHPMyAdmin — Interface do banco de dados
**http://localhost:8081**

Credenciais de acesso:
- **Usuário:** `hosteldavila`
- **Senha:** `senha_segura`
- **Banco:** `hosteldavila`

Você verá as tabelas: `usuarios`, `acomodacoes`, `reservas`, `clientes`, `pagamentos`, `descontos`, `webhooks_log`.

---

## Passo 5 — Testar a API com o Postman

Baixe o Postman em https://www.postman.com/downloads/ se ainda não tiver.

---

### Teste 1 — Login (obter token JWT)

O token JWT é necessário para acessar todos os endpoints protegidos.

```
Método:  POST
URL:     http://localhost:8080/api/v1/auth/login
```

**Headers:**
```
Content-Type: application/json
```

**Body (raw → JSON):**
```json
{
  "email": "admin@hosteldavila.com.br",
  "senha": "admin123"
}
```

**Resposta esperada (200 OK):**
```json
{
  "token": "eyJ0eXAiOiJKV1Qi...",
  "usuario": {
    "id": 1,
    "nome": "Administrador",
    "email": "admin@hosteldavila.com.br",
    "perfil": "admin"
  }
}
```

> Copie o valor do campo `token` — você vai usá-lo nos próximos testes.

---

### Teste 2 — Ver dados do usuário logado

```
Método:  GET
URL:     http://localhost:8080/api/v1/auth/me
```

**Headers:**
```
Authorization: Bearer <cole o token aqui>
```

**Resposta esperada (200 OK):**
```json
{
  "id": 1,
  "nome": "Administrador",
  "email": "admin@hosteldavila.com.br",
  "perfil": "admin"
}
```

---

### Teste 3 — Criar uma acomodação

```
Método:  POST
URL:     http://localhost:8080/admin/acomodacoes
```

**Headers:**
```
Content-Type:  application/json
Authorization: Bearer <cole o token aqui>
```

**Body (raw → JSON):**
```json
{
  "nome": "Dormitório Misto 8 Leitos",
  "tipo_id": 1,
  "capacidade": 8,
  "preco_base": 75.00,
  "descricao": "Dormitório compartilhado com beliches e armários individuais com cadeado"
}
```

**Resposta esperada (201 Created):**
```json
{
  "id": 1,
  "nome": "Dormitório Misto 8 Leitos",
  "preco_base": "75.00",
  "capacidade": 8,
  ...
}
```

> Os IDs de `tipo_id` disponíveis são os do seed:
> - `1` = Dormitório Misto
> - `2` = Dormitório Feminino
> - `3` = Quarto Privê Duplo
> - `4` = Quarto Privê Individual

---

### Teste 4 — Listar acomodações (rota pública)

Esta rota não precisa de token — é a mesma que o site vai consumir.

```
Método:  GET
URL:     http://localhost:8080/api/v1/acomodacoes
```

**Resposta esperada (200 OK):**
```json
[
  {
    "id": 1,
    "nome": "Dormitório Misto 8 Leitos",
    "preco_base": "75.00",
    "capacidade": 8,
    ...
  }
]
```

---

### Teste 5 — Criar uma reserva (rota pública)

Esta é a rota que o formulário do site vai chamar.

```
Método:  POST
URL:     http://localhost:8080/api/v1/reservas
```

**Headers:**
```
Content-Type: application/json
```

**Body (raw → JSON):**
```json
{
  "acomodacao_id": 1,
  "checkin": "2026-06-10",
  "checkout": "2026-06-13",
  "adultos": 2,
  "criancas": 0,
  "cliente": {
    "nome": "João Silva",
    "email": "joao@email.com",
    "telefone": "11999999999"
  },
  "observacoes": "Chegamos por volta das 18h"
}
```

**Resposta esperada (201 Created):**
```json
{
  "id": 1,
  "codigo": "HDV-XXXXXXXX",
  "status": "pendente",
  "preco_total": "225.00",
  "preco_final": "225.00",
  "checkin": "2026-06-10",
  "checkout": "2026-06-13",
  ...
}
```

---

### Teste 6 — Listar reservas (rota protegida)

```
Método:  GET
URL:     http://localhost:8080/api/v1/reservas
```

**Headers:**
```
Authorization: Bearer <cole o token aqui>
```

**Filtros opcionais via query string:**
```
?status=pendente
?checkin_de=2026-06-01&checkin_ate=2026-06-30
?pagina=1&por_pagina=20
```

---

## Comandos úteis do dia a dia

```powershell
# Parar todos os containers (mantém os dados do banco)
docker compose stop

# Subir novamente sem rebuild
docker compose up -d

# Ver logs em tempo real do PHP
docker logs hosteldavila_app -f

# Ver logs do Nginx
docker logs hosteldavila_nginx -f

# Acessar o terminal do container PHP (para debug)
docker exec -it hosteldavila_app bash

# Resetar o banco completamente (apaga todos os dados e recria as tabelas)
docker compose down -v
docker compose up -d --build
```

---

## Estrutura de pastas do projeto

```
Hostel-da-Vila/
├── public/               → Ponto de entrada (index.php) — Nginx aponta para cá
├── src/
│   ├── App/
│   │   ├── Controllers/  → Recebem a requisição e retornam a resposta
│   │   │   ├── Api/      → Endpoints públicos e autenticados da API
│   │   │   ├── Admin/    → Endpoints da área administrativa
│   │   │   └── WebhookController.php
│   │   ├── Services/     → Regras de negócio (cálculo de preço, validações)
│   │   ├── Repositories/ → Acesso ao banco de dados (queries SQL)
│   │   ├── Middleware/   → CORS, autenticação JWT, tratamento de erros
│   │   ├── Integrations/ → Cloudbeds, Getnet, Kommo, HSystem (fase 2)
│   │   └── Exceptions/   → Exceções customizadas
│   ├── Config/
│   │   ├── settings.php      → Lê variáveis do .env
│   │   └── dependencies.php  → Container de injeção de dependências (PHP-DI)
│   └── Routes/
│       ├── api.php       → Rotas /api/v1/*
│       ├── admin.php     → Rotas /admin/* (requerem JWT)
│       └── webhooks.php  → Rotas /webhooks/* (Cloudbeds, Getnet, Kommo)
├── docker/
│   ├── mysql/init.sql    → Schema do banco + dados iniciais (seed)
│   ├── nginx/default.conf
│   └── php/local.ini
├── storage/
│   ├── logs/             → Logs da aplicação
│   └── cache/            → Cache do container PHP-DI (produção)
├── vendor/               → Dependências PHP (gerado pelo Composer, não versionar)
├── .env                  → Variáveis de ambiente (não versionar)
├── .env.example          → Template do .env (versionado)
├── composer.json         → Dependências e autoload PHP
└── docker-compose.yml    → Orquestração dos containers
```
