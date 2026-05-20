# 🏨 Hostel da Vila - Backend API

Sistema de gestão completo para hostel com integrações de pagamento, PMS e CRM.

## 🚀 Tecnologias

- **PHP 8.2** com arquitetura MVC
- **Slim Framework 4** para API REST
- **MySQL 8.0** como banco de dados
- **Redis** para cache e sessões
- **Docker** para containerização
- **Nginx** como servidor web
- **Composer** para gerenciamento de dependências

## 📋 Pré-requisitos

- Docker & Docker Compose instalados
- Git
- 4GB RAM disponível (mínimo)

## 🔧 Instalação e Configuração

### 1. Clone o repositório

```bash
git clone [url-do-repositorio]
cd hosteldavila-backend
```

### 2. Configure as variáveis de ambiente

```bash
cp .env.example .env
```

Edite o arquivo `.env` com suas configurações reais (principalmente as chaves de API).

### 3. Inicie os containers Docker

```bash
docker-compose up -d
```

Isso irá:
- Criar e iniciar todos os containers (PHP, Nginx, MySQL, Redis)
- Instalar as dependências do Composer automaticamente
- Executar o script de inicialização do banco de dados

### 4. Instale as dependências PHP (se necessário)

```bash
docker-compose exec app composer install
```

### 5. Verifique se está funcionando

Acesse: `http://localhost:8080/api/v1/health`

Você deve ver uma resposta JSON:
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

## 📁 Estrutura do Projeto

```
hosteldavila-backend/
├── docker/                 # Configurações Docker
├── public/                 # Ponto de entrada público
│   └── index.php          # Entry point da aplicação
├── src/
│   ├── App/
│   │   ├── Controllers/   # Controladores MVC
│   │   ├── Models/        # Modelos de dados
│   │   ├── Services/      # Lógica de negócio
│   │   ├── Repositories/  # Acesso a dados
│   │   ├── Middleware/    # Middlewares
│   │   └── Integrations/  # Integrações externas
│   ├── Config/            # Configurações
│   └── Routes/            # Definições de rotas
├── storage/               # Logs e cache
└── tests/                 # Testes
```

## 🔐 Autenticação

O sistema usa JWT (JSON Web Tokens) para autenticação.

### Login
```bash
POST /admin/login
Content-Type: application/json

{
  "email": "admin@hosteldavila.com.br",
  "password": "admin123"
}
```

**Credenciais padrão:**
- Email: `admin@hosteldavila.com.br`
- Senha: `admin123`

⚠️ **IMPORTANTE**: Altere a senha padrão em produção!

### Usar o token

Inclua o token no header `Authorization`:
```
Authorization: Bearer seu_token_aqui
```

## 📚 Endpoints da API

### Públicos

#### Listar acomodações
```
GET /api/v1/acomodacoes
```

#### Verificar disponibilidade
```
GET /api/v1/acomodacoes/{id}/disponibilidade?checkin=2025-02-01&checkout=2025-02-05
```

#### Criar reserva
```
POST /api/v1/reservas
Content-Type: application/json

{
  "acomodacao_id": 1,
  "checkin": "2025-02-01",
  "checkout": "2025-02-05",
  "numero_hospedes": 2,
  "cliente": {
    "nome": "João Silva",
    "email": "joao@email.com",
    "telefone": "+5521999999999",
    "documento": "12345678900"
  }
}
```

### Autenticados (requerem JWT)

#### Listar reservas
```
GET /api/v1/reservas?page=1&per_page=20&status=confirmada
Authorization: Bearer {token}
```

#### Atualizar reserva
```
PUT /api/v1/reservas/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "status": "confirmada",
  "observacoes": "Check-in antecipado"
}
```

#### Gerar link de pagamento
```
POST /api/v1/pagamentos/gerar-link
Authorization: Bearer {token}
Content-Type: application/json

{
  "reserva_id": 123,
  "valor": 300.00
}
```

### Webhooks

#### Cloudbeds - Reserva criada
```
POST /webhooks/cloudbeds/reserva-criada
```

#### Getnet - Notificação de pagamento
```
POST /webhooks/getnet/pagamento
```

#### Kommo - Novo lead
```
POST /webhooks/kommo/lead
```

## 🔗 Integrações

### 1. Cloudbeds (PMS)

Configure no `.env`:
```env
CLOUDBEDS_API_KEY=sua_chave
CLOUDBEDS_CLIENT_ID=seu_client_id
CLOUDBEDS_CLIENT_SECRET=seu_secret
CLOUDBEDS_PROPERTY_ID=seu_property_id
CLOUDBEDS_WEBHOOK_SECRET=seu_webhook_secret
```

**Webhooks suportados:**
- Reserva criada
- Reserva modificada
- Reserva cancelada
- Check-in realizado
- Check-out realizado

### 2. Getnet (Gateway de Pagamento)

Configure no `.env`:
```env
GETNET_CLIENT_ID=seu_client_id
GETNET_CLIENT_SECRET=seu_secret
GETNET_SELLER_ID=seu_seller_id
GETNET_ENVIRONMENT=sandbox  # ou production
```

**Funcionalidades:**
- Geração de link de pagamento
- Notificações de status
- Webhooks de confirmação

### 3. Kommo CRM

Configure no `.env`:
```env
KOMMO_SUBDOMAIN=seu_subdominio
KOMMO_CLIENT_ID=seu_client_id
KOMMO_CLIENT_SECRET=seu_secret
KOMMO_ACCESS_TOKEN=seu_token
KOMMO_REFRESH_TOKEN=seu_refresh_token
```

**Funcionalidades:**
- Sincronização de leads
- Criação automática de negócios
- Webhooks de atualização

### 4. HSystem

Configure no `.env`:
```env
HSYSTEM_API_URL=https://api.hsystem.com.br
HSYSTEM_API_KEY=sua_chave
HSYSTEM_HOTEL_ID=seu_hotel_id
```

## 🛠️ Comandos Úteis

### Acessar container PHP
```bash
docker-compose exec app bash
```

### Ver logs em tempo real
```bash
docker-compose logs -f app
```

### Reiniciar containers
```bash
docker-compose restart
```

### Parar todos os containers
```bash
docker-compose down
```

### Rebuild completo
```bash
docker-compose down -v
docker-compose up --build -d
```

### Executar composer
```bash
docker-compose exec app composer install
docker-compose exec app composer update
```

### Acessar MySQL
```bash
docker-compose exec mysql mysql -u hosteldavila_user -phosteldavila_pass hosteldavila
```

### Backup do banco de dados
```bash
docker-compose exec mysql mysqldump -u root -proot_secret hosteldavila > backup.sql
```

### Restaurar backup
```bash
docker-compose exec -T mysql mysql -u root -proot_secret hosteldavila < backup.sql
```

## 🧪 Testes

```bash
# Rodar todos os testes
docker-compose exec app composer test

# Rodar testes unitários
docker-compose exec app vendor/bin/phpunit tests/Unit

# Rodar testes de integração
docker-compose exec app vendor/bin/phpunit tests/Integration
```

## 📊 Ferramentas de Desenvolvimento

### PHPMyAdmin
Acesse: `http://localhost:8081`
- Usuário: `hosteldavila_user`
- Senha: `hosteldavila_pass`

### Logs
Os logs ficam em `storage/logs/app.log`

```bash
# Ver logs
tail -f storage/logs/app.log
```

## 🚦 Fluxo de Desenvolvimento Recomendado

### Fase 1: Estrutura Base (1-2 semanas)
1. ✅ Configurar Docker e ambiente
2. ✅ Criar estrutura MVC
3. ✅ Implementar autenticação JWT
4. ✅ CRUD de acomodações
5. ✅ CRUD de reservas
6. ✅ CRUD de clientes

### Fase 2: Integrações Principais (2-3 semanas)
1. Integração Cloudbeds (webhooks)