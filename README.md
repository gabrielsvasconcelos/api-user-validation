cat << 'EOF' > README.md
# 🧪 User Validation API

Microserviço Laravel para validação, enriquecimento e análise de risco de dados de usuários via múltiplas APIs externas. Utiliza Redis para cache e fila com geração de relatório em PDF.

---

## 📦 Requisitos

- PHP 8.1 ou superior  
- Composer  
- MySQL  
- Redis  
- Laravel 10+  
- Extensões PHP: `pdo`, `mbstring`, `openssl`, `fileinfo`  
- Node.js (opcional para frontend/Vite)

---
## 🚀 Instalação
```
git clone https://github.com/gabrielsvasconcelos/api-user-validation.git
```
Acesse o diretório e rode os comandos do laravel
```
cd api-user-validation
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```
## ⚙️ Configuração do `.env`

```env
APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

CACHE_DRIVER=redis
QUEUE_CONNECTION=database
SESSION_DRIVER=database

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=uservalidationdb
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=log
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="User Validation"

LOG_CHANNEL=stack
LOG_LEVEL=debug
```
⏱️ Execução da Fila (Para geração dos relatórios)
```
php artisan queue:work
```

📡 Endpoints da API
🔹 POST /api/v1/users/process

Envia os dados do usuário para validação, enriquecimento e processamento.
```
curl --location 'http://localhost:8000/api/v1/users/process' \
--header 'Content-Type: application/json' \
--data-raw '{
    "cpf": "14847244028",
    "cep": "62630000",
    "email": "gabriel@gmail.com"
  }'
```

🔹 GET /api/v1/users/{cpf}

Consulta os dados do usuário com base no CPF.
```
curl --location 'http://localhost:8000/api/v1/users/14847244028'
```

🔹 GET /api/v1/mock/cpf-status/{cpf}

Mock que retorna status aleatório baseado nos últimos dígitos do CPF.

```
curl --location 'http://localhost:8000/api/v1/mock/cpf-status/14847244028'
```

🧾 Funcionalidades

✅ Validação com FormRequest

✅ Retry automático em APIs (ViaCEP, Nationalize)

✅ Cache com Redis TTL 24h

✅ Job assíncrono de análise de risco

✅ Geração de PDF com DomPDF

✅ Simulação de envio de e-mail via Log

✅ Mock de status de CPF

✅ Logs estruturados

✅ Repository pattern

✅ Testes com Pest



📄 Geração de Relatório PDF


O relatório é salvo em:
```
storage/app/reports/report_{cpf}.pdf
```
Contém:

Dados do usuário

Endereço formatado

Status do CPF

Risco: low, medium, high

🧠 Lógica de Risco

CPF negativado + cidade SP ou RJ → high

CPF negativado → medium

Caso contrário → low

📝 Testes com Pest

Rodar todos:

```
php artisan test
```

Testes implementados:

Validação de CPF, CEP, e-mail

Mock de status do CPF

Endpoint /process (com e sem cache)

Endpoint /users/{cpf}

Logs de debug

Cobertura da distribuição de status aleatórios


🧩 Extras Implementados: 

✅ Pest com testes automatizados

✅ Redis com tags e TTL

✅ Queue com retry e fallback

✅ PDF com barryvdh/laravel-dompdf

✅ Logging estruturado e simulação de e-mail

✅ API mockada de CPF status


👨‍💻 Desenvolvedor
Gabriel Vasconcelos

