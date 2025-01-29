## Instruções para a aplicação

A aplicação foi construída em Laravel, sendo necessária a utilização de PHP, pelo menos a partir da versão 8.2, para rodar a aplicação. Será necessário ainda possuir o composer para realizar a instalação das dependências. Uma vez instalado o PHP e o Composer, rode o comando a seguir para instalar as dependências da aplicação:
```shell
composer install
```
1. Após instalação das dependências, você deve copiar o arquivo `.env.example` para um arquivo `.env`, para configuração dos dados da aplicação. Lembro de alterar o arquivo .env para corresponder ao seu ambiente de desenvolvimento.
Uma vez configurado o arquivo `.env`, execute o comando a seguir para gerar a chave secreta a ser utilizada para o token jwt
```shell
php artisan jwt:secret
```
2. Após geração da chave, realize as migrações do banco de dados, para possuir os dados necessários para a aplicação, conforme comando abaixo:
```shell
php artisan migrate --seed
```
Não esquecer da opção `--seed` para permitir que o banco de dados seja populado com dados básicos necessários.

3. Após criação do banco de dados, rode o comando abaixo para permitir que o upload de arquivos de imagem de perfil seja salvo na aplicação e fique acessível externamente.
```shell
php artisan storage:link
```
4. Por último, suba a aplicação com o comando abaixo:
```shell
php artisan serve
```
A aplicação deve subir no endereço `http://localhost:8000`
Você pode acessar uma documentação da api no endereço [`http://localhost:8000/docs/api`](http://localhost:8000/docs/api)
