# PixelRank
![PixelRank Screenshot](imagem)

A game review platform inspired by Letterboxd, where users can rate games,
write reviews, comment, and discover new reviews from the community.

## 🎯 Objective

PixelRank aims to offer a platform focused on game reviews, inspired by the Letterboxd experience for movies,
allowing any user to publish, share, and discover game reviews.

## ✨ Features

- Review publishing
- Comment system
- Game search using the IGDB API
- User dashboard
- User registration and authentication
- Filters and sorting

## 🚧 Status

Project under development.

## 🛠 Tecnologies

### Backend

- PHP
- Laravel
- Queues & Jobs

### Frontend

- Blade
- Tailwind CSS
- DaisyUI
- JavaScript

### Database

- MySQL

### APIs

- IGDB API

### Tools

- Git
- Docker (in development)

## 🚀 Install

### Pré-requisitos
- PHP 8.3+
- Composer
- Node.js 18+
- MySQL (ou SQLite para testes)
- Git

### Instalação (Windows)

```powershell
git clone https://github.com/GuilhermeZalla/pixel-rank-app.git
cd pixel-rank-app
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
npm install
npm run build
php artisan serve
```

### Instalação (Linux/Mac)

```bash
git clone https://github.com/GuilhermeZalla/pixel-rank-app.git
cd pixel-rank-app
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
npm install
npm run build
php artisan serve
```

### Rodar os testes

```bash
php artisan test
```

## 📸 Screenshots

(under construction)

### Home
![](docs/images/home.jpg)

![](docs/images/home-menu.jpg)


### Login & Register

![](docs/images/login.jpg)

![](docs/images/register.jpg)

### Review page

![](docs/images/review-show.jpg)


### Create review

![](docs/images/review-create-1.jpg)

![](docs/images/review-create-2.jpg)


### User dashboard

![](docs/images/dashboard-profile.jpg)

![](docs/images/dashboard-reviews.jpg)

![](docs/images/dashboard-comments.jpg)


....

## 🏗 Architecture

The project follows Laravel's MVC architecture.

Business logic is encapsulated within Services, while Policies handle authorization.
Notifications are responsible for dispatching notifications, and the integration with
the IGDB API is encapsulated in a dedicated service layer to keep controllers thin.

## 📂 Project's Structure

The project's core structure follows Laravel's standard organization,
with clear separation of concerns through Services, Policies, and Notifications.

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

### Version 1.0 (current)

- [x] Reviews
- [x] Comments
- [ ] Comment interaction
- [x] User creation and authentication
- [x] User dashboard
- [ ] Recommendation system
- [ ] Notifications
- [x] Tests

### Version 2.0 (future)

- [ ] Follower system
- [ ] Private messaging system
- [ ] Internationalization (PT-BR / EN)
- [ ] Docker
- [ ] Deploy

## 🔐 Security

- Authorization using Laravel Policies
- Validation via Form Requests
- Protection against unauthorized actions

## 🚀 Produção

Para rodar em produção, defina as seguintes variáveis de ambiente:

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://seudominio.com`
- `SESSION_SECURE_COOKIE=true`
- `SESSION_ENCRYPT=true`

Execução em produção:

```bash
# Cache de configuração, rotas e views
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Link do storage público
php artisan storage:link

# Fila e agendador (execute em processos separados)
php artisan queue:work
php artisan schedule:work

# E-mail SMTP real (configure MAIL_* no .env)
```

## 🌐 Idiomas

O site suporta dois idiomas: **Português (Brasil)** e **Inglês (Estados Unidos)**.

### Como trocar o idioma
Acesse qualquer página, use o seletor de idioma no cabeçalho (navbar) e escolha `Português (BR)` ou `English (US)`. A escolha é salva na sessão e em um cookie.

### Como adicionar um novo idioma
1. Copie a pasta `lang/en` para `lang/<codigo>` (ex.: `lang/es`).
2. Traduza todos os valores das chaves nos arquivos copiados.
3. Adicione o novo código à lista de locales permitidos em `app/Http/Middleware/SetLocale.php`.
4. Adicione a opção no seletor de idioma em `resources/views/components/layout.blade.php`.
5. Rode o teste de paridade de chaves: `php artisan test --filter=LocaleKeysParityTest`.

## 📄 License

This project was developed for study and portfolio purposes.

## 🌐 Demonstration

(soon)