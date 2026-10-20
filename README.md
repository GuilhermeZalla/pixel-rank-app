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

## 📄 License

This project was developed for study and portfolio purposes.

## 🌐 Demonstration

(soon)