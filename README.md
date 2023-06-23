## Versões utilizadas

- **PHP** (7.2.34)
- **LUMEN** (7.2.2) **Laravel Components** (^7.0)
- **PHPUNIT** (7.5.2)
- **COMPOSER** (2.5.5)

## Instalação e Configuração

Clone o repositório em uma pasta
```
git clone https://github.com/lc-lucascunha/lumen-api-restful.git
```

Acesse a pasta do projeto
```
cd lumen-api-restful/
```

Instale as dependência
```
composer install
```

Faça uma cópia do arquivo de configuração
```
cp .env.example .env
```

Após criar a DATABASE, abra o arquivo .env e definida as configurações da base de dados
```
nano .env
```
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
```

Defina também no .env as configurações do servidor de email
```
MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=your-email@example.com
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@example.com
MAIL_FROM_NAME="${APP_NAME}"
```

Execute a migração da base de dados com os dados inciais
```
php artisan migrate --seed
```

## Iniciando a Aplicação

```
php -S localhost:8080 -t public
```

## Executando o Teste Unitário
```
vendor/bin/phpunit
```
Resultado esperado:
```
............... 15 / 15 (100%)
OK (15 tests, 39 assertions)
```

# Documentação da API

Aqui estão listados todos os endpoints disponíveis na API, juntamente com suas descrições e exemplos de uso.

## Endpoints

### Clientes
- GET `/api/clients` Listar todos os clientes.
- POST `/api/clients` Cadastrar um novo cliente.
- GET `/api/clients/{id}` Exibir detalhes de um cliente específico.
- PUT `/api/clients/{id}` Atualizar os dados de um cliente.
- DELETE `/api/clients/{id}` Excluir um cliente.
- GET `/api/clients/{id}/orders` Listar todos os pedidos associados a um cliente específico.
### Produtos
- GET `/api/products` Listar todos os produtos.
- POST `/api/products` Cadastrar um novo produto.
- GET `/api/products/{id}` Exibir detalhes de um produto específico.
- PUT `/api/products/{id}` Atualizar os dados de um produto.
- DELETE `/api/products/{id}` Excluir um produto.
### Pedidos
- GET `/api/orders` Listar todos os pedidos.
- POST `/api/orders` Cadastrar um novo pedido.
- GET `/api/orders/{id}` Exibir detalhes de um pedido específico.
- PUT `/api/orders/{id}` Atualizar os dados de um pedido.
- DELETE `/api/orders/{id}` Excluir um pedido.

## Clientes

### Cadastrar cliente

Cadastra um novo cliente.

- URL: `/api/clients`
- Método: POST

Exemplo do corpo da requisição:

``` 
{
    "name": "Nome do cliente",
    "email": "cliente@example.com",
    "phone": "1234567890",
    "birthdate": "1990-01-01",
    "zip_code": "12345678",
    "address": "Endereço do cliente",
    "province": "Bairro do cliente",
    "complement": "Complemento do endereço"
}
```

### Atualizar cliente

Atualiza os dados de um cliente existente.

- URL: `/api/clients/{id}`
- Método: PUT

Exemplo do corpo da requisição:

```
{
    "name": "Novo nome do cliente",
    "email": "novocliente@example.com",
    "phone": "9876543210",
    "birthdate": "1990-02-02",
    "zip_code": "87654321",
    "address": "Novo endereço do cliente",
    "province": "Novo estado do cliente",
    "complement": "Novo complemento do endereço"
}
```

## Produtos

### Cadastrar produto

Cadastra um novo produto.

- URL: `/api/products`
- Método: POST

Exemplo do corpo da requisição:

``` 
{
    "name": "Nome do produto",
    "price": 9.99,
    "photo": "URL da foto do produto"
}
```

### Atualizar produto

Atualiza os dados de um produto existente.

- URL: `/api/products/{id}`
- Método: PUT

Exemplo do corpo da requisição:

```
{
    "name": "Novo nome do produto",
    "price": 14.99,
    "photo": "Nova URL da foto do produto"
}
```

## Pedidos

### Cadastrar pedido

Cadastra um novo pedido.

- URL: `/api/orders`
- Método: POST

Exemplo do corpo da requisição:

``` 
{
    "client_id": 5,
    "product_ids": [
        1,
        4
    ]
}
```

### Atualizar pedido

Atualiza os dados de um pedido existente.

- URL: `/api/orders/{id}`
- Método: PUT

Exemplo do corpo da requisição:

```
{
    "client_id": 5,
    "product_ids": [
        2,
        6,
        8
    ]
}
```
