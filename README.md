# PixelRank

![Tela inicial do PixelRank](docs/images/home.jpg)

O PixelRank é uma plataforma de avaliações de jogos inspirada na experiência do Letterboxd. Os usuários podem avaliar títulos com notas de 0 a 10, escrever resenhas detalhadas com indicação de prós e contras, comentar em publicações da comunidade, filtrar o feed e gerenciar seu perfil e histórico em um dashboard dedicado. É um projeto de portfólio desenvolvido com Laravel, Blade, Tailwind CSS, DaisyUI e Alpine.js.

## Features da aplicação

- Criar conta, autenticar-se, verificar e-mail e redefinir senha com segurança e proteção contra força bruta (rate limiting).
- Publicar, editar e excluir avaliações de jogos com nota, recomendação (Recomendado, Não recomendado, Misto ou Essencial), lista de prós e contras e aviso opcional de spoiler.
- Buscar jogos em tempo real com integração à API do IGDB/Twitch (capas, plataformas, sinopse e data de lançamento), contando com degradação graciosa quando a API externa não estiver configurada.
- Explorar o feed da comunidade com filtros rápidos (populares, mais bem avaliados, menores notas, mais antigos, quentes e por recomendação) e alternância para ocultar spoilers.
- Comentar nas avaliações e acompanhar as discussões em tempo real.
- Painel pessoal do usuário (Dashboard) organizado em abas: edição de perfil (dados, foto de avatar e bio), listagem das próprias avaliações e histórico de comentários.
- Internacionalização completa em Português (Brasil) e Inglês (EUA), com alternância de idioma salva na sessão e em cookie.

## Tecnologias

- PHP 8.4.27 e Laravel 13
- Blade, Tailwind CSS 4 e DaisyUI 5
- Alpine.js 3 e Vite 8
- Pest 4 para os testes automatizados
- MySQL por padrão; SQLite em memória para os testes automatizados ou para desenvolvimento local simplificado
- Integração resiliente com a API Twitch / IGDB com cache de credenciais OAuth

## Rodar na sua máquina

Você precisa de PHP 8.4 com as extensões do Laravel, Composer, Node.js/npm e Git. Com SQLite, não é necessário instalar um servidor de banco de dados externo.

```bash
git clone https://github.com/GuilhermeZalla/pixel-rank-app.git
cd pixel-rank-app
composer install
npm install
```

Copie `.env.example` para `.env` (no PowerShell, use `Copy-Item .env.example .env`), gere a chave e prepare o banco:

```bash
php artisan key:generate
php artisan migrate --seed
```

O arquivo `.env.example` vem pré-configurado para MySQL. Para usar SQLite no ambiente local sem instalar MySQL, defina `DB_CONNECTION=sqlite` no `.env`, comente as outras variáveis `DB_*` e crie o arquivo antes de migrar (`type nul > database/database.sqlite` no Windows ou `touch database/database.sqlite` no Linux/Mac).

*(Opcional)* Se quiser exibir metadados reais da API de jogos (Twitch/IGDB), preencha no `.env` as variáveis `TWITCH_CLIENT_ID`, `TWITCH_CLIENT_SECRET`, `GAME_API_URL` e `GAME_API_KEY`. Se deixar em branco, o site funcionará normalmente em modo degradado, sem erros.

Inicie a aplicação e o Vite:

```bash
composer run dev
```

Abra [http://localhost:8000](http://localhost:8000). O seeder cria uma conta de teste inicial `test@example.com` (senha `password`).

## Rodar as verificações

```bash
php artisan test
npm run build
php vendor/bin/pint --test
```

A suíte conta com 64 testes automatizados em Pest cobrindo autenticação, autorização via Policies, rate limiting, paridade de idiomas (PT-BR/EN-US), resiliência da API de jogos e as telas do dashboard.

## Como o app é organizado

As rotas ficam em `routes/web.php` e encaminham as requisições para controllers em `app/Http/Controllers/`. As validações de entrada ficam encapsuladas em Form Requests (`app/Http/Requests/`) e as regras de autorização de recursos ficam protegidas por Policies (`app/Policies/`). Models e migrations estruturam as entidades no banco (usuários, avaliações, comentários e prós/contras).

A camada de serviços em `app/Services/GameApiService.php` isola a comunicação externa com a API do IGDB/Twitch, gerenciando cache de token OAuth e convertendo falhas de rede em exceções de domínio seguras para evitar erros 500 no front-end.

As views Blade ficam em `resources/views/`, divididas entre telas principais (`reviews/`, `users/`, `auth/`) e componentes reutilizáveis em `resources/views/components/` (cards de avaliação, badges, formulários, layout base e modais). A estilização é construída com Tailwind CSS e componentes DaisyUI, enquanto micro-interações acessíveis (como abas do dashboard e alertas) utilizam Alpine.js.

A internacionalização fica em `lang/pt_BR/` e `lang/en/`, com paridade total de chaves e middleware `SetLocale` para controle de sessão/cookie. Notificações e e-mails usam Jobs e Mailables (`app/Jobs/` e `app/Mail/`).

## Demonstração

### Tela Inicial

![Tela inicial do PixelRank](docs/images/home.jpg)

### Menu e Navegação

![Menu inicial](docs/images/home-menu.jpg)

### Avaliação e Detalhes

![Página de avaliação](docs/images/review-show.jpg)

### Criação de Avaliação

![Criação de avaliação - Etapa 1](docs/images/review-create-1.jpg)

![Criação de avaliação - Etapa 2](docs/images/review-create-2.jpg)

### Dashboard do Usuário

![Perfil no dashboard](docs/images/dashboard-profile.jpg)

![Minhas avaliações no dashboard](docs/images/dashboard-reviews.jpg)

![Meus comentários no dashboard](docs/images/dashboard-comments.jpg)

### Login e Cadastro

![Tela de login](docs/images/login.jpg)

![Tela de cadastro](docs/images/register.jpg)

## Deploy

Para rodar em ambiente de produção, configure as variáveis essenciais no `.env` (`APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://seudominio.com`, `SESSION_SECURE_COOKIE=true`), execute os comandos de cache (`php artisan config:cache`, `php artisan route:cache`, `php artisan view:cache`, `php artisan storage:link`) e mantenha os workers de fila e agendador ativos (`php artisan queue:work` e `php artisan schedule:work`).

O app ainda não possui uma URL pública de produção.
