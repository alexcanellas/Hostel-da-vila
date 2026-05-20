# 📘 Guia de Desenvolvimento - Hostel da Vila

## 🎯 Visão Geral do Projeto

Este documento orienta o fluxo de desenvolvimento completo do backend do sistema de gestão do Hostel da Vila.

## ✅ Estrutura Base Criada

### Arquivos Docker
- ✅ `docker-compose.yml` - Orquestração de containers
- ✅ `docker/Dockerfile` - Imagem PHP 8.2
- ✅ `docker/nginx/default.conf` - Configuração Nginx
- ✅ `docker/php/local.ini` - Configurações PHP
- ✅ `docker/mysql/init.sql` - Schema do banco de dados

### Configurações
- ✅ `composer.json` - Dependências PHP
- ✅ `.env.example` - Template de variáveis de ambiente
- ✅ `.gitignore` - Arquivos ignorados pelo Git
- ✅ `src/Config/settings.php` - Configurações gerais
- ✅ `src/Config/dependencies.php` - Injeção de dependências

### Estrutura MVC
- ✅ `public/index.php` - Entry point
- ✅ Controllers base e exemplos
- ✅ Models com Reserva completo
- ✅ Repositories com ReservaRepository
- ✅ Services com ReservaService
- ✅ Middlewares (CORS, Auth)
- ✅ Rotas organizadas (API, Admin, Webhooks)

### Integrações
- ✅ `CloudbedsClient` - Integração completa Cloudbeds
- ✅ `GetnetClient` - Integração pagamentos Getnet
- ✅ `WebhookController` - Processamento de webhooks

## 🚀 Comandos Rápidos de Início

```bash
# 1. Clonar/criar estrutura do projeto
mkdir hosteldavila-backend
cd hosteldavila-backend

# 2. Copiar todos os arquivos criados para o projeto

# 3. Configurar ambiente
cp .env.example .env
# Editar .env com suas credenciais

# 4. Iniciar containers
docker-compose up -d

# 5. Instalar dependências
docker-compose exec app composer install

# 6. Verificar funcionamento
curl http://localhost:8080/api/v1/health
```

## 📋 Checklist de Implementação

### Fase 1: Base do Sistema (1-2 semanas)

#### Semana 1: Estrutura e CRUD Básico
- [x] Configurar Docker e ambiente
- [x] Criar estrutura MVC completa
- [x] Implementar autenticação JWT
- [ ] Implementar AuthController completo
- [ ] Criar hash de senha para usuário admin
- [ ] Testar login e geração de token

**Controllers a implementar:**
- [ ] `AcomodacaoController` (Admin e API)
- [ ] `ClienteController`
- [ ] `PagamentoController`
- [ ] `DashboardController`

**Repositories a criar:**
- [ ] `AcomodacaoRepository`
- [ ] `ClienteRepository`
- [ ] `PagamentoRepository`
- [ ] `DescontoRepository`

**Models a criar:**
- [ ] `Acomodacao`
- [ ] `Cliente`
- [ ] `Pagamento`
- [ ] `Desconto`
- [ ] `Usuario`

#### Semana 2: Validações e Testes
- [ ] Implementar validações com Respect\Validation
- [ ] Criar testes unitários básicos
- [ ] Testar todos endpoints CRUD
- [ ] Documentar endpoints no README
- [ ] Implementar tratamento de erros global

### Fase 2: Integrações Principais (2-3 semanas)

#### Semana 3: Cloudbeds
- [x] Cliente Cloudbeds básico criado
- [ ] Testar autenticação Cloudbeds
- [ ] Implementar sincronização de acomodações
- [ ] Configurar webhooks no painel Cloudbeds
- [ ] Testar recebimento de webhooks
- [ ] Implementar processamento completo de reservas
- [ ] Sistema de retry para falhas
- [ ] Logs detalhados de integração

**Endpoints Cloudbeds necessários:**
- [ ] Listar tipos de quartos
- [ ] Criar reserva
- [ ] Atualizar reserva
- [ ] Cancelar reserva
- [ ] Buscar disponibilidade
- [ ] Check-in/Check-out

#### Semana 4: Getnet
- [x] Cliente Getnet básico criado
- [ ] Testar autenticação Getnet (sandbox)
- [ ] Implementar geração de link de pagamento
- [ ] Configurar webhook Getnet
- [ ] Testar fluxo completo de pagamento
- [ ] Implementar consulta de status
- [ ] Sistema de conciliação de pagamentos

**Fluxo de pagamento completo:**
```
1. Cliente cria reserva → Status: PENDENTE
2. Sistema gera link Getnet → Salva no banco
3. Cliente paga via link → Webhook recebido
4. Sistema valida pagamento → Atualiza status: CONFIRMADA
5. Notifica Cloudbeds → Atualiza reserva
6. Envia email confirmação → Cliente notificado
```

#### Semana 5: Sistema de Notificações
- [ ] Implementar envio de emails (PHPMailer ou SwiftMailer)
- [ ] Templates de email (confirmação, cancelamento, etc)
- [ ] Notificações para admin
- [ ] Sistema de fila para emails (opcional: Redis)

### Fase 3: Área Administrativa (2 semanas)

#### Semana 6: Dashboard e Relatórios
- [ ] Dashboard com estatísticas em tempo real
- [ ] Gráfico de ocupação
- [ ] Relatório de receitas
- [ ] Calendário de reservas
- [ ] Lista de hóspedes atuais

**Endpoints Dashboard:**
```php
GET /admin/dashboard
GET /admin/relatorios/ocupacao?inicio=2025-01&fim=2025-02
GET /admin/relatorios/financeiro?mes=2025-01
GET /admin/relatorios/reservas?status=confirmada
```

#### Semana 7: Gestão de Preços
- [ ] CRUD de preços sazonais
- [ ] Sistema de descontos
- [ ] Validação de cupons
- [ ] Cálculo automático de valores
- [ ] Regras de preço por temporada

**Regras de negócio:**
```
- Preço base por acomodação
- Preços sazonais (alta temporada, eventos)
- Desconto por quantidade de noites
- Cupons de desconto com limite de uso
- Preço por pessoa extra
```

### Fase 4: Integrações Complementares (1-2 semanas)

#### Semana 8: Kommo CRM
- [ ] Implementar `KommoCRM` client
- [ ] Sincronização de leads
- [ ] Criar negócio automaticamente para reservas
- [ ] Webhooks Kommo
- [ ] Atualização de status de negócios

#### Semana 9: HSystem
- [ ] Implementar `HSystemClient`
- [ ] Sincronização de dados
- [ ] Mapeamento de campos
- [ ] Testes de integração

### Fase 5: Otimizações e Qualidade (1 semana)

#### Semana 10: Performance e Qualidade
- [ ] Implementar cache Redis para queries frequentes
- [ ] Otimizar queries N+1
- [ ] Adicionar índices faltantes no banco
- [ ] Code review e refatoração
- [ ] Documentação de código
- [ ] Testes de carga

**Cache Strategy:**
```php
// Exemplos de cache
- Lista de acomodações: 1 hora
- Configurações do sistema: 24 horas
- Disponibilidade: 5 minutos
- Dashboard stats: 10 minutos
```

### Fase 6: Deploy e Produção (1 semana)

#### Semana 11: Preparação para Produção
- [ ] Configurar servidor de produção
- [ ] Setup Docker Swarm ou Kubernetes
- [ ] Configurar SSL/HTTPS
- [ ] Backup automático do banco
- [ ] Monitoramento (New Relic, Sentry)
- [ ] Logs centralizados (ELK Stack)
- [ ] CI/CD com GitHub Actions

**Checklist de Deploy:**
```bash
# Produção
- [ ] Alterar .env para production
- [ ] Gerar nova JWT_SECRET
- [ ] Configurar SSL
- [ ] Ativar OPcache
- [ ] Desabilitar display_errors
- [ ] Configurar firewall
- [ ] Setup backup diário
- [ ] Monitoramento de erros
- [ ] Documentação de deploy
```

## 🔧 Tarefas Imediatas (Próximos 3 dias)

### Dia 1: Setup Inicial
```bash
# 1. Criar estrutura de pastas
mkdir -p src/App/{Controllers/{Admin,Api,Site},Models,Services,Repositories,Middleware,Integrations/{Cloudbeds,Getnet,Kommo,HSystem}}
mkdir -p storage/{logs,cache,uploads}
mkdir -p tests/{Unit,Integration}
mkdir -p database/{migrations,seeds}

# 2. Copiar todos os arquivos fornecidos
# 3. Iniciar Docker
docker-compose up -d

# 4. Verificar logs
docker-compose logs -f

# 5. Acessar banco e verificar tabelas
docker-compose exec mysql mysql -u hosteldavila_user -p
```

### Dia 2: Implementar AuthController
```php
// src/App/Controllers/Admin/AuthController.php
- Login com validação
- Geração de JWT
- Refresh token
- Logout
- Verificar token
```

### Dia 3: Completar CRUD de Acomodações
```php
// Controllers, Models, Repositories, Services
- Listar acomodações
- Criar acomodação
- Atualizar acomodação
- Deletar acomodação
- Upload de fotos
```

## 📝 Código Faltante Essencial

### 1. AuthController
```php
namespace App\Controllers\Admin;

use Firebase\JWT\JWT;
use App\Controllers\BaseController;

class AuthController extends BaseController
{
    public function login(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        
        // Buscar usuário
        // Validar senha
        // Gerar token JWT
        // Retornar token
    }
}
```

### 2. Middleware de Rate Limiting
```php
namespace App\Middleware;

class RateLimitMiddleware
{
    // Limitar requisições por IP
    // Usar Redis para contador
    // Retornar 429 se exceder
}
```

### 3. Sistema de Migrations
```bash
# Criar sistema de versionamento do banco
php artisan migrate
php artisan migrate:rollback
php artisan migrate:fresh
```

## 🎓 Recursos e Documentação

### Documentação das Integrações
- [Cloudbeds API](https://hotels.cloudbeds.com/api/docs/)
- [Getnet Developers](https://developers.getnet.com.br/)
- [Kommo API](https://www.amocrm.com/developers/content/crm_platform/platform-api)
- [HSystem Docs](https://hsystem.com.br/documentacao)

### Ferramentas Recomendadas
- **Postman/Insomnia**: Testar APIs
- **TablePlus/DBeaver**: Cliente MySQL
- **VSCode**: Editor com extensões PHP
- **xdebug**: Debug PHP
- **PHPStan**: Análise estática
- **Swagger/OpenAPI**: Documentação API

## 🐛 Debug e Troubleshooting

### Ver logs em tempo real
```bash
# Logs da aplicação
tail -f storage/logs/app.log

# Logs Docker
docker-compose logs -f app
docker-compose logs -f nginx
docker-compose logs -f mysql
```

### Acessar container
```bash
docker-compose exec app bash
docker-compose exec mysql bash
```

### Limpar cache
```bash
docker-compose exec app rm -rf storage/cache/*
docker-compose restart redis
```

## 📊 Métricas de Sucesso

### Performance
- [ ] Tempo de resposta < 200ms (endpoints simples)
- [ ] Tempo de resposta < 500ms (endpoints complexos)
- [ ] 99.9% uptime
- [ ] Zero downtime deploys

### Qualidade
- [ ] Cobertura de testes > 70%
- [ ] Zero vulnerabilidades críticas
- [ ] Code review em 100% dos PRs
- [ ] Documentação atualizada

## 🎯 Conclusão

Você agora tem uma estrutura completa e profissional para começar o desenvolvimento. Siga o checklist fase por fase e você terá um sistema robusto e escalável.

**Próximo passo imediato**: Configurar o ambiente Docker e testar o endpoint `/api/v1/health`!

Boa sorte! 🚀