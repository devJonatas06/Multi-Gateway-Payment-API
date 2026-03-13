# Multi-Gateway Payment API

API RESTful desenvolvida em **Laravel 10** para gerenciamento de pagamentos utilizando múltiplos gateways com fallback automático.

📌 **Níveis implementados:** Nível 1 (completo) e Nível 2 (completo)  
❌ **Não implementado:** Nível 3 (roles, TDD, Docker Compose)

---

## 🛠️ Tecnologias

- PHP 8.2+ / Laravel 10
- MySQL / Eloquent ORM
- Laravel HTTP Client
- Docker (para os mocks dos gateways)

---

## 📦 Pré-requisitos

- PHP 8.1+
- Composer
- MySQL
- Docker
- Git

---

## 🔧 Instalação

```bash
# Clone o repositório
git clone https://github.com/seu-usuario/multi-gateway-api.git
cd multi-gateway-api

# Instale as dependências
composer install

# Configure o ambiente
cp .env.example .env
php artisan key:generate
Edite o arquivo .env com suas credenciais do banco:
env

DB_DATABASE=betalent
DB_USERNAME=root
DB_PASSWORD=sua_senha

bash

# Execute as migrations
php artisan migrate

# Inicie o servidor
php artisan serve

A API estará disponível em http://localhost:8000
🐳 Executando os Gateways Mock
bash

docker run -p 3001:3001 -p 3002:3002 matheusprotzen/gateways-mock

Gateways disponíveis:

    Gateway 1: http://localhost:3001

    Gateway 2: http://localhost:3002

🏗️ Arquitetura do Projeto
text

app
├── Http/Controllers
│   ├── ProductController.php      # CRUD de produtos
│   └── TransactionController.php  # Processamento de compras
├── Models
│   ├── Client.php
│   ├── Product.php
│   ├── Transaction.php
│   └── TransactionProduct.php
└── Services
    └── PaymentService.php          # Lógica de pagamento e fallback

📊 Estrutura do Banco de Dados

clients: id, name, email
products: id, name, amount (em centavos)
transactions: id, client_id, gateway, external_id, status, amount, card_last_numbers
transaction_products: transaction_id, product_id, quantity (tabela pivot)
🌐 Rotas da API
📦 Produtos

Criar produto
POST /api/products
json

{
    "name": "Mouse Gamer",
    "amount": 15000
}

Resposta:
json

{
    "id": 1,
    "name": "Mouse Gamer",
    "amount": 15000
}

Campo	Tipo	Validação
name	string	obrigatório, máximo 255
amount	integer	obrigatório, mínimo 1 (centavos)
💳 Compras

Realizar compra
POST /api/purchase
json

{
    "products": [
        { "id": 1, "quantity": 2 },
        { "id": 2, "quantity": 1 }
    ],
    "name": "João Silva",
    "email": "joao@email.com",
    "cardNumber": "5569000000006063",
    "cvv": "010"
}

Resposta sucesso:
json

{
    "success": true,
    "gateway": "gateway1",
    "data": {
        "id": "transacao_123456",
        "status": "approved"
    }
}

Resposta fallback (Gateway 2):
json

{
    "success": true,
    "gateway": "gateway2",
    "data": {
        "id": "transacao_789012",
        "status": "approved"
    }
}

Resposta erro:
json

{
    "success": false,
    "message": "Todos os gateways falharam"
}

Campo	Tipo	Validação
products	array	obrigatório, mínimo 1
products[].id	integer	obrigatório, deve existir
products[].quantity	integer	obrigatório, mínimo 1
name	string	obrigatório
email	string	obrigatório, formato email
cardNumber	string	obrigatório, 16 dígitos
cvv	string	obrigatório, 3-4 dígitos
🔄 Fluxo de Pagamento

    Cliente envia produtos + dados do comprador

    Sistema calcula valor total (produto × quantidade)

    Cliente é criado/recuperado pelo email

    Tenta processar no Gateway 1

    Se falhar, tenta Gateway 2 automaticamente

    Se sucesso: registra transação + produtos no banco

    Retorna resposta com gateway utilizado

🧪 Testes de Fallback
CVV	Gateway 1	Gateway 2	Resultado
010	✅ Sucesso	-	Aprovado (Gateway 1)
100	❌ Falha	✅ Sucesso	Aprovado (Gateway 2)
200	❌ Falha	❌ Falha	Rejeitado

Cartão válido para testes: 5569000000006063
🔐 Autenticação dos Gateways

Gateway 1: Token JWT (obtido automaticamente pelo sistema)

Gateway 2: Headers fixos
text

Gateway-Auth-Token: tk_f2198cc671b5289fa856
Gateway-Auth-Secret: 3d15e8ed6131446ea7e3456728b1211f

🚀 Melhorias Futuras

    Implementar autenticação JWT

    CRUD completo de gateways

    Endpoints de listagem e detalhes

    Sistema de reembolso

    Testes automatizados (PHPUnit)

    Dockerizar aplicação completa

📝 Autor

Jonatas
GitHub: https://github.com/devJonatas06
Projeto desenvolvido para teste técnico BeTalent.

