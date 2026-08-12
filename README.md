# PixelRank
![PixelRank Screenshot](imagem)

Uma plataforma de reviews de jogos inspirada no Letterboxd, onde os usuários podem
avaliar jogos, escrever análises, comentar e descobrir novas reviews da comunidade.

## 🎯 Objetivo

O objetivo do PixelRank é oferecer uma plataforma focada em reviews de jogos,
inspirada na experiência do Letterboxd para filmes, permitindo que qualquer
usuário publique, compartilhe e descubra reviews de jogos.

## ✨ Funcionalidades

- Publicação de reviews
- Sistema de comentários
- Busca de jogos utilizando a API da IGDB
- Dashboard do usuário
- Cadastro e autenticação de usuários
- Filtros e ordenação

## 🚧 Status

Projeto em desenvolvimento.

## 🛠 Tecnologias

### Backend

- PHP
- Laravel
- Queues & Jobs

### Frontend

- Blade
- Tailwind CSS
- DaisyUI
- JavaScript

### Banco de dados

- MySQL

### APIs

- IGDB API

### Ferramentas

- Git
- Docker (em desenvolvimento)

## 🚀 Instalação

```bash
git clone ...

cd PixelRank

composer install

cp .env.example .env

php artisan key:generate

php artisan migrate

npm install

npm run dev

php artisan serve
```

## 📸 Screenshots

(em breve)

### Home
(docs/images/home.jpg)

(docs/images/screenshot.png)

....
### Review page
....

### Create review
....

### User profile
....

## 🏗 Arquitetura

O projeto segue a arquitetura MVC do Laravel.

As regras de negócio ficam concentradas em Services, enquanto Policies são utilizadas
para autorização. Notifications são responsáveis pelo envio de notificações e a
integração com a API da IGDB é encapsulada em uma camada de serviços para manter
os controllers enxutos.

## 📂 Estrutura do projeto

A estrutura principal do projeto segue a organização padrão do Laravel,
com separação de responsabilidades através de Services, Policies e Notifications.

```text
PixelRank/
│
├── app/
│ ├── Http/
│ │ ├── Controllers/
│ │ ├── Requests/
│ │ └── Middleware/
│ │
│ ├── Models/
│ ├── Services/
│ ├── Policies/
│ └── Notifications/
│
├── database/
│ ├── migrations/
│ └── seeders/
│
├── resources/
│ ├── views/
│ ├── css/
│ └── js/
│
├── routes/
│ └── web.php
│
├── tests/
│
├── .env.example
├── composer.json
└── package.json
```

## 🗺 Roadmap

- [x] Reviews
- [x] Comentários
- [x] Criação e autenticação de usuários
- [x] Dashboard do usuário
- [ ] Notificações
- [ ] Sistema de seguidores
- [ ] Sistema de mensagens privadas
- [ ] Sistema de recomendação
- [ ] Internacionalização (PT-BR / EN)
- [ ] Docker
- [ ] Testes automatizados
- [ ] Deploy

## 🔐 Segurança

- Autorização utilizando Laravel Policies
- Validação através de Form Requests
- Proteção contra ações não autorizadas

## 📄 Licença

Este projeto foi desenvolvido para fins de estudo e portfólio.

## 🌐 Demonstração

(em breve)