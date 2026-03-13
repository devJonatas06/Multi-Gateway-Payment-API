# Multi-Gateway Payment API

API RESTful desenvolvida em **Laravel** para gerenciamento de pagamentos utilizando múltiplos gateways.
O sistema realiza compras, calcula automaticamente o valor com base nos produtos selecionados e tenta processar o pagamento utilizando **dois gateways externos**, respeitando uma ordem de prioridade.

Caso o primeiro gateway falhe, o sistema automaticamente tenta o segundo gateway.

---

# Tecnologias Utilizadas

* PHP 8+
* Laravel 10+
* MySQL
* Eloquent ORM
* Docker (para execução dos mocks de gateway)
* HTTP Client do Laravel

---

# Funcionalidades Implementadas

### Compras

* Criação de compras utilizando múltiplos produtos
* Cálculo automático do valor total no backend
* Integração com gateways de pagamento externos
* Fallback automático entre gateways

### Produtos

* Criação de produtos
* Definição de preço em centavos

### Clientes

* Criação automática do cliente ao realizar uma compra

### Transações

* Registro da transação no banco de dados
* Associação de produtos à transação
* Armazenamento do gateway utilizado
* Armazenamento dos últimos 4 dígitos do cartão

---

# Arquitetura do Projeto

A aplicação foi estruturada utilizando uma separação básica de responsabilidades.

```
app
 ├── Http
 │   └── Controllers
 │        ├── ProductController
 │        └── TransactionController
 │
 ├── Models
 │        ├── Client
 │        ├── Gateway
 │        ├── Product
 │        ├── Transaction
 │        └── TransactionProduct
 │
 └── Services
          └── PaymentService
```

### Controllers

Responsáveis por receber requisições HTTP e validar os dados.

### Services

Contém a lógica de negócio principal do sistema, incluindo:

* cálculo do valor da compra
* integração com gateways
* fallback entre gateways

### Models

Representam as tabelas do banco de dados utilizando Eloquent ORM.

---

# Estrutura do Banco de Dados

### clients

| campo | descrição                |
| ----- | ------------------------ |
| id    | identificador do cliente |
| name  | nome do cliente          |
| email | email do cliente         |

---

### products

| campo  | descrição         |
| ------ | ----------------- |
| id     | identificador     |
| name   | nome do produto   |
| amount | valor em centavos |

---

### transactions

| campo             | descrição                   |
| ----------------- | --------------------------- |
| id                | identificador               |
| client_id         | cliente da transação        |
| gateway           | gateway utilizado           |
| external_id       | id retornado pelo gateway   |
| status            | status da transação         |
| amount            | valor total                 |
| card_last_numbers | últimos 4 dígitos do cartão |

---

### transaction_products

Tabela pivot que relaciona produtos com transações.

| campo          | descrição  |
| -------------- | ---------- |
| transaction_id | transação  |
| product_id     | produto    |
| quantity       | quantidade |

---

# Instalação do Projeto

Clone o repositório:

```
git clone https://github.com/seuusuario/multi-gateway-api.git
```

Entre no diretório:

```
cd multi-gateway-api
```

Instale as dependências:

```
composer install
```

Configure o arquivo `.env`:

```
cp .env.example .env
```

Configure as credenciais do banco MySQL no `.env`.

Exemplo:

```
DB_DATABASE=betalant
DB_USERNAME=root
DB_PASSWORD=senha
```

Gere a chave da aplicação:

```
php artisan key:generate
```

Execute as migrations:

```
php artisan migrate
```

Inicie o servidor:

```
php artisan serve
```

---

# Executando os Gateways Mock

Para rodar os gateways fornecidos no teste:

```
docker run -p 3001:3001 -p 3002:3002 matheusprotzen/gateways-mock
```

Gateway 1 ficará disponível em:

```
http://localhost:3001
```

Gateway 2 ficará disponível em:

```
http://localhost:3002
```

---

# Rotas da API

## Criar Produto

POST

```
/api/products
```

Body:

```
{
 "name": "Mouse",
 "amount": 1000
}
```

Resposta:

```
{
 "id": 1,
 "name": "Mouse",
 "amount": 1000
}
```

---

# Realizar Compra

POST

```
/api/purchase
```

Body:

```
{
 "products": [
   { "id": 1, "quantity": 2 },
   { "id": 2, "quantity": 1 }
 ],
 "name": "Jonatas",
 "email": "jonatas@email.com",
 "cardNumber": "5569000000006063",
 "cvv": "010"
}
```

Resposta:

```
{
 "success": true,
 "gateway": "gateway1",
 "data": {
   "id": "transaction-id"
 }
}
```

---

# Fluxo de Pagamento

1. Cliente envia requisição de compra
2. Backend calcula valor total baseado nos produtos
3. Sistema tenta processar no Gateway 1
4. Se o Gateway 1 falhar, o sistema tenta o Gateway 2
5. Se algum gateway aprovar a transação, a compra é registrada no banco
6. Produtos são associados à transação

---

# Testes de Fallback

Exemplo de teste para validar fallback entre gateways.

Gateway 1 falha com:

```
cvv = 100
```

Gateway 2 deve processar a transação.

---

# Autor https://github.com/devJonatas06

Desenvolvido como parte de teste técnico de back-end para a empresa betalent.
