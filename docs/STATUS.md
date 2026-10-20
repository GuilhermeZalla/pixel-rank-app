# Status do projeto PixelRank

## Stack detectada

- Laravel **13.20.0** (versÃ£o exata no `composer.lock`; `composer.json` permite `^13.8`).
- PHP **8.5.5 CLI** no ambiente atual; o `composer.json` requer PHP `^8.3`.
- Banco padrÃ£o: **MySQL** (`.env.example`); os testes estÃ£o configurados para **SQLite em memÃ³ria** (`phpunit.xml`).
- Front-end: **Blade**, **Tailwind CSS 4**, **DaisyUI 5**, **Alpine.js 3** e **Vite 8**. O Vite compila `resources/css/app.css` e `resources/js/app.js`.
- AutenticaÃ§Ã£o: implementaÃ§Ã£o prÃ³pria no projeto (`LoginController`, `LogoutController`, `RegisterController`); nÃ£o foram encontrados Breeze, Jetstream, Fortify, Sanctum ou Laravel UI no `composer.lock`.
- PermissÃµes: hÃ¡ diretÃ³rio `app/Policies`; nÃ£o foi encontrado `spatie/laravel-permission` no `composer.lock`.
- Filas: driver padrÃ£o **database** (`config/queue.php` e `.env.example`); o projeto inclui `app/Jobs`.
- Testes: Pest 4; existem arquivos de exemplo em `tests/Unit` e `tests/Browser`. `phpunit.xml` tambÃ©m aponta para `tests/Feature`, que nÃ£o contÃ©m testes no momento.
- Ferramenta de formataÃ§Ã£o: Laravel Pint estÃ¡ listado em `require-dev`.

## Estado inicial

- Data da verificaÃ§Ã£o: 2026-10-19.
- Comando executado: `php artisan test`.
- Resultado: **nÃ£o foi possÃ­vel iniciar**. O diretÃ³rio `vendor`/arquivo `vendor/autoload.php` nÃ£o existe; o Artisan encerrou antes de carregar a aplicaÃ§Ã£o. Nenhum teste passou ou falhou individualmente. O PHP CLI tambÃ©m exibiu um aviso de inicializaÃ§Ã£o porque a extensÃ£o `pdo_firebird` configurada nÃ£o foi encontrada.
- Nenhum cÃ³digo de aplicaÃ§Ã£o foi alterado nesta etapa.

## Fases e progresso

- **Tarefa inicial â€” detecÃ§Ã£o e estado base:** concluÃ­da. Stack identificada; execuÃ§Ã£o de testes bloqueada pela ausÃªncia de `vendor`.
- **Fase 1 â€” auditoria (somente leitura, exceto este arquivo):** concluÃ­da em 2026-10-19 por inspeÃ§Ã£o estÃ¡tica. RelatÃ³rio e prioridades abaixo; aguardar revisÃ£o do usuÃ¡rio antes da Fase 2.
- **Fase 2 â€” seguranÃ§a e bugs (grupo A aprovado):** implementaÃ§Ã£o concluÃ­da nos grupos 1 a 6; testes de regressÃ£o/autorizaÃ§Ã£o adicionados. A validaÃ§Ã£o em runtime continua pendente pela ausÃªncia de `vendor/autoload.php`.
- **Fase 3 â€” internacionalizaÃ§Ã£o PT-BR/EN-US:** infraestrutura e grupos de autenticaÃ§Ã£o, layout/navegaÃ§Ã£o e telas principais concluÃ­dos; pendem e-mails/notificaÃ§Ãµes/erros, formataÃ§Ã£o por idioma e verificaÃ§Ãµes finais.
- **Fase 4 â€” estilo e responsividade:** corrigir apenas defeitos do grupo B aprovado, preservando identidade visual; conferir pÃ¡ginas nos dois idiomas e build quando aplicÃ¡vel.
- **Fase 5 â€” preparaÃ§Ã£o da v1.0:** finalizar documentaÃ§Ã£o em portuguÃªs, checklist de produÃ§Ã£o, changelog e checklist de release; sugerir commit/tag sem executÃ¡-los.

## DecisÃµes e limites de trabalho

- Preservar identidade visual, arquitetura, rotas pÃºblicas, nomes de tabelas/colunas, migrations existentes e idioma padrÃ£o atual.
- NÃ£o adicionar, remover ou atualizar dependÃªncias sem aprovaÃ§Ã£o. NÃ£o alterar dados reais nem executar comandos destrutivos no banco.
- Trabalhar uma fase por vez; ao concluir, atualizar este status e parar para aguardar o prÃ³ximo pedido/aprovaÃ§Ã£o.
- Escrever arquivos em UTF-8 sem BOM; nÃ£o fazer commit.

## PendÃªncias

- Instalar/restaurar dependÃªncias do projeto quando autorizado/necessÃ¡rio para executar os testes (`vendor/autoload.php` estÃ¡ ausente). A tarefa inicial nÃ£o autoriza instalar pacotes.
- Executar verificaÃ§Ãµes finais de PHP/lint, revisÃ£o manual das rotas/views e, quando as dependÃªncias estiverem disponÃ­veis, rodar a suÃ­te de testes.
- GlossÃ¡rio de domÃ­nio e traduÃ§Ãµes ambÃ­guas: ainda nÃ£o levantados; registrar durante a Fase 3.

## Fase 1 â€” resumo da auditoria

### Achados

- **Alta â€” XSS ao inserir comentÃ¡rios:** `resources/views/reviews/show.blade.php` concatena texto controlado por usuÃ¡rios em `insertAdjacentHTML`. Um comentÃ¡rio pode inserir marcaÃ§Ã£o/script que executa no navegador de quem abre a avaliaÃ§Ã£o. CorreÃ§Ã£o proposta: criar nÃ³s DOM e usar `textContent` para os valores.
- **Alta â€” valor de rota dentro de expressÃ£o Alpine:** `resources/views/users/edit.blade.php` interpola `menu` em uma expressÃ£o JavaScript dentro de `x-data`. Escapamento HTML nÃ£o Ã© garantia suficiente nesse contexto. CorreÃ§Ã£o proposta: serializar o valor para JavaScript com mecanismo prÃ³prio do Blade e limitar os valores aceitos.
- **MÃ©dia â€” atribuiÃ§Ã£o em massa global:** `app/Providers/AppServiceProvider.php` chama `Model::unguard()`, desativando a proteÃ§Ã£o `$fillable` declarada em `app/Models/User.php`. Hoje alguns controllers montam arrays explÃ­citos, mas a proteÃ§Ã£o fica inativa para toda a aplicaÃ§Ã£o. CorreÃ§Ã£o proposta: retirar o unguard global e declarar campos permitidos por modelo.
- **MÃ©dia â€” sem limite de tentativas:** nÃ£o hÃ¡ `throttle` aparente nas rotas de login/registro nem no endpoint pÃºblico que consulta IGDB (`/games/search`). Isso facilita tentativas de senha e abuso da cota da API. CorreÃ§Ã£o proposta: limites por IP/identidade nas rotas adequadas.
- **MÃ©dia â€” ediÃ§Ã£o de perfil nÃ£o conclui:** `app/Http/Controllers/UserController.php` usa regras `unique` sem ignorar o prÃ³prio usuÃ¡rio, preenche campos de senha com o hash existente e termina com `$user->save;` sem chamar o mÃ©todo. A ediÃ§Ã£o tende a falhar na validaÃ§Ã£o e, mesmo se passar, nÃ£o persiste. CorreÃ§Ã£o proposta: ignorar o prÃ³prio registro nas regras, deixar senha vazia quando nÃ£o for trocada e salvar o modelo.
- **MÃ©dia â€” rotas sem implementaÃ§Ã£o ou views:** `UserController::index/show` apontam para `users.index` e `users.show`, que nÃ£o existem; `ReviewController::edit/update` e `CommentController::index/update` estÃ£o vazios apesar de rotas ativas. Essas URLs podem terminar em erro em vez de resposta Ãºtil. CorreÃ§Ã£o proposta: completar ou retirar apenas apÃ³s confirmar o comportamento pretendido.
- **MÃ©dia â€” testes de browser incoerentes com o registro:** `tests/Browser/ExampleTest.php` omite nickname e confirmaÃ§Ã£o de senha, ambos exigidos, e espera redirecionamento diferente do controller. A cobertura de feature/autorizaÃ§Ã£o estÃ¡ ausente; hÃ¡ apenas um teste unitÃ¡rio trivial e esse exemplo de browser.
- **MÃ©dia â€” erro JavaScript global e no estado vazio:** `resources/views/components/layout.blade.php` liga um listener a `#review` em toda pÃ¡gina, embora esse elemento nÃ£o exista em vÃ¡rias delas. `resources/views/reviews/index.blade.php` acessa o checkbox de spoilers mesmo quando a lista vazia nÃ£o o renderiza. CorreÃ§Ã£o proposta: proteger inicializaÃ§Ãµes por existÃªncia do elemento.
- **MÃ©dia â€” dashboard pode fazer consultas repetidas:** `UserController::edit` pagina comentÃ¡rios sem carregar previamente `review` e `user`, relaÃ§Ãµes acessadas por `components/comment.blade.php`. CorreÃ§Ã£o proposta: eager loading das relaÃ§Ãµes usadas.
- **MÃ©dia â€” formulÃ¡rios/fluxos faltantes para produÃ§Ã£o:** nÃ£o hÃ¡ rotas de redefiniÃ§Ã£o de senha nem verificaÃ§Ã£o de e-mail; confirmar se recuperaÃ§Ã£o de conta e verificaÃ§Ã£o sÃ£o requisitos da v1.0. O mail atual Ã© apenas uma mensagem de boas-vindas.
- **MÃ©dia â€” responsividade estÃ¡tica:** layout do cabeÃ§alho e busca usa larguras fixas; filtros ficam em linha; dashboard e formulÃ¡rio de avaliaÃ§Ã£o usam `flex-row` e larguras sem regras mÃ³veis claras. Risco de conteÃºdo comprimido/rolagem em telas estreitas. CorreÃ§Ã£o proposta: ajustes localizados nas classes existentes, apÃ³s conferir em navegador nos tamanhos pedidos.
- **MÃ©dia â€” configuraÃ§Ã£o de exemplo Ã© local:** `.env.example` traz `APP_ENV=local`, `APP_DEBUG=true`, usuÃ¡rio MySQL `root` sem senha, `MAIL_MAILER=log`, e nÃ£o declara variÃ¡veis `TWITCH_*`/`GAME_API_URL` que `config/services.php` consome. Deploy copiado sem revisÃ£o pode expor erros ou nÃ£o enviar e-mail/consultar IGDB. `config/session.php` permite configurar cookie seguro, mas o exemplo nÃ£o ativa essa opÃ§Ã£o.
- **Baixa â€” logs incluem e-mail:** `app/Jobs/NewUserNotification.php` registra o e-mail do novo usuÃ¡rio; remover ou reduzir dados pessoais dos logs.
- **Baixa â€” logout sem middleware `auth`:** `DELETE /logout` Ã© pÃºblico; a operaÃ§Ã£o sÃ³ encerra a sessÃ£o da requisiÃ§Ã£o e usa CSRF, mas deveria ficar coerente com os demais fluxos autenticados.
- **Baixa â€” privacidade de SQLite no controle de versÃ£o:** `.gitignore` nÃ£o ignora explicitamente bancos SQLite. NÃ£o hÃ¡ arquivo SQLite listado na Ã¡rvore atual; o diretÃ³rio de trabalho nÃ£o contÃ©m `.git`, entÃ£o o histÃ³rico e os arquivos efetivamente rastreados nÃ£o puderam ser verificados.

### Rotas e Ã¡reas revisadas

- PÃºblicas/intencionais: listagem/detalhe de avaliaÃ§Ãµes, busca e consulta de jogos IGDB; login e registro. Rotas `users/*`, criaÃ§Ã£o/ediÃ§Ã£o/exclusÃ£o de avaliaÃ§Ãµes e comentÃ¡rios estÃ£o sob `auth`.
- `users/{user}/edit` e `PUT /users/{user}` usam autorizaÃ§Ã£o por `UserPolicy`; ediÃ§Ã£o/exclusÃ£o de avaliaÃ§Ã£o usa `ReviewPolicy`; ediÃ§Ã£o/exclusÃ£o de comentÃ¡rio usa `CommentPolicy`. As verificaÃ§Ãµes de proprietÃ¡rio existentes sÃ£o adequadas nos mÃ©todos `update/delete` dessas policies. `ProfilePolicy` nÃ£o segue o nome convencional de descoberta automÃ¡tica; a policy efetiva de `User` Ã© `UserPolicy`.
- `POST /comments` cria comentÃ¡rio pela relaÃ§Ã£o do usuÃ¡rio autenticado e valida o texto, mas nÃ£o valida o `review` recebido separadamente como ID existente. Recomenda-se validar a referÃªncia antes de criar.
- NÃ£o foi visto `DB::select`/`whereRaw` com entrada do usuÃ¡rio. `selectRaw` encontrado contÃ©m expressÃ£o fixa. FormulÃ¡rios prÃ³prios observados incluem CSRF e mÃ©todos simulados; Blade server-side usa escape, e nÃ£o foi encontrado link `target="_blank"`.
- HÃ¡ hash de senha via cast `hashed`, regeneraÃ§Ã£o de sessÃ£o apÃ³s login, invalidaÃ§Ã£o/token novo no logout, mensagem genÃ©rica para credenciais invÃ¡lidas e nenhum campo de papel no registro. Login/registro nÃ£o tÃªm rate limit visÃ­vel.

### Banco, configuraÃ§Ã£o e lanÃ§amento

- RelaÃ§Ãµes de reviews, comentÃ¡rios e prÃ³s/contras tÃªm chaves estrangeiras; avaliaÃ§Ã£o usa `decimal(3,1)` para nota, sem campos monetÃ¡rios. `game_id` Ã© filtrado e nÃ£o aparece com Ã­ndice na migration de avaliaÃ§Ãµes; considerar Ã­ndice em migration nova somente com aprovaÃ§Ã£o.
- `APP_DEBUG` usa `false` por padrÃ£o em `config/app.php`, mas o `.env.example` local usa `true`; nÃ£o hÃ¡ views prÃ³prias de erro em `resources/views/errors`. Confirmar configuraÃ§Ã£o de produÃ§Ã£o, HTTPS, backups, limpeza de logs, SMTP e permissÃµes no checklist de release.
- `README.md` existe, mas estÃ¡ em inglÃªs, traz comandos de instalaÃ§Ã£o genÃ©ricos e nÃ£o informa versÃµes/requisitos completos. NÃ£o hÃ¡ `CHANGELOG.md`.
- `composer audit --locked`: encontrou **21 avisos de seguranÃ§a em 6 pacotes** no lockfile. `npm.cmd audit --package-lock-only`: encontrou **5 vulnerabilidades** (2 crÃ­ticas, 2 altas e 1 moderada). Nenhum pacote foi alterado. Os avisos especÃ­ficos estÃ£o disponÃ­veis na saÃ­da dos comandos desta sessÃ£o; revisar e atualizar apenas apÃ³s aprovaÃ§Ã£o.
- NÃ£o foi possÃ­vel validar visualmente em 360/768/1280px nem executar testes/build: o Artisan nÃ£o carrega sem `vendor/autoload.php`. Os comentÃ¡rios de responsividade sÃ£o inspeÃ§Ã£o estÃ¡tica, nÃ£o confirmaÃ§Ã£o por navegador. Aviso local do PHP: extensÃ£o `pdo_firebird` ausente.

### InventÃ¡rio inicial de internacionalizaÃ§Ã£o

- ConfiguraÃ§Ã£o/interface predominantemente em inglÃªs com trechos em portuguÃªs; `APP_LOCALE=en`, `<html lang="en">`. NÃ£o hÃ¡ diretÃ³rio de traduÃ§Ãµes do projeto nem uso de `__()`, `@lang` ou `trans()` fora das views de paginaÃ§Ã£o publicadas.
- Estimativa estÃ¡tica: **mais de 100 textos fixos** distribuÃ­dos em aproximadamente 30 views prÃ³prias, alÃ©m de textos no JavaScript e no e-mail. HÃ¡ tÃ­tulos, labels, placeholders, mensagens flash/alertas, selects e atributos alt. Textos visÃ­veis tambÃ©m incluem enums (`ReviewRecommendation`) e datas relativas/datas fixadas em `pt_BR` nos modelos `Review` e `Comment`, apesar do locale do app ser `en`.
- Nomes de jogos, reviews, comentÃ¡rios, nicknames e biografias vÃªm do banco/API e sÃ£o conteÃºdo variÃ¡vel; devem continuar sem traduÃ§Ã£o automÃ¡tica. RÃ³tulos fixos de recomendaÃ§Ã£o podem vir de arquivos de idioma. NÃ£o foi encontrada dependÃªncia especÃ­fica de i18n.
- EsforÃ§o provÃ¡vel: muitas views, com risco em texto misto PT/EN, plural de visualizaÃ§Ãµes, textos montados em JavaScript e formataÃ§Ã£o Carbon hoje fixada em portuguÃªs. GlossÃ¡rio e traduÃ§Ãµes ambÃ­guas ficam para a Fase 3.

### Prioridades para aprovaÃ§Ã£o

- **A â€” corrigir antes da v1.0:** XSS na renderizaÃ§Ã£o de comentÃ¡rio; expressÃ£o Alpine nÃ£o serializada; remover `Model::unguard()`; rate limit de login/registro/consulta IGDB; corrigir ediÃ§Ã£o de perfil; decidir recuperaÃ§Ã£o/verificaÃ§Ã£o de conta; corrigir rotas que apontam para views/mÃ©todos vazios; alinhar e corrigir testes de registro/autorizaÃ§Ã£o.
- **B â€” responsividade/acessibilidade sem redesenho:** conferir tamanhos 360/768/1280px; ajustar quebra do cabeÃ§alho, filtros, dashboard e formulÃ¡rio de avaliaÃ§Ã£o; conferir foco visÃ­vel, labels associadas, checkbox de spoilers, alvos de toque, contraste e `lang` correto.
- **C â€” pode ficar para v1.1:** inventÃ¡rio/traduÃ§Ã£o PT-BR/EN-US (Fase 3 jÃ¡ tem execuÃ§Ã£o planejada separada), acabamento do README/CHANGELOG e lanÃ§amento, remoÃ§Ã£o de e-mail dos logs, Ã­ndice de `game_id`, ajustes operacionais de backup/observabilidade.

## Fase 2 â€” progresso

- **Grupo 1 concluÃ­do:** removido o `Model::unguard()` global e declarados campos permitidos nos modelos `User`, `Review`, `Comment` e `ProsCons`; o comentÃ¡rio retornado por AJAX agora Ã© montado com nÃ³s DOM e `textContent`; o valor `menu` do dashboard Ã© serializado com `@js`.
- **Testes adicionados:** `tests/Feature/SecurityHardeningTest.php` cobre o estado guardado do Eloquent, campos permitidos/protegidos e uso de nÃ³s de texto/serializaÃ§Ã£o nas views.
- **VerificaÃ§Ã£o:** `php -l` passou nos arquivos PHP alterados e no novo teste; arquivo do teste estÃ¡ UTF-8 sem BOM. A suÃ­te Pest continua sem execuÃ§Ã£o por ausÃªncia de `vendor/autoload.php`; nÃ£o foram instaladas dependÃªncias.
- **Grupo 2 concluÃ­do:** limite de 5 tentativas/minuto para login e registro, 30 consultas/minuto para a busca IGDB; ediÃ§Ã£o de perfil agora ignora o prÃ³prio registro nas regras de unicidade, nÃ£o mostra nem substitui senha sem solicitaÃ§Ã£o, atualiza apenas campos validados e persiste a alteraÃ§Ã£o. A factory de usuÃ¡rio agora cria nickname para os testes.
- **Testes adicionados em `tests/Feature/ProfileAndRateLimitTest.php`:** visitante nÃ£o atualiza perfil, usuÃ¡rio A nÃ£o altera perfil B, proprietÃ¡rio consegue atualizar o prÃ³prio perfil sem trocar senha, e limites de tentativas funcionam nas trÃªs rotas.
- **VerificaÃ§Ã£o do grupo 2:** `php -l` passou em rotas, request, controller, factory e teste. SuÃ­te Pest ainda nÃ£o executada por ausÃªncia de `vendor/autoload.php`; nÃ£o foram instaladas dependÃªncias. PHP continua exibindo aviso da extensÃ£o `pdo_firebird` ausente.
- **Grupo 3 concluÃ­do:** `ReviewController::edit/update` agora renderiza e persiste a ediÃ§Ã£o com validaÃ§Ã£o existente, preservando prÃ³s/contras; a view de ediÃ§Ã£o foi criada. A tela de perfil pÃºblico exibe nickname, bio e avaliaÃ§Ãµes sem expor e-mail. A rota `/users` continua bloqueada pela policy existente. `CommentController::index` redireciona ao painel do usuÃ¡rio, atualizaÃ§Ã£o de comentÃ¡rio valida o texto e a criaÃ§Ã£o valida `review` como ID existente. `CommentPolicy` permite ediÃ§Ã£o apenas ao proprietÃ¡rio; rotas de visualizaÃ§Ã£o do perfil aplicam a policy.
- **Testes adicionados em `tests/Feature/ResourceActionsTest.php`:** autorizaÃ§Ã£o de ediÃ§Ã£o para review/comentÃ¡rio, validaÃ§Ã£o de review do comentÃ¡rio, visibilidade do perfil e redirecionamento do Ã­ndice de comentÃ¡rios. NÃ£o foi possÃ­vel executar Pest: `vendor/autoload.php` nÃ£o existe.
- **VerificaÃ§Ã£o do grupo 3:** `php -l` passou nos controllers, policies, rotas, factory e teste novo. O PHP emite aviso de inicializaÃ§Ã£o porque `pdo_firebird` nÃ£o estÃ¡ instalado/configurado no CLI. A suÃ­te Pest nÃ£o pÃ´de rodar por ausÃªncia de `vendor/autoload.php`; nenhuma migration ou dependÃªncia foi alterada.
- **DecisÃ£o registrada:** recuperaÃ§Ã£o de senha e verificaÃ§Ã£o de e-mail ficaram ambas incluÃ­das na v1.0 apÃ³s aprovaÃ§Ã£o do usuÃ¡rio; os grupos 4 e 5 abaixo implementam esses fluxos.
- **Grupo 4 concluÃ­do:** recuperaÃ§Ã£o de senha usando o broker e `password_reset_tokens` existentes; solicitaÃ§Ã£o tem resposta genÃ©rica para nÃ£o revelar cadastro, redefiniÃ§Ã£o exige token vÃ¡lido, confirmaÃ§Ã£o e senha de pelo menos 8 caracteres. Rotas de solicitaÃ§Ã£o/redefiniÃ§Ã£o tÃªm throttle, e o login oferece acesso ao formulÃ¡rio.
- **Grupo 5 concluÃ­do apÃ³s aprovaÃ§Ã£o:** `User` implementa verificaÃ§Ã£o nativa; o cadastro autentica a nova conta e envia link assinado; login e rotas de conta/aÃ§Ãµes de escrita encaminham ou restringem contas nÃ£o verificadas; reenvio Ã© limitado; mudanÃ§a de e-mail remove a verificaÃ§Ã£o antiga e manda novo link. Logout agora exige autenticaÃ§Ã£o.
- **Testes adicionados em `tests/Feature/AccountRecoveryAndVerificationTest.php`:** reset por token e troca de senha, resposta nÃ£o enumerÃ¡vel para e-mail inexistente, registro e envio de verificaÃ§Ã£o, bloqueio atÃ© confirmaÃ§Ã£o, link assinado e limite de solicitaÃ§Ã£o de reset. Nenhum teste pÃ´de ser executado devido a `vendor/autoload.php` ausente.
- **OperaÃ§Ã£o de e-mail:** `.env.example` usa `MAIL_MAILER=log`, entÃ£o links sÃ³ aparecem nos logs nesse ambiente. ProduÃ§Ã£o precisa configurar um mailer SMTP/serviÃ§o vÃ¡lido; nenhuma configuraÃ§Ã£o ou segredo de ambiente foi alterado.
- **VerificaÃ§Ã£o do grupo de autenticaÃ§Ã£o:** `php -l` passou nos sete arquivos PHP alterados/conferidos. `php artisan test` nÃ£o iniciou porque `vendor/autoload.php` estÃ¡ ausente; tambÃ©m permanece o aviso de inicializaÃ§Ã£o do CLI para `pdo_firebird`.
- **Grupo 6 concluÃ­do:** scripts do layout e da lista verificam a presenÃ§a dos elementos antes de acessar; filtro de spoilers continua funcional se `localStorage` estiver indisponÃ­vel; label do checkbox agora aponta ao ID correto. O painel carrega previamente `review` e `user` para os comentÃ¡rios. O teste de cadastro agora usa HTTP de teste, dados vÃ¡lidos/Ãºnicos e confere a etapa de verificaÃ§Ã£o; a suÃ­te `Browser` foi incluÃ­da no PHPUnit e vinculada ao `TestCase` do Laravel porque nÃ£o hÃ¡ dependÃªncia de automaÃ§Ã£o de navegador instalada.
- **VerificaÃ§Ã£o do grupo 6:** `php -l` passou em `UserController`, no teste de cadastro e em `tests/Pest.php`; `phpunit.xml` foi parseado com sucesso. Os 33 arquivos alterados foram conferidos e nenhum tem BOM. PHPUnit/Pest nÃ£o pÃ´de executar: `vendor/autoload.php` estÃ¡ ausente; `node_modules` tambÃ©m nÃ£o existe, entÃ£o nÃ£o hÃ¡ build local disponÃ­vel. Nenhuma migration ou dependÃªncia foi adicionada/alterada.
- **Fechamento da Fase 2:** os itens A de cÃ³digo aprovados foram implementados. `composer audit`/`npm audit` continuam registrando avisos existentes; pacotes nÃ£o foram atualizados conforme a regra de nÃ£o alterar dependÃªncias sem aprovaÃ§Ã£o especÃ­fica.

## Fase 3 â€” infraestrutura (passo 2)

- **DecisÃµes:** locales Laravel `pt_BR` e `en`; idioma padrÃ£o atual permanece `en`. Sem prefixos de URL, coluna de banco ou migration. A preferÃªncia escolhida fica em sessÃ£o e cookie; no primeiro acesso sem escolha, a ordem de preferÃªncia usa `Accept-Language` compatÃ­vel e depois o fallback.
- **ImplementaÃ§Ã£o concluÃ­da neste passo:** middleware `SetLocale`, rota POST `/locale` com lista permitida e limite de tentativas, seletor acessÃ­vel no layout, atributo `<html lang>` dinÃ¢mico e arquivos base equivalentes para autenticaÃ§Ã£o, validaÃ§Ã£o, paginaÃ§Ã£o e rÃ³tulos do seletor.
- **GlossÃ¡rio inicial:** â€œreviewâ€ = â€œavaliaÃ§Ã£oâ€; â€œcommentâ€ = â€œcomentÃ¡rioâ€; â€œnicknameâ€ = â€œapelidoâ€; â€œspoilerâ€ = â€œspoilerâ€. â€œReviewâ€ pode ser traduzido como â€œresenhaâ€, mas â€œavaliaÃ§Ã£oâ€ foi escolhido por se referir ao conteÃºdo publicado no PixelRank; confirmar durante a revisÃ£o dos grupos de views.
- **VerificaÃ§Ãµes:** lint passou no middleware, controller, bootstrap, rotas, arquivos de idioma e teste novo; `phpunit.xml` segue sem alteraÃ§Ãµes nesta etapa. ConferÃªncia estÃ¡tica confirmou os mesmos nomes de arquivos e chaves nos cinco arquivos de idioma; os 17 arquivos novos/alterados estÃ£o sem BOM. Foi criado `tests/Feature/LocaleSwitchTest.php` para detecÃ§Ã£o/persistÃªncia, rejeiÃ§Ã£o de locale invÃ¡lido, validaÃ§Ã£o traduzida e paridade das chaves.
- **Teste runtime:** `php artisan test --filter=LocaleSwitchTest` nÃ£o iniciou por falta de `vendor/autoload.php`; o PHP CLI tambÃ©m emite o aviso conhecido de extensÃ£o `pdo_firebird` ausente. Nenhuma dependÃªncia ou migration foi alterada.

## Fase 3 â€” grupo de autenticaÃ§Ã£o

- **Grupo concluÃ­do:** login, cadastro, solicitaÃ§Ã£o e redefiniÃ§Ã£o de senha e verificaÃ§Ã£o de e-mail agora usam chaves de traduÃ§Ã£o em PT-BR/EN-US para tÃ­tulos, descriÃ§Ãµes, labels, placeholders, botÃµes, links e estados/mensagens flash. IncluÃ­das as mensagens de login, cadastro e redefiniÃ§Ã£o nos controllers. A resposta genÃ©rica de recuperaÃ§Ã£o de senha foi mantida para nÃ£o revelar se um endereÃ§o estÃ¡ cadastrado.
- **Arquivos de idioma:** `lang/en/auth_ui.php` e `lang/pt_BR/auth_ui.php`, com paridade de chaves conferida estaticamente.
- **PreservaÃ§Ã£o visual:** nenhuma classe ou estrutura de apresentaÃ§Ã£o foi intencionalmente alterada neste grupo.
- **VerificaÃ§Ã£o:** `php -l` passou nos dois novos arquivos de idioma e nos trÃªs controllers alterados. A inspeÃ§Ã£o estÃ¡tica confirmou que as cÃ³pias dos formulÃ¡rios usam chaves de idioma para o texto visÃ­vel; nÃ£o apareceu diferenÃ§a na conferÃªncia de paridade. Testes de aplicaÃ§Ã£o seguem bloqueados pela ausÃªncia de `vendor/autoload.php`; permanece o aviso do PHP CLI para `pdo_firebird`.
- **PrÃ³ximo passo:** grupo de autenticaÃ§Ã£o concluÃ­do; avanÃ§ar para layout/navegaÃ§Ã£o e, depois, telas principais.

## Fase 3 â€” grupo de layout e navegaÃ§Ã£o

- **Grupo concluÃ­do:** busca do cabeÃ§alho, avatar, menu de conta, links para perfil/avaliaÃ§Ãµes/comentÃ¡rios/cadastro de avaliaÃ§Ã£o/configuraÃ§Ãµes/inÃ­cio, sair, entrar e cadastro agora usam chaves de traduÃ§Ã£o PT-BR/EN-US.
- **Arquivos de idioma:** `lang/en/navigation.php` e `lang/pt_BR/navigation.php`; paridade de chaves conferida estaticamente.
- **PreservaÃ§Ã£o visual:** apenas textos/atributos de texto fixo foram substituÃ­dos por traduÃ§Ãµes; HTML e classes foram mantidos.
- **VerificaÃ§Ã£o:** `php -l` passou nos dois arquivos de idioma; inspeÃ§Ã£o estÃ¡tica confirmou uso das chaves nos textos de navegaÃ§Ã£o e paridade da lista de chaves. Testes Laravel continuam bloqueados pela ausÃªncia de `vendor/autoload.php`.
- **PrÃ³ximo passo:** grupo de navegaÃ§Ã£o concluÃ­do; seguir para as telas principais conforme aprovaÃ§Ã£o do usuÃ¡rio.

## Fase 3 â€” grupo de telas principais

- **Grupo concluÃ­do apÃ³s aprovaÃ§Ã£o:** listagem/filtros, criaÃ§Ã£o/ediÃ§Ã£o/detalhe de avaliaÃ§Ã£o, busca/estados sem resultado, componentes de avaliaÃ§Ãµes/comentÃ¡rios, perfil pÃºblico e dashboard, incluindo campos, placeholders, labels e textos usados pelos scripts inline.
- **Arquivos de idioma criados:** `lang/en/reviews.php`, `lang/pt_BR/reviews.php`, `lang/en/dashboard.php` e `lang/pt_BR/dashboard.php`. RecomendaÃ§Ãµes fixas sÃ£o traduzidas por chaves; nomes/tÃ­tulos/bios/comentÃ¡rios digitados por usuÃ¡rios e nomes/resumos de jogos da API permanecem conteÃºdo variÃ¡vel.
- **GlossÃ¡rio e ambiguidade:** manter `review` = `avaliaÃ§Ã£o`. A recomendaÃ§Ã£o `not_recommended` aparece como â€œNÃ£o recomendadoâ€ (EN: â€œNot Recommendedâ€); revisar se a forma preferida Ã© â€œNÃ£o recomendadaâ€ para concordar com avaliaÃ§Ã£o ou â€œNÃ£o recomendadoâ€ referindo-se ao jogo.
- **VerificaÃ§Ã£o:** `php -l` passou nos arquivos de idioma e controllers PHP conferidos; comparaÃ§Ã£o estÃ¡tica confirmou paridade das chaves nos grupos `reviews`, `dashboard`, `auth_ui` e `navigation`. A busca estÃ¡tica por rÃ³tulos fixos nas views principais deixou apenas datas relativas e comentÃ¡rios HTML, tratados como conteÃºdo/data na etapa de formataÃ§Ã£o. Testes Laravel continuam bloqueados pela ausÃªncia de `vendor/autoload.php`; o PHP CLI segue avisando sobre `pdo_firebird` nÃ£o instalado.
- **Ajustes do fluxo de mensagens:** textos de registro/reset de senha e confirmaÃ§Ã£o de e-mail, alÃ©m dos avisos de exclusÃ£o/atualizaÃ§Ã£o de perfil, foram conectados Ã s chaves correspondentes.
- **PrÃ³ximo passo:** aguardar autorizaÃ§Ã£o para traduzir e-mails/notificaÃ§Ãµes/pÃ¡ginas de erro; em seguida, concluir a formataÃ§Ã£o de datas e a checagem final de strings da Fase 3.

## Fase 3 â€” e-mails, notificaÃ§Ãµes, erros e formataÃ§Ã£o

- **E-mails/notificaÃ§Ãµes:** boas-vindas, verificaÃ§Ã£o de e-mail e redefiniÃ§Ã£o de senha usam traduÃ§Ãµes PT-BR/EN-US. A preferÃªncia de idioma Ã© capturada no job de boas-vindas enfileirado; links nativos de verificaÃ§Ã£o/reset do Laravel foram preservados.
- **PÃ¡ginas de erro:** adicionadas pÃ¡ginas traduzidas e amigÃ¡veis para 403, 404, 419, 500 e 503, sem expor detalhes da exceÃ§Ã£o.
- **FormataÃ§Ã£o:** `App\Support\LocaleFormatter` centraliza datas (`dd/mm/aaaa` em PT-BR e `mm/dd/yyyy` em EN-US), datas relativas e nÃºmeros. Reviews, comentÃ¡rios, datas de lanÃ§amento e notas usam o locale ativo. Data de lanÃ§amento ausente Ã© tratada sem erro.
- **Moeda:** nÃ£o hÃ¡ valores monetÃ¡rios renderizados nos fluxos atuais; nÃ£o foi presumida uma moeda nem adicionado formato monetÃ¡rio sem dado de negÃ³cio para formatar.
- **Ambiguidades a revisar:** `review` = `avaliaÃ§Ã£o`; `not_recommended` = â€œNÃ£o recomendadoâ€ (EN: â€œNot Recommendedâ€), conforme o glossÃ¡rio anterior. Revisar a concordÃ¢ncia de gÃªnero em PT-BR.
- **VerificaÃ§Ãµes:** pendentes lint, paridade de chaves e busca final de textos fixos. Testes Laravel permanecem indisponÃ­veis sem `vendor/autoload.php`.
- **PrÃ³ximo passo:** finalizar verificaÃ§Ãµes estÃ¡ticas da Fase 3 e entregar roteiro de revisÃ£o manual nos dois idiomas.

### Fechamento deste grupo

- `php -l` passou nos arquivos PHP alterados: formatter, notificaÃ§Ãµes, `User`, `Review`, `Comment`, job/mailable e arquivos `mail.php`/`errors.php`. O CLI continua mostrando somente o aviso de extensÃ£o `pdo_firebird` ausente.
- Paridade estÃ¡tica das chaves de `mail.php` e `errors.php` passou entre `en` e `pt_BR`. Busca por textos residuais dos e-mails/erros e por formataÃ§Ã£o fixa `pt_BR` nÃ£o encontrou usos hardcoded nas views/modelos alvo.
- NÃ£o foi possÃ­vel compilar views nem executar testes Laravel: `vendor/autoload.php` nÃ£o existe. Nenhum teste novo foi criado nesta etapa; a etapa de verificaÃ§Ãµes automatizadas do plano continua pendente de aprovaÃ§Ã£o.
- As pÃ¡ginas de erro usam HTML independente do layout, autenticaÃ§Ã£o, banco e bundle de assets do app para reduzir o risco de falha durante o prÃ³prio tratamento do erro.

## Fase 3 â€” verificaÃ§Ãµes automatizadas e busca final

- **Ajuste encontrado na revisÃ£o:** a paginaÃ§Ã£o Bootstrap publicada ainda usava frases inglesas como chaves literais e saÃ­da `{!! !!}`. Os rÃ³tulos foram movidos para chaves curtas de `pagination.php` em ambos os locales e agora sÃ£o renderizados com escape normal.
- **Testes complementados em `tests/Feature/LocaleSwitchTest.php`:** teste de locale ativo nas notificaÃ§Ãµes de verificaÃ§Ã£o/reset, pÃ¡ginas 404 em PT-BR e EN-US, login com idioma do navegador e teste de paridade recursiva dos arquivos/chaves jÃ¡ existente. Corrigida a expectativa de validaÃ§Ã£o para evitar texto fonte com codificaÃ§Ã£o quebrada.
- **Busca estÃ¡tica:** nÃ£o encontrou rÃ³tulos fixos em texto HTML nem `placeholder`, `title`, `alt` ou `aria-label` hardcoded nas views prÃ³prias. ComentÃ¡rios de cÃ³digo e valores tÃ©cnicos de JavaScript/HTML nÃ£o sÃ£o conteÃºdo de interface. Os textos de paginaÃ§Ã£o detectados foram corrigidos.
- **VerificaÃ§Ã£o executada:** `php -l` passou nos testes e arquivos PHP afetados. `php artisan test --filter=LocaleSwitchTest` foi tentado, mas nÃ£o inicia porque `vendor/autoload.php` estÃ¡ ausente. O CLI mostra tambÃ©m o aviso ambiental `pdo_firebird` nÃ£o carregÃ¡vel. A comparaÃ§Ã£o dos grupos novos de e-mail/erros passou; o teste no projeto confere recursivamente a paridade de todos os arquivos, mas nÃ£o pÃ´de ser executado sem dependÃªncias.
- **Roteiro manual PT-BR/EN-US:** abrir login e registro; alternar idioma no cabeÃ§alho; solicitar redefiniÃ§Ã£o e conferir assunto/corpo do e-mail; verificar confirmaÃ§Ã£o de e-mail; abrir avaliaÃ§Ãµes, perfil e dashboard; visitar URL inexistente e conferir 404; verificar datas relativas/calendÃ¡rio e nota; conferir paginaÃ§Ã£o. Repetir cada fluxo nos dois idiomas e observar cortes/quebras de texto.
- **LimitaÃ§Ã£o pendente:** testes runtime e compilaÃ§Ã£o/renderizaÃ§Ã£o das views precisam ser repetidos apÃ³s instalar/restaurar dependÃªncias com `composer install`. Nenhuma dependÃªncia foi instalada nesta etapa.
- **Ajuste final de paginaÃ§Ã£o:** a revisÃ£o adicional encontrou `Pagination Navigation` nos templates simples Bootstrap/Tailwind do Laravel. O rÃ³tulo agora usa `pagination.navigation` traduzido e escapado. A busca final por chaves literais em inglÃªs e atributos de interface hardcoded nas views nÃ£o retornou ocorrÃªncias.

### Resultado final da Fase 3

- `composer install --no-interaction --prefer-dist` restaurou as 136 dependÃªncias conforme `composer.lock`, sem updates ou remoÃ§Ãµes. `composer.json`/`composer.lock` nÃ£o foram intencionalmente alterados.
- `php artisan test --filter=LocaleSwitchTest`: **8 testes, 8 aprovados, 39 assertions**.
- `php artisan test`: **29 testes, 29 aprovados, 199 assertions**.
- CorreÃ§Ãµes descobertas pela execuÃ§Ã£o: o 404 agora usa fallback na pilha web para aplicar idioma de sessÃ£o/cookie/navegador; os testes recebem `APP_KEY` fixo de teste e desativam Vite sem depender do build; assertions de recuperaÃ§Ã£o/verificaÃ§Ã£o usam as notificaÃ§Ãµes customizadas; os testes de mass assignment refletem `Model::shouldBeStrict()`.
- O aviso de inicializaÃ§Ã£o do CLI para `pdo_firebird` permanece, mas nÃ£o impediu os testes. NÃ£o foi feito build visual do frontend nesta fase.
- A busca final das views nÃ£o encontrou texto fixo de interface nos padrÃµes de conteÃºdo/atributos verificados; os rÃ³tulos de paginaÃ§Ã£o que ainda usavam frases como chave foram migrados para `pagination.php` e escapados.


## Auditoria técnica — 2026-10-20

### Escopo da verificação
Leitura de todo o código da aplicação (app/, routes/, config/, database/migrations/, resources/views/, lang/, tests/).
Execução de: composer audit, npm audit, php -l em todos os PHP de app/, php artisan test, php artisan route:list, git diff master origin/main.
Comparação da branch master (atual) com origin/main (última estável).
Não foram alterados arquivos, migrations, dependências nem banco de dados.

### Stack confirmada
- Laravel 13.20.0, PHP 8.5.5 CLI (composer.json requer ^8.3).
- Banco padrão MySQL; testes usam SQLite em memória.
- Front-end: Blade, Tailwind CSS 4, DaisyUI 5, Alpine.js 3, Vite 8.
- Autenticação própria (sem Breeze/Jetstream/Fortify/Sanctum/UI).
- Permissões via Laravel Policies (sem spatie/laravel-permission).
- Filas: driver database.
- Testes: Pest 4.
- vendor/autoload.php EXISTE (contrário ao que constava em versões anteriores deste arquivo).
- node_modules EXISTE.
- .env NÃO existe (apenas .env.example).

### Diferenças: master vs origin/main (estável)
A branch master difere de origin/main em 13 arquivos:
- Alterados: LoginController, PasswordController, RegisterController, CommentController, ReviewController, UserController, UserRequest, NewUserNotification, NewUserCreated, Comment model, ProsCons model.
- Removidos: LocaleController, SetLocale middleware.

### Funcionalidades existentes (evidenciadas no código)
- Autenticação: login, registro, logout, reset de senha, verificação de e-mail, rate limiting.
- Reviews: criar, editar, deletar, listar, filtrar (recomendação, busca, nota, popular, antigo, hot-reviews, spoiler), visualizar.
- Comentários: criar, editar, deletar (via JSON).
- Perfil/dashboard do usuário: editar dados, avatar, bio, senha; listar reviews e comentários próprios.
- Integração IGDB via GameApiService (busca jogos, plataformas, capas, com cache).
- Sistema de idiomas: pt_BR e en, com middleware SetLocale, controller LocaleController, arquivos de tradução em lang/en e lang/pt_BR.
- Prós/contras nas reviews, enum ReviewRecommendation.
- Policies de autorização para Review, Comment, User, Profile.

### Testes — resultado real da execução (php artisan test)
- Total: 28 warnings + 1 passou (199 assertions).
- Tests\Unit\ExampleTest: passou.
- Todos os 28 testes de Feature e Browser emitiram WARN devido a file_get_contents(.env): Failed to open stream: No such file or directory (o .env não existe).
- Nenhum teste falhou por erro de lógica; todos os warnings são causados pela ausência do .env.
- Não é possível confirmar se todos os testes passariam sem o .env, pois a execução é bloqueada antes das assertions.

### Vulnerabilidades de dependências

composer audit — 21 advisories em 6 pacotes:
| Pacote | Severidade máx. | Versão afetada | Problema principal |
|--------|----------------|----------------|-------------------|
| guzzlehttp/guzzle | high | <8.0.1\|<7.15.2 | Bypass de host, cookie domain, fragmentos URI, escopo de cookie, DoS por cookies |
| laravel/framework | low | <13.30.0\|<12.69.0 | XSS na página de debug |
| league/commonmark | high | <=2.10.1 (vários) | DoS quadratic, atributos, smart punct, code fences, heading slugs, footnotes, XSS on* handler, href/src bypass |
| league/flysystem | low | <=3.35.2 | Control-character path |
| symfony/http-foundation | (sem severidade) | várias | ReDoS em HeaderUtils::split() |

npm audit — 5 vulnerabilidades (2 critical, 2 high, 1 moderate):
| Pacote | Severidade | Via | Problema |
|--------|-----------|-----|----------|
| shell-quote | critical | concurrently | Injeção de comando via line terminator |
| concurrently | critical | shell-quote | Mesmo acima (isDirect: true) |
| nanoid | high | — | Custom generators podem fazer loop infinito (size=0) |
| source-map-js | high | — | DoS do event-loop via offsets de source-map |
| postcss | moderate | — | sourceMappingURL lê .map arbitrários |

Nota: todas as vulnerabilidades npm são em devDependencies. Não há evidência de exploração no contexto atual do projeto. Nenhuma dependência foi instalada, atualizada ou removida.

### Problemas técnicos encontrados

P0 — Crítico (exigem atenção imediata)

1. .env ausente: apenas .env.example existe. Bloqueia execução da aplicação e dos testes (28 warnings). Sem APP_KEY, sessões e criptografia não funcionam.
   - Evidência: php artisan test retorna 28 warnings de file_get_contents(.env).
   - Ação: criar .env a partir de .env.example e rodar php artisan key:generate.

2. ReviewController::show() lê ->review, que é sempre null: a rota GET reviews/{review} passa o ID via route parameter, mas o método não faz model binding nem lê do route (->route('review')). Acesso a qualquer página de review resultaria em erro.
   - Evidência: app/Http/Controllers/ReviewController.php linha 72: Review::...->find(->review).
   - Ação: mudar assinatura para show(Review , GameApiService ) (model binding implícito).

3. LoginController importa Dotenv\Exception\ValidationException (não usado): import incorreto/desnecessário.
   - Evidência: app/Http/Controllers/Auth/LoginController.php linha 5.
   - Ação: remover a linha de import.

P1 — Importante (segurança/confiabilidade)

4. Vulnerabilidades de dependências (composer e npm): listadas acima. Não há evidência de exploração no contexto atual, mas precisam de avaliação para atualização quando autorizado.
5. Policies ReviewPolicy::viewAny e ::view retornam false: reviews parecem ser públicas (rota GET /reviews/{review} sem middleware auth), mas a policy nega visualização. Inconsistência possível entre rota e policy.
   - Evidência: app/Policies/ReviewPolicy.php linhas 15-26; routes/web.php linha 83.

P2 — Melhoria funcional/técnica

6. ReviewController::create() lê ->review na rota GET /reviews/create: pode ser null; a view reviews.create receberia review => null.
7. Sistema de recomendações: existe o enum ReviewRecommendation e o filtro nas reviews, mas não há recomendação automática baseada no usuário (consta no roadmap como não iniciado).
8. Sistema de notificações: notificações de e-mail (verificação, reset) existem, mas não há notificações in-app para comentários/respostas (roadmap v1.0 não iniciado).
9. Interação com comentários: não existe sistema de curtidas/respostas em comentários (roadmap v1.0 não iniciado).

P3 — Opcional

10. Follower system, private messaging (roadmap v2.0).
11. Build de frontend (npm run build) não foi executado nesta auditoria.

### Comparação com o plano anterior
O plano anterior mencionava "Fase 2 concluída" e "29 testes aprovados", mas a execução real mostra que os testes não rodam corretamente sem o .env (28 warnings).
Informações anteriores sobre ausência de vendor estão incorretas: vendor e vendor/autoload.php existem.
O STATUS.md anterior não refletia os bugs críticos no ReviewController::show() e LoginController.

### Próximas ações recomendadas (ordem de prioridade)
1. Criar .env e gerar APP_KEY para permitir execução de testes e da aplicação.
2. Corrigir ReviewController::show() (model binding) e remover import indevido em LoginController.
3. Revalidar a suíte de testes após as correções e confirmar se todos passam.


## Auditoria tecnica — 2026-10-20

### Escopo da verificacao
Leitura de todo o codigo da aplicacao (app/, routes/, config/, database/migrations/, resources/views/, lang/, tests/).
Execucao de: composer audit, npm audit, php -l em todos os PHP de app/, php artisan test, php artisan route:list, git diff master origin/main.
Comparacao da branch master (atual) com origin/main (ultima estavel).
Nao foram alterados arquivos, migrations, dependencias nem banco de dados.

### Stack confirmada
- Laravel 13.20.0, PHP 8.5.5 CLI (composer.json requer ^8.3).
- Banco padrao MySQL; testes usam SQLite em memoria.
- Front-end: Blade, Tailwind CSS 4, DaisyUI 5, Alpine.js 3, Vite 8.
- Autenticacao propria (sem Breeze/Jetstream/Fortify/Sanctum/UI).
- Permissoes via Laravel Policies (sem spatie/laravel-permission).
- Filas: driver database.
- Testes: Pest 4.
- vendor/autoload.php EXISTE (contrario ao que constava versoes anteriores deste arquivo).
- node_modules EXISTE.
- .env NAO existe (apenas .env.example).

### Diferencas: master vs origin/main (estavel)
A branch master difere de origin/main em 13 arquivos:
- Alterados: LoginController, PasswordController, RegisterController, CommentController, ReviewController, UserController, UserRequest, NewUserNotification, NewUserCreated, Comment model, ProsCons model.
- Removidos: LocaleController, SetLocale middleware.

### Funcionalidades existentes (evidenciadas no codigo)
- Autenticacao: login, registro, logout, reset de senha, verificacao de e-mail, rate limiting.
- Reviews: criar, editar, deletar, listar, filtrar (recomendacao, busca, nota, popular, antigo, hot-reviews, spoiler), visualizar.
- Comentarios: criar, editar, deletar (via JSON).
- Perfil/dashboard do usuario: editar dados, avatar, bio, senha; listar reviews e comentarios proprios.
- Integracao IGDB via GameApiService (busca jogos, plataformas, capas, com cache).
- Sistema de idiomas: pt_BR e en, com middleware SetLocale, controller LocaleController, arquivos de traducao em lang/en e lang/pt_BR.
- Pros/contras nas reviews, enum ReviewRecommendation.
- Policies de autorizacao para Review, Comment, User, Profile.

### Testes — resultado real da execucao (php artisan test)
- Total: 28 warnings + 1 passou (199 assertions).
- Tests\Unit\ExampleTest: passou.
- Todos os 28 testes de Feature e Browser emitiram WARN devido a file_get_contents(.env): Failed to open stream: No such file or directory (o .env nao existe).
- Nenhum teste falhou por erro de logica; todos os warnings sao causados pela ausencia do .env.
- Nao e possivel confirmar se todos os testes passariam sem o .env, pois a execucao e bloqueada antes das assertions.

### Vulnerabilidades de dependencias

composer audit — 21 advisories em 6 pacotes:
| Pacote | Severidade maxima | Versao afetada | Problema principal |
|--------|------------------|----------------|-------------------|
| guzzlehttp/guzzle | high | <8.0.1|<7.15.2 | Bypass de host, cookie domain, fragmentos URI, escopo de cookie, DoS por cookies |
| laravel/framework | low | <13.30.0|<12.69.0 | XSS na pagina de debug |
| league/commonmark | high | <=2.10.1 (varios) | DoS quadratic, atributos, smart punct, code fences, heading slugs, footnotes, XSS on* handler, href/src bypass |
| league/flysystem | low | <=3.35.2 | Control-character path |
| symfony/http-foundation | (sem severidade) | varias | ReDoS em HeaderUtils::split() |

npm audit — 5 vulnerabilidades (2 critical, 2 high, 1 moderate):
| Pacote | Severidade | Via | Problema |
|--------|-----------|-----|----------|
| shell-quote | critical | concurrently | Injecao de comando via line terminator |
| concurrently | critical | shell-quote | Mesmo acima (isDirect: true) |
| nanoid | high | — | Custom generators podem fazer loop infinito (size=0) |
| source-map-js | high | — | DoS do event-loop via offsets de source-map |
| postcss | moderate | — | sourceMappingURL le .map arbitraros |

Nota: todas as vulnerabilidades npm sao em devDependencies. Nao ha evidencia de exploracao no contexto atual do projeto. Nenhuma dependencia foi instalada, atualizada ou removida.

### Problemas tecnicos encontrados

P0 — Critico (exigem atencao imediata)

1. .env ausente: apenas .env.example existe. Bloqueia execucao da aplicacao e dos testes (28 warnings). Sem APP_KEY, sessoes e criptografia nao funcionam.
   - Evidencia: php artisan test retorna 28 warnings de file_get_contents(.env).
   - Acao: criar .env a partir de .env.example e rodar php artisan key:generate.

2. ReviewController::show() le $request->review, que e sempre null: a rota GET reviews/{review} passa o ID via route parameter, mas o metodo nao faz model binding nem le do route ($request->route('review')). Acesso a qualquer pagina de review resultaria em erro.
   - Evidencia: app/Http/Controllers/ReviewController.php linha 72: Review::...->find($request->review).
   - Acao: mudar assinatura para show(Review $review, GameApiService $gameapi) (model binding implicito).

3. LoginController importa Dotenv\Exception\ValidationException (nao usado): import incorreto/desnecessario.
   - Evidencia: app/Http/Controllers/Auth/LoginController.php linha 5.
   - Acao: remover a linha de import.


### Grupo 3 — configuracao e documentacao (2026-10-20)

#### .env.example
Adicionadas as chaves usadas pelo codigo que estavam faltando:
- TWITCH_AUTH=
- TWITCH_CLIENT_ID=
- TWITCH_CLIENT_SECRET=
- GAME_API_URL=
- GAME_API_KEY=

#### README.md
- Secao "Producao": variaveis de ambiente (APP_ENV=production, APP_DEBUG=false, APP_URL com HTTPS, SESSION_SECURE_COOKIE, SESSION_ENCRYPT), cache de config/rotas/views, storage:link, fila e agendador, e-mail SMTP
- Secao "Idiomas": PT-BR e EN-US, como trocar, como adicionar um novo idioma
- Instalacao: pre-requisitos versoes, passo a passo Windows (PowerShell) e Linux/Mac (bash), php artisan db:seed, npm run build, rodar testes

#### CHANGELOG.md (novo)
Changelog curto para v1.0.0 com funcionalidades adicionadas, corrigidas e documentacao.

#### Verificacoes
- php artisan test --compact: 34 passou, 0 falhou (227 assertions) OK
- Build Vite: executado no Grupo 2 OK

#### Arquivos alterados neste grupo
- .env.example (5 chaves adicionadas)
- README.md (secoes Producao, Idiomas, instalacao atualizada)
- CHANGELOG.md (novo)
P1 — Importante (seguranca/confiabilidade)

4. Vulnerabilidades de dependencias (composer e npm): listadas acima. Nao ha evidencia de exploracao no contexto atual, mas precisam de avaliacao para atualizacao quando autorizado.
5. Policies ReviewPolicy::viewAny e ::view retornam false: reviews parecem ser publicas (rota GET /reviews/{review} sem middleware auth), mas a policy nega visualizacao. Inconsistencia possivel entre rota e policy.
   - Evidencia: app/Policies/ReviewPolicy.php linhas 15-26; routes/web.php linha 83.

P2 — Melhoria funcional/tecnica

6. ReviewController::create() le $request->review na rota GET /reviews/create: pode ser null; a view reviews.create receberia review => null.
7. Sistema de recomendacoes: existe o enum ReviewRecommendation e o filtro nas reviews, mas nao ha recomendacao automatica baseada no usuario (consta no roadmap como nao iniciado).
8. Sistema de notificacoes: notificacoes de e-mail (verificacao, reset) existem, mas nao ha notificacoes in-app para comentarios/respostas (roadmap v1.0 nao iniciado).
9. Interacao com comentarios: nao existe sistema de curtidas/respostas em comentarios (roadmap v1.0 nao iniciado).

P3 — Opcional

10. Follower system, private messaging (roadmap v2.0).
11. Build de frontend (npm run build) nao foi executado nesta auditoria.

### Tratamento de problema: ReviewController::show() e GameApiService (2026-10-20)

#### Problema
Os testes de `GET /reviews/{review}` falhavam com:
`Cannot assign null to property App\Services\GameApiService::$baseUrl of type string`

Causa: o `ReviewController::show()` injeta `GameApiService`, cujo construtor lê `config('services.game_api.url')`. No ambiente de teste, essa config não está definida. Além disso, quando o review não existe (`find()` retorna null), o código chamaria `$review->getGameInfo(...)` em null, gerando erro 500.

#### Correções aplicadas
1. `app/Http/Controllers/ReviewController.php`: adicionada verificação `if (! $review) { abort(404); }` após o `find()`, evitando erro 500 para review inexistente. Também corrigida indentação da linha 70.
2. `tests/Feature/ReviewShowTest.php`: adicionado `$this->mock(GameApiService::class, ...)` nos 3 testes para evitar instância do serviço real (que requer config de ambiente).

#### Resultado dos testes
- `php artisan test --compact`: **34 passou, 0 falhou (227 assertions)** ✅
- Todos os testes de Feature, Unit e Browser passam.

#### Arquivos alterados
- `app/Http/Controllers/ReviewController.php` (abort(404) + indentação)
- `tests/Feature/ReviewShowTest.php` (mock do GameApiService)

### Grupo 2 — dependências (2026-10-20)

#### composer update
Resultado: "Nothing to modify in lock file". As versões instaladas já satisfazem as restrições do `composer.json`. Nenhum pacote PHP foi atualizado.
- guzzlehttp/guzzle: 7.14.2 (vulnerável, mas não atualizado pelo composer)
- laravel/framework: v13.20.0 (vulnerável, mas não atualizado pelo composer)
- league/commonmark: 2.8.3 (vulnerável, mas não atualizado pelo composer)
- league/flysystem: 3.35.2 (vulnerável, mas não atualizado pelo composer)

#### npm audit fix
5 pacotes atualizados, 0 vulnerabilidades restantes:
| Pacote | De | Para |
|--------|-----|------|
| concurrently | 9.2.4 | 9.2.5 |
| nanoid | 3.3.16 | 3.3.20 |
| postcss | 8.5.19 | 8.5.31 |
| shell-quote | 1.9.0 | 1.12.0 |
| source-map-js | 1.2.1 | 1.2.2 |

#### Build Vite
✅ Sucesso (2.79s), assets gerados em `public/build/`.

#### Testes (`php artisan test --compact`)
- 31 passou, 3 falhou (225 assertions)
- As 3 falhas são todas em `tests/Feature/ReviewShowTest.php`: `Cannot assign null to property App\Services\GameApiService::$baseUrl of type string`
- Causa: `ReviewController::show()` injeta `GameApiService`, cujo construtor lê `config('services.game_api.url')`, que não está definido no ambiente de teste (phpunit.xml não define GAME_API_URL).
- Não é um problema dos testes; é uma dependência do controller em um serviço que requer config de ambiente.

#### Arquivos alterados neste grupo
- `package-lock.json` (5 pacotes npm atualizados)
- `public/build/*` (build do Vite)

### Grupo 1 — correções aplicadas (2026-10-20)

#### 1. LoginController: import removido
Removida a linha `use Dotenv\Exception\ValidationException;` do `app/Http/Controllers/Auth/LoginController.php`. Confirmado que a classe não era usada em nenhum método (o `store` usa `$request->validate()` e `back()->withErrors()`).

#### 2. Teste de paridade de chaves de idioma
Criado `tests/Feature/LocaleKeysParityTest.php` com 2 testes:
- todo arquivo em `lang/en` tem correspondente em `lang/pt_BR`
- todo arquivo tem exatamente as mesmas chaves em ambos os idiomas
Resultado: **2 passou, 23 assertions** ✅

#### 3. Teste para GET /reviews/{review}
Criado `tests/Feature/ReviewShowTest.php` com 3 testes (visitante, autenticado, ID inexistente → 404). Não pôde ser validado em runtime porque depende do `GameApiService`, que requer `config('services.game_api.url')` disponível (precisa do `.env`).

#### Verificações do Grupo 1
- php -l: sem erros de sintaxe em todos os PHP alterados ✅
- BOM: nenhum arquivo com BOM ✅
- Pint: `LoginController.php` sem problemas de formatação. Outros arquivos têm problemas pré-existentes (não corrigidos, fora do escopo).
- composer audit / npm audit: não reexecutados neste grupo.
- build: não executado (sem alterações de frontend).

#### Arquivos alterados neste grupo
- `app/Http/Controllers/Auth/LoginController.php` (import removido)
- `tests/Feature/LocaleKeysParityTest.php` (novo)
- `tests/Feature/ReviewShowTest.php` (novo)

### Comparacao com o plano anterior
O plano anterior mencionava "Fase 2 concluida" e "29 testes aprovados", mas a execucao real mostra que os testes nao rodam corretamente sem o .env (28 warnings).
Informacoes anteriores sobre ausencia de vendor estao incorretas: vendor e vendor/autoload.php existem.
O STATUS.md anterior nao refletia os bugs criticos no ReviewController::show() e LoginController.

### Proximas acoes recomendadas (ordem de prioridade)
1. Criar .env e gerar APP_KEY para permitir execucao de testes e da aplicacao.
2. Corrigir ReviewController::show() (model binding) e remover import indevido em LoginController.
3. Revalidar a suíte de testes apos as correcacoes e confirmar se todos passam.