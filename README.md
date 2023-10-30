# Hotel Faker

## Sobre o Projeto:
Este projeto trata-se de uma API de um sistema de gerenciamento de um hotel fantasia chamado Hotel Faker.
A API pode registrar usuários, registrar colaboradores, fazer gerenciamento de hospedagem (reservas, cancelamentos, check-ins, check-outs, etc.) e administrar permissões de acesso.

O projeto está hospedado na SquareCloud (https://squarecloud.app/home).
Vale ressaltar que todos os dados hospedados no banco de dados são dados de teste, ou seja, nomes, e-mails e CPFs fakes foram utilizados no cadastro de usuários.
Não há dados de pessoas reais registrados, e não se recomenda o uso de dados reais para testes.

Recomendamos o uso de um software como o Postman (https://www.postman.com/) ou o Insomnia (https://insomnia.rest/) para testar as rotas da API.

## Documentação

### URL Base:
#### `https://hotel-faker-api.squareweb.app/api`

### Rotas:
1. **Authentication Routes**
    - `POST /login`: Esta rota é usada para fazer logout de um usuário. Ela usa o método `logout` do `AuthController`.
    - `POST /logout`: Esta rota é usada para fazer logout de um usuário. Ela usa o método `logout` do `AuthController`.
    - `POST /refresh`: Esta rota é usada para atualizar a sessão de um usuário. Ela usa o método `refresh` do `AuthController`.
    - `POST /register`: Esta rota é usada para registrar um novo usuário. Os parâmetros são: name, email, cpf (11 caracteres) e password (min. 6 caracteres). Ela usa o método `register` do `AuthController`.

2. **Rotas de Usuários** (Requer Token de Autenticação)
    - `GET /users/list`: Esta rota lista todos os usuários. Ela usa o método `list` do `UserController`.
    - `GET /users/show/{id}`: Esta rota mostra detalhes de um usuário específico com base em seu ID. Ela usa o método `show` do `UserController`.
    - `POST /users/update/{id}`: Esta rota atualiza detalhes de um usuário específico com base em seu ID. Os parâmetros são: name, email, cpf (11 caracteres) e flag_collaborator (0 ou 1). Ela usa o método `update` do `UserController`.
    - `POST /users/delete/{id}`: Esta rota exclui um usuário específico com base em seu ID. Ela usa o método `destroy` do `UserController`.

3. **Rotas de Colaboradores** (Requer Token de Autenticação)
    - `GET /collaborators/list`: Esta rota lista todos os colaboradores. Ela usa o método `list` do `CollaboratorsController`.
    - `POST /collaborators/store`: Esta rota cria um novo colaborador. Os parâmetros são: name, email, password, cpf (11 caracteres) e flag_permissions (0 ou 1). Apenas o email é obrigatório se o usuário já existir, outros parâmetros são necessários se o usuário ainda não existir. Ela usa o método `store` do `CollaboratorsController`.
    - `GET /collaborators/show/{id}`: Esta rota mostra detalhes de um colaborador específico com base em seu ID. Ela usa o método `show` do `CollaboratorsController`.
    - `POST /collaborators/delete/{id}`: Esta rota exclui um colaborador específico com base em seu ID. Ela usa o método `destroy` do `CollaboratorsController`.

### Observações:
- Todas as rotas retornam uma JsonResponse.
- As rotas que necessitam de autenticação utilizam um Bearer Token devolvido pela API no login do usuário.
