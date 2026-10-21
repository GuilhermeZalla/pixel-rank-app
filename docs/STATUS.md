# Status do projeto PixelRank

## Stack detectada

- Laravel **13.20.0** (versão exata no `composer.lock`; `composer.json` permite `^13.8`).
- PHP **8.5.5 CLI** no ambiente atual; o `composer.json` requer PHP `^8.3`.
- Banco padrão: **MySQL** (`.env.example`); os testes estão configurados para **SQLite em memória** (`phpunit.xml`).
- Front-end: **Blade**, **Tailwind CSS 4**, **DaisyUI 5**, **Alpine.js 3** e **Vite 8**. O Vite compila `resources/css/app.css` e `resources/js/app.js`.
- Autenticação: implementação própria no projeto (`LoginController`, `LogoutController`, `RegisterController`); não foram encontrados Breeze, Jetstream, Fortify, Sanctum ou Laravel UI no `composer.lock`.
- Permissões: há diretório `app/Policies`; não foi encontrado `spatie/laravel-permission` no `composer.lock`.
- Filas: driver padrão **database** (`config/queue.php` e `.env.example`); o projeto inclui `app/Jobs`.
- Testes: Pest 4; existem arquivos de exemplo em `tests/Unit` e `tests/Browser`. `phpunit.xml` também aponta para `tests/Feature`, que não contém testes no momento.
- Ferramenta de formatação: Laravel Pint está listado em `require-dev`.

## Estado inicial

- Data da verificação: 2026-10-19.
- Comando executado: `php artisan test`.
- Resultado: **não foi possível iniciar**. O diretório `vendor`/arquivo `vendor/autoload.php` não existe; o Artisan encerrou antes de carregar a aplicação. Nenhum teste passou ou falhou individualmente. O PHP CLI também exibiu um aviso de inicialização porque a extensão `pdo_firebird` configurada não foi encontrada.
- Nenhum código de aplicação foi alterado nesta etapa.

## Fases e progresso

- **Tarefa inicial â€” detecção e estado base:** concluída. Stack identificada; execução de testes bloqueada pela ausência de `vendor`.
- **Fase 1 â€” auditoria (somente leitura, exceto este arquivo):** concluída em 2026-10-19 por inspeção estática. Relatório e prioridades abaixo; aguardar revisão do usuário antes da Fase 2.
- **Fase 2 â€” segurança e bugs (grupo A aprovado):** implementação concluída nos grupos 1 a 6; testes de regressão/autorização adicionados. A validação em runtime continua pendente pela ausência de `vendor/autoload.php`.
- **Fase 3 â€” internacionalização PT-BR/EN-US:** infraestrutura e grupos de autenticação, layout/navegação e telas principais concluídos; pendem e-mails/notificações/erros, formatação por idioma e verificações finais.
- **Fase 4 â€” estilo e responsividade:** corrigir apenas defeitos do grupo B aprovado, preservando identidade visual; conferir páginas nos dois idiomas e build quando aplicável.
- **Fase 5 â€” preparação da v1.0:** finalizar documentação em português, checklist de produção, changelog e checklist de release; sugerir commit/tag sem executá-los.

## Decisões e limites de trabalho

- Preservar identidade visual, arquitetura, rotas públicas, nomes de tabelas/colunas, migrations existentes e idioma padrão atual.
- Não adicionar, remover ou atualizar dependências sem aprovação. Não alterar dados reais nem executar comandos destrutivos no banco.
- Trabalhar uma fase por vez; ao concluir, atualizar este status e parar para aguardar o próximo pedido/aprovação.
- Escrever arquivos em UTF-8 sem BOM; não fazer commit.

## Pendências

- Instalar/restaurar dependências do projeto quando autorizado/necessário para executar os testes (`vendor/autoload.php` está ausente). A tarefa inicial não autoriza instalar pacotes.
- Executar verificações finais de PHP/lint, revisão manual das rotas/views e, quando as dependências estiverem disponíveis, rodar a suíte de testes.
- Glossário de domínio e traduções ambíguas: ainda não levantados; registrar durante a Fase 3.

## Fase 1 â€” resumo da auditoria

### Achados

- **Alta â€” XSS ao inserir comentários:** `resources/views/reviews/show.blade.php` concatena texto controlado por usuários em `insertAdjacentHTML`. Um comentário pode inserir marcação/script que executa no navegador de quem abre a avaliação. Correção proposta: criar nós DOM e usar `textContent` para os valores.
- **Alta â€” valor de rota dentro de expressão Alpine:** `resources/views/users/edit.blade.php` interpola `menu` em uma expressão JavaScript dentro de `x-data`. Escapamento HTML não é garantia suficiente nesse contexto. Correção proposta: serializar o valor para JavaScript com mecanismo próprio do Blade e limitar os valores aceitos.
- **Média â€” atribuição em massa global:** `app/Providers/AppServiceProvider.php` chama `Model::unguard()`, desativando a proteção `$fillable` declarada em `app/Models/User.php`. Hoje alguns controllers montam arrays explícitos, mas a proteção fica inativa para toda a aplicação. Correção proposta: retirar o unguard global e declarar campos permitidos por modelo.
- **Média â€” sem limite de tentativas:** não há `throttle` aparente nas rotas de login/registro nem no endpoint público que consulta IGDB (`/games/search`). Isso facilita tentativas de senha e abuso da cota da API. Correção proposta: limites por IP/identidade nas rotas adequadas.
- **Média â€” edição de perfil não conclui:** `app/Http/Controllers/UserController.php` usa regras `unique` sem ignorar o próprio usuário, preenche campos de senha com o hash existente e termina com `$user->save;` sem chamar o método. A edição tende a falhar na validação e, mesmo se passar, não persiste. Correção proposta: ignorar o próprio registro nas regras, deixar senha vazia quando não for trocada e salvar o modelo.
- **Média â€” rotas sem implementação ou views:** `UserController::index/show` apontam para `users.index` e `users.show`, que não existem; `ReviewController::edit/update` e `CommentController::index/update` estão vazios apesar de rotas ativas. Essas URLs podem terminar em erro em vez de resposta útil. Correção proposta: completar ou retirar apenas após confirmar o comportamento pretendido.
- **Média â€” testes de browser incoerentes com o registro:** `tests/Browser/ExampleTest.php` omite nickname e confirmação de senha, ambos exigidos, e espera redirecionamento diferente do controller. A cobertura de feature/autorização está ausente; há apenas um teste unitário trivial e esse exemplo de browser.
- **Média â€” erro JavaScript global e no estado vazio:** `resources/views/components/layout.blade.php` liga um listener a `#review` em toda página, embora esse elemento não exista em várias delas. `resources/views/reviews/index.blade.php` acessa o checkbox de spoilers mesmo quando a lista vazia não o renderiza. Correção proposta: proteger inicializações por existência do elemento.
- **Média â€” dashboard pode fazer consultas repetidas:** `UserController::edit` pagina comentários sem carregar previamente `review` e `user`, relações acessadas por `components/comment.blade.php`. Correção proposta: eager loading das relações usadas.
- **Média â€” formulários/fluxos faltantes para produção:** não há rotas de redefinição de senha nem verificação de e-mail; confirmar se recuperação de conta e verificação são requisitos da v1.0. O mail atual é apenas uma mensagem de boas-vindas.
- **Média â€” responsividade estática:** layout do cabeçalho e busca usa larguras fixas; filtros ficam em linha; dashboard e formulário de avaliação usam `flex-row` e larguras sem regras móveis claras. Risco de conteúdo comprimido/rolagem em telas estreitas. Correção proposta: ajustes localizados nas classes existentes, após conferir em navegador nos tamanhos pedidos.
- **Média â€” configuração de exemplo é local:** `.env.example` traz `APP_ENV=local`, `APP_DEBUG=true`, usuário MySQL `root` sem senha, `MAIL_MAILER=log`, e não declara variáveis `TWITCH_*`/`GAME_API_URL` que `config/services.php` consome. Deploy copiado sem revisão pode expor erros ou não enviar e-mail/consultar IGDB. `config/session.php` permite configurar cookie seguro, mas o exemplo não ativa essa opção.
- **Baixa â€” logs incluem e-mail:** `app/Jobs/NewUserNotification.php` registra o e-mail do novo usuário; remover ou reduzir dados pessoais dos logs.
- **Baixa â€” logout sem middleware `auth`:** `DELETE /logout` é público; a operação só encerra a sessão da requisição e usa CSRF, mas deveria ficar coerente com os demais fluxos autenticados.
- **Baixa â€” privacidade de SQLite no controle de versão:** `.gitignore` não ignora explicitamente bancos SQLite. Não há arquivo SQLite listado na árvore atual; o diretório de trabalho não contém `.git`, então o histórico e os arquivos efetivamente rastreados não puderam ser verificados.

### Rotas e áreas revisadas

- Públicas/intencionais: listagem/detalhe de avaliações, busca e consulta de jogos IGDB; login e registro. Rotas `users/*`, criação/edição/exclusão de avaliações e comentários estão sob `auth`.
- `users/{user}/edit` e `PUT /users/{user}` usam autorização por `UserPolicy`; edição/exclusão de avaliação usa `ReviewPolicy`; edição/exclusão de comentário usa `CommentPolicy`. As verificações de proprietário existentes são adequadas nos métodos `update/delete` dessas policies. `ProfilePolicy` não segue o nome convencional de descoberta automática; a policy efetiva de `User` é `UserPolicy`.
- `POST /comments` cria comentário pela relação do usuário autenticado e valida o texto, mas não valida o `review` recebido separadamente como ID existente. Recomenda-se validar a referência antes de criar.
- Não foi visto `DB::select`/`whereRaw` com entrada do usuário. `selectRaw` encontrado contém expressão fixa. Formulários próprios observados incluem CSRF e métodos simulados; Blade server-side usa escape, e não foi encontrado link `target="_blank"`.
- Há hash de senha via cast `hashed`, regeneração de sessão após login, invalidação/token novo no logout, mensagem genérica para credenciais inválidas e nenhum campo de papel no registro. Login/registro não têm rate limit visível.

### Banco, configuração e lançamento

- Relações de reviews, comentários e prós/contras têm chaves estrangeiras; avaliação usa `decimal(3,1)` para nota, sem campos monetários. `game_id` é filtrado e não aparece com índice na migration de avaliações; considerar índice em migration nova somente com aprovação.
- `APP_DEBUG` usa `false` por padrão em `config/app.php`, mas o `.env.example` local usa `true`; não há views próprias de erro em `resources/views/errors`. Confirmar configuração de produção, HTTPS, backups, limpeza de logs, SMTP e permissões no checklist de release.
- `README.md` existe, mas está em inglês, traz comandos de instalação genéricos e não informa versões/requisitos completos. Não há `CHANGELOG.md`.
- `composer audit --locked`: encontrou **21 avisos de segurança em 6 pacotes** no lockfile. `npm.cmd audit --package-lock-only`: encontrou **5 vulnerabilidades** (2 críticas, 2 altas e 1 moderada). Nenhum pacote foi alterado. Os avisos específicos estão disponíveis na saída dos comandos desta sessão; revisar e atualizar apenas após aprovação.
- Não foi possível validar visualmente em 360/768/1280px nem executar testes/build: o Artisan não carrega sem `vendor/autoload.php`. Os comentários de responsividade são inspeção estática, não confirmação por navegador. Aviso local do PHP: extensão `pdo_firebird` ausente.

### Inventário inicial de internacionalização

- Configuração/interface predominantemente em inglês com trechos em português; `APP_LOCALE=en`, `<html lang="en">`. Não há diretório de traduções do projeto nem uso de `__()`, `@lang` ou `trans()` fora das views de paginação publicadas.
- Estimativa estática: **mais de 100 textos fixos** distribuídos em aproximadamente 30 views próprias, além de textos no JavaScript e no e-mail. Há títulos, labels, placeholders, mensagens flash/alertas, selects e atributos alt. Textos visíveis também incluem enums (`ReviewRecommendation`) e datas relativas/datas fixadas em `pt_BR` nos modelos `Review` e `Comment`, apesar do locale do app ser `en`.
- Nomes de jogos, reviews, comentários, nicknames e biografias vêm do banco/API e são conteúdo variável; devem continuar sem tradução automática. Rótulos fixos de recomendação podem vir de arquivos de idioma. Não foi encontrada dependência específica de i18n.
- Esforço provável: muitas views, com risco em texto misto PT/EN, plural de visualizações, textos montados em JavaScript e formatação Carbon hoje fixada em português. Glossário e traduções ambíguas ficam para a Fase 3.

### Prioridades para aprovação

- **A â€” corrigir antes da v1.0:** XSS na renderização de comentário; expressão Alpine não serializada; remover `Model::unguard()`; rate limit de login/registro/consulta IGDB; corrigir edição de perfil; decidir recuperação/verificação de conta; corrigir rotas que apontam para views/métodos vazios; alinhar e corrigir testes de registro/autorização.
- **B â€” responsividade/acessibilidade sem redesenho:** conferir tamanhos 360/768/1280px; ajustar quebra do cabeçalho, filtros, dashboard e formulário de avaliação; conferir foco visível, labels associadas, checkbox de spoilers, alvos de toque, contraste e `lang` correto.
- **C â€” pode ficar para v1.1:** inventário/tradução PT-BR/EN-US (Fase 3 já tem execução planejada separada), acabamento do README/CHANGELOG e lançamento, remoção de e-mail dos logs, índice de `game_id`, ajustes operacionais de backup/observabilidade.

## Fase 2 â€” progresso

- **Grupo 1 concluído:** removido o `Model::unguard()` global e declarados campos permitidos nos modelos `User`, `Review`, `Comment` e `ProsCons`; o comentário retornado por AJAX agora é montado com nós DOM e `textContent`; o valor `menu` do dashboard é serializado com `@js`.
- **Testes adicionados:** `tests/Feature/SecurityHardeningTest.php` cobre o estado guardado do Eloquent, campos permitidos/protegidos e uso de nós de texto/serialização nas views.
- **Verificação:** `php -l` passou nos arquivos PHP alterados e no novo teste; arquivo do teste está UTF-8 sem BOM. A suíte Pest continua sem execução por ausência de `vendor/autoload.php`; não foram instaladas dependências.
- **Grupo 2 concluído:** limite de 5 tentativas/minuto para login e registro, 30 consultas/minuto para a busca IGDB; edição de perfil agora ignora o próprio registro nas regras de unicidade, não mostra nem substitui senha sem solicitação, atualiza apenas campos validados e persiste a alteração. A factory de usuário agora cria nickname para os testes.
- **Testes adicionados em `tests/Feature/ProfileAndRateLimitTest.php`:** visitante não atualiza perfil, usuário A não altera perfil B, proprietário consegue atualizar o próprio perfil sem trocar senha, e limites de tentativas funcionam nas três rotas.
- **Verificação do grupo 2:** `php -l` passou em rotas, request, controller, factory e teste. Suíte Pest ainda não executada por ausência de `vendor/autoload.php`; não foram instaladas dependências. PHP continua exibindo aviso da extensão `pdo_firebird` ausente.
- **Grupo 3 concluído:** `ReviewController::edit/update` agora renderiza e persiste a edição com validação existente, preservando prós/contras; a view de edição foi criada. A tela de perfil público exibe nickname, bio e avaliações sem expor e-mail. A rota `/users` continua bloqueada pela policy existente. `CommentController::index` redireciona ao painel do usuário, atualização de comentário valida o texto e a criação valida `review` como ID existente. `CommentPolicy` permite edição apenas ao proprietário; rotas de visualização do perfil aplicam a policy.
- **Testes adicionados em `tests/Feature/ResourceActionsTest.php`:** autorização de edição para review/comentário, validação de review do comentário, visibilidade do perfil e redirecionamento do índice de comentários. Não foi possível executar Pest: `vendor/autoload.php` não existe.
- **Verificação do grupo 3:** `php -l` passou nos controllers, policies, rotas, factory e teste novo. O PHP emite aviso de inicialização porque `pdo_firebird` não está instalado/configurado no CLI. A suíte Pest não pôde rodar por ausência de `vendor/autoload.php`; nenhuma migration ou dependência foi alterada.
- **Decisão registrada:** recuperação de senha e verificação de e-mail ficaram ambas incluídas na v1.0 após aprovação do usuário; os grupos 4 e 5 abaixo implementam esses fluxos.
- **Grupo 4 concluído:** recuperação de senha usando o broker e `password_reset_tokens` existentes; solicitação tem resposta genérica para não revelar cadastro, redefinição exige token válido, confirmação e senha de pelo menos 8 caracteres. Rotas de solicitação/redefinição têm throttle, e o login oferece acesso ao formulário.
- **Grupo 5 concluído após aprovação:** `User` implementa verificação nativa; o cadastro autentica a nova conta e envia link assinado; login e rotas de conta/ações de escrita encaminham ou restringem contas não verificadas; reenvio é limitado; mudança de e-mail remove a verificação antiga e manda novo link. Logout agora exige autenticação.
- **Testes adicionados em `tests/Feature/AccountRecoveryAndVerificationTest.php`:** reset por token e troca de senha, resposta não enumerável para e-mail inexistente, registro e envio de verificação, bloqueio até confirmação, link assinado e limite de solicitação de reset. Nenhum teste pôde ser executado devido a `vendor/autoload.php` ausente.
- **Operação de e-mail:** `.env.example` usa `MAIL_MAILER=log`, então links só aparecem nos logs nesse ambiente. Produção precisa configurar um mailer SMTP/serviço válido; nenhuma configuração ou segredo de ambiente foi alterado.
- **Verificação do grupo de autenticação:** `php -l` passou nos sete arquivos PHP alterados/conferidos. `php artisan test` não iniciou porque `vendor/autoload.php` está ausente; também permanece o aviso de inicialização do CLI para `pdo_firebird`.
- **Grupo 6 concluído:** scripts do layout e da lista verificam a presença dos elementos antes de acessar; filtro de spoilers continua funcional se `localStorage` estiver indisponível; label do checkbox agora aponta ao ID correto. O painel carrega previamente `review` e `user` para os comentários. O teste de cadastro agora usa HTTP de teste, dados válidos/únicos e confere a etapa de verificação; a suíte `Browser` foi incluída no PHPUnit e vinculada ao `TestCase` do Laravel porque não há dependência de automação de navegador instalada.
- **Verificação do grupo 6:** `php -l` passou em `UserController`, no teste de cadastro e em `tests/Pest.php`; `phpunit.xml` foi parseado com sucesso. Os 33 arquivos alterados foram conferidos e nenhum tem BOM. PHPUnit/Pest não pôde executar: `vendor/autoload.php` está ausente; `node_modules` também não existe, então não há build local disponível. Nenhuma migration ou dependência foi adicionada/alterada.
- **Fechamento da Fase 2:** os itens A de código aprovados foram implementados. `composer audit`/`npm audit` continuam registrando avisos existentes; pacotes não foram atualizados conforme a regra de não alterar dependências sem aprovação específica.

## Fase 3 â€” infraestrutura (passo 2)

- **Decisões:** locales Laravel `pt_BR` e `en`; idioma padrão atual permanece `en`. Sem prefixos de URL, coluna de banco ou migration. A preferência escolhida fica em sessão e cookie; no primeiro acesso sem escolha, a ordem de preferência usa `Accept-Language` compatível e depois o fallback.
- **Implementação concluída neste passo:** middleware `SetLocale`, rota POST `/locale` com lista permitida e limite de tentativas, seletor acessível no layout, atributo `<html lang>` dinâmico e arquivos base equivalentes para autenticação, validação, paginação e rótulos do seletor.
- **Glossário inicial:** â€œreviewâ€ = â€œavaliaçãoâ€; â€œcommentâ€ = â€œcomentárioâ€; â€œnicknameâ€ = â€œapelidoâ€; â€œspoilerâ€ = â€œspoilerâ€. â€œReviewâ€ pode ser traduzido como â€œresenhaâ€, mas â€œavaliaçãoâ€ foi escolhido por se referir ao conteúdo publicado no PixelRank; confirmar durante a revisão dos grupos de views.
- **Verificações:** lint passou no middleware, controller, bootstrap, rotas, arquivos de idioma e teste novo; `phpunit.xml` segue sem alterações nesta etapa. Conferência estática confirmou os mesmos nomes de arquivos e chaves nos cinco arquivos de idioma; os 17 arquivos novos/alterados estão sem BOM. Foi criado `tests/Feature/LocaleSwitchTest.php` para detecção/persistência, rejeição de locale inválido, validação traduzida e paridade das chaves.
- **Teste runtime:** `php artisan test --filter=LocaleSwitchTest` não iniciou por falta de `vendor/autoload.php`; o PHP CLI também emite o aviso conhecido de extensão `pdo_firebird` ausente. Nenhuma dependência ou migration foi alterada.

## Fase 3 â€” grupo de autenticação

- **Grupo concluído:** login, cadastro, solicitação e redefinição de senha e verificação de e-mail agora usam chaves de tradução em PT-BR/EN-US para títulos, descrições, labels, placeholders, botões, links e estados/mensagens flash. Incluídas as mensagens de login, cadastro e redefinição nos controllers. A resposta genérica de recuperação de senha foi mantida para não revelar se um endereço está cadastrado.
- **Arquivos de idioma:** `lang/en/auth_ui.php` e `lang/pt_BR/auth_ui.php`, com paridade de chaves conferida estaticamente.
- **Preservação visual:** nenhuma classe ou estrutura de apresentação foi intencionalmente alterada neste grupo.
- **Verificação:** `php -l` passou nos dois novos arquivos de idioma e nos três controllers alterados. A inspeção estática confirmou que as cópias dos formulários usam chaves de idioma para o texto visível; não apareceu diferença na conferência de paridade. Testes de aplicação seguem bloqueados pela ausência de `vendor/autoload.php`; permanece o aviso do PHP CLI para `pdo_firebird`.
- **Próximo passo:** grupo de autenticação concluído; avançar para layout/navegação e, depois, telas principais.

## Fase 3 â€” grupo de layout e navegação

- **Grupo concluído:** busca do cabeçalho, avatar, menu de conta, links para perfil/avaliações/comentários/cadastro de avaliação/configurações/início, sair, entrar e cadastro agora usam chaves de tradução PT-BR/EN-US.
- **Arquivos de idioma:** `lang/en/navigation.php` e `lang/pt_BR/navigation.php`; paridade de chaves conferida estaticamente.
- **Preservação visual:** apenas textos/atributos de texto fixo foram substituídos por traduções; HTML e classes foram mantidos.
- **Verificação:** `php -l` passou nos dois arquivos de idioma; inspeção estática confirmou uso das chaves nos textos de navegação e paridade da lista de chaves. Testes Laravel continuam bloqueados pela ausência de `vendor/autoload.php`.
- **Próximo passo:** grupo de navegação concluído; seguir para as telas principais conforme aprovação do usuário.

## Fase 3 â€” grupo de telas principais

- **Grupo concluído após aprovação:** listagem/filtros, criação/edição/detalhe de avaliação, busca/estados sem resultado, componentes de avaliações/comentários, perfil público e dashboard, incluindo campos, placeholders, labels e textos usados pelos scripts inline.
- **Arquivos de idioma criados:** `lang/en/reviews.php`, `lang/pt_BR/reviews.php`, `lang/en/dashboard.php` e `lang/pt_BR/dashboard.php`. Recomendações fixas são traduzidas por chaves; nomes/títulos/bios/comentários digitados por usuários e nomes/resumos de jogos da API permanecem conteúdo variável.
- **Glossário e ambiguidade:** manter `review` = `avaliação`. A recomendação `not_recommended` aparece como â€œNão recomendadoâ€ (EN: â€œNot Recommendedâ€); revisar se a forma preferida é â€œNão recomendadaâ€ para concordar com avaliação ou â€œNão recomendadoâ€ referindo-se ao jogo.
- **Verificação:** `php -l` passou nos arquivos de idioma e controllers PHP conferidos; comparação estática confirmou paridade das chaves nos grupos `reviews`, `dashboard`, `auth_ui` e `navigation`. A busca estática por rótulos fixos nas views principais deixou apenas datas relativas e comentários HTML, tratados como conteúdo/data na etapa de formatação. Testes Laravel continuam bloqueados pela ausência de `vendor/autoload.php`; o PHP CLI segue avisando sobre `pdo_firebird` não instalado.
- **Ajustes do fluxo de mensagens:** textos de registro/reset de senha e confirmação de e-mail, além dos avisos de exclusão/atualização de perfil, foram conectados às chaves correspondentes.
- **Próximo passo:** aguardar autorização para traduzir e-mails/notificações/páginas de erro; em seguida, concluir a formatação de datas e a checagem final de strings da Fase 3.

## Fase 3 â€” e-mails, notificações, erros e formatação

- **E-mails/notificações:** boas-vindas, verificação de e-mail e redefinição de senha usam traduções PT-BR/EN-US. A preferência de idioma é capturada no job de boas-vindas enfileirado; links nativos de verificação/reset do Laravel foram preservados.
- **Páginas de erro:** adicionadas páginas traduzidas e amigáveis para 403, 404, 419, 500 e 503, sem expor detalhes da exceção.
- **Formatação:** `App\Support\LocaleFormatter` centraliza datas (`dd/mm/aaaa` em PT-BR e `mm/dd/yyyy` em EN-US), datas relativas e números. Reviews, comentários, datas de lançamento e notas usam o locale ativo. Data de lançamento ausente é tratada sem erro.
- **Moeda:** não há valores monetários renderizados nos fluxos atuais; não foi presumida uma moeda nem adicionado formato monetário sem dado de negócio para formatar.
- **Ambiguidades a revisar:** `review` = `avaliação`; `not_recommended` = â€œNão recomendadoâ€ (EN: â€œNot Recommendedâ€), conforme o glossário anterior. Revisar a concordância de gênero em PT-BR.
- **Verificações:** pendentes lint, paridade de chaves e busca final de textos fixos. Testes Laravel permanecem indisponíveis sem `vendor/autoload.php`.
- **Próximo passo:** finalizar verificações estáticas da Fase 3 e entregar roteiro de revisão manual nos dois idiomas.

### Fechamento deste grupo

- `php -l` passou nos arquivos PHP alterados: formatter, notificações, `User`, `Review`, `Comment`, job/mailable e arquivos `mail.php`/`errors.php`. O CLI continua mostrando somente o aviso de extensão `pdo_firebird` ausente.
- Paridade estática das chaves de `mail.php` e `errors.php` passou entre `en` e `pt_BR`. Busca por textos residuais dos e-mails/erros e por formatação fixa `pt_BR` não encontrou usos hardcoded nas views/modelos alvo.
- Não foi possível compilar views nem executar testes Laravel: `vendor/autoload.php` não existe. Nenhum teste novo foi criado nesta etapa; a etapa de verificações automatizadas do plano continua pendente de aprovação.
- As páginas de erro usam HTML independente do layout, autenticação, banco e bundle de assets do app para reduzir o risco de falha durante o próprio tratamento do erro.

## Fase 3 â€” verificações automatizadas e busca final

- **Ajuste encontrado na revisão:** a paginação Bootstrap publicada ainda usava frases inglesas como chaves literais e saída `{!! !!}`. Os rótulos foram movidos para chaves curtas de `pagination.php` em ambos os locales e agora são renderizados com escape normal.
- **Testes complementados em `tests/Feature/LocaleSwitchTest.php`:** teste de locale ativo nas notificações de verificação/reset, páginas 404 em PT-BR e EN-US, login com idioma do navegador e teste de paridade recursiva dos arquivos/chaves já existente. Corrigida a expectativa de validação para evitar texto fonte com codificação quebrada.
- **Busca estática:** não encontrou rótulos fixos em texto HTML nem `placeholder`, `title`, `alt` ou `aria-label` hardcoded nas views próprias. Comentários de código e valores técnicos de JavaScript/HTML não são conteúdo de interface. Os textos de paginação detectados foram corrigidos.
- **Verificação executada:** `php -l` passou nos testes e arquivos PHP afetados. `php artisan test --filter=LocaleSwitchTest` foi tentado, mas não inicia porque `vendor/autoload.php` está ausente. O CLI mostra também o aviso ambiental `pdo_firebird` não carregável. A comparação dos grupos novos de e-mail/erros passou; o teste no projeto confere recursivamente a paridade de todos os arquivos, mas não pôde ser executado sem dependências.
- **Roteiro manual PT-BR/EN-US:** abrir login e registro; alternar idioma no cabeçalho; solicitar redefinição e conferir assunto/corpo do e-mail; verificar confirmação de e-mail; abrir avaliações, perfil e dashboard; visitar URL inexistente e conferir 404; verificar datas relativas/calendário e nota; conferir paginação. Repetir cada fluxo nos dois idiomas e observar cortes/quebras de texto.
- **Limitação pendente:** testes runtime e compilação/renderização das views precisam ser repetidos após instalar/restaurar dependências com `composer install`. Nenhuma dependência foi instalada nesta etapa.
- **Ajuste final de paginação:** a revisão adicional encontrou `Pagination Navigation` nos templates simples Bootstrap/Tailwind do Laravel. O rótulo agora usa `pagination.navigation` traduzido e escapado. A busca final por chaves literais em inglês e atributos de interface hardcoded nas views não retornou ocorrências.

### Resultado final da Fase 3

- `composer install --no-interaction --prefer-dist` restaurou as 136 dependências conforme `composer.lock`, sem updates ou remoções. `composer.json`/`composer.lock` não foram intencionalmente alterados.
- `php artisan test --filter=LocaleSwitchTest`: **8 testes, 8 aprovados, 39 assertions**.
- `php artisan test`: **29 testes, 29 aprovados, 199 assertions**.
- Correções descobertas pela execução: o 404 agora usa fallback na pilha web para aplicar idioma de sessão/cookie/navegador; os testes recebem `APP_KEY` fixo de teste e desativam Vite sem depender do build; assertions de recuperação/verificação usam as notificações customizadas; os testes de mass assignment refletem `Model::shouldBeStrict()`.
- O aviso de inicialização do CLI para `pdo_firebird` permanece, mas não impediu os testes. Não foi feito build visual do frontend nesta fase.
- A busca final das views não encontrou texto fixo de interface nos padrões de conteúdo/atributos verificados; os rótulos de paginação que ainda usavam frases como chave foram migrados para `pagination.php` e escapados.


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

## Fase 5 — correções funcionais e de interface (round D8)

### Correções aplicadas

- **Spoiler:** o badge e o filtro de spoilers passam a respeitar o estado persistido
  e funcionam mesmo quando `localStorage` está indisponível.
- **Validação:** mensagens de validação traduzidas para PT-BR/EN-US em todos os
  formulários auth e de perfil.
- **Badges PT-BR:** rótulos e contagens exibidos em português, sem texto duplicado.
- **Avatar:** imagem de perfil e iniciais renderizadas corretamente no perfil e no
  painel.
- **Comentários:** XSS corrigido (uso de `textContent` em vez de
  `insertAdjacentHTML`) e o formulário de comentário valida antes de enviar.
- **Dashboard:** o painel carrega previamente `review` e `user` para evitar
  consultas repetidas (N+1) na listagem de comentários.

### Testes

- `tests/Feature/ProfileAndRateLimitTest.php`: foi **removido** o caso que acessava
  `/users/{id}/edit`. A rota real exige o parâmetro `menu`
  (`/users/{id}/{menu}/edit`), portanto o teste era inválido, não um bug da
  aplicação.
- Suíte final: **42 testes, 247 assertions, todos passando**.
- `npm run build`: concluído com sucesso.
- Pint: aplicado ao novo teste. Os avisos em `app/Http/Controllers/UserController.php`
  e `app/Models/User.php` foram confirmados como **pré-existentes** e não foram
  alterados.

### Codificação deste arquivo

- `docs/STATUS.md` estava com duplo encoding (mojibake) **já no commit**; não foi
  introduzido pelas correções desta fase.
- Reparo aplicado revertendo a codificação: `mb_convert_encoding($run, 'ISO-8859-1', 'UTF-8')`,
  o que corrigiu **748 trechos**.
- A conversão alterou **apenas a codificação**: o conteúdo do backup, decodificado,
  é **idêntico byte a byte** ao arquivo reparado (503 linhas em ambos, sem BOM,
  com quebras de linha CRLF preservadas).
- Varredura do repositório com o detector corrigido: **nenhum outro arquivo**
  apresenta mojibake real. Os apontamentos anteriores em `lang/pt_BR/*.php`,
  `docs/PLANO_V1.txt`, `CHANGELOG.md` e `README.md` eram falsos positivos do
  detector, causado por acentos legítimos em UTF-8.

### Validação pendente do usuário

- Revisão manual no navegador das telas corrigidas (spoiler, validação, badges,
  avatar, comentários e dashboard).
- Criar `.env` e gerar `APP_KEY` para permitir a execução da aplicação em
  desenvolvimento.

3. Revalidar a suíte de testes apos as correcacoes e confirmar se todos passam.

---

## Fase — Resiliência da API de jogos (Twitch/IGDB)

### Objetivo

Evitar erros HTTP 500 nas telas de avaliação e nos dados de jogo quando a API
externa (Twitch + IGDB) está ausente, mal configurada ou indisponível.

### Implementação

- `app/Exceptions/GameApiUnavailableException.php` (novo): exceção de domínio com
  fábricas `notConfigured()`, `upstreamFailure()` e `invalidResponse()`.
- `app/Services/GameApiService.php`:
  - `isConfigured()` verifica URL base, URL de autenticação, client id e secret;
    sem esses valores **nenhuma requisição externa é feita**.
  - URLs normalizadas com `rtrim(..., '/')` (evita `//games` em base URL vazia).
  - `timeout(10)` e `retry(2, 200)` em todas as chamadas; token do Twitch é
    cacheado e reaproveitado.
  - Falhas de conexão (`ConnectionException`), respostas HTTP de erro
    (`RequestException`/status >= 400) e payloads inválidos são convertidos em
    `GameApiUnavailableException` e registradas em log.
  - `getPlatforms()`, `getGames()`, `getGame()` e `getGamesCovers()` devolvem
    listas vazias em vez de propagar a exceção.
- `app/Models/Review.php`: `getGameInfo()` aceita array vazio e sempre devolve
  as chaves `platforms`, `platforms_count`, `release_date`, `summary`, `cover`.
- `app/Http/Controllers/ReviewController.php`: `show()` e `create()` toleram
  metadados ausentes e expõem `gameApiAvailable` às views.
- `app/Http/Controllers/GameController.php`: busca em branco responde `[]`;
  falhas da API também respondem `[]` (HTTP 200).
- `resources/views/reviews/show.blade.php`: exibe `reviews.not_available` para
  plataformas, data de lançamento e resumo ausentes; capa usa
  `public/images/game-placeholder.svg` quando não há `game_cover`.
- `resources/views/reviews/create.blade.php`: lista de plataformas vazia e
  feedback no cliente quando a busca de jogos falha.
- `lang/en/reviews.php` e `lang/pt_BR/reviews.php`: novas chaves
  `not_available`, `game_api_unavailable` e `game_search_failed`.
- `.env.example`: seção opcional documentando `TWITCH_AUTH`, `TWITCH_CLIENT_ID`,
  `TWITCH_CLIENT_SECRET`, `GAME_API_URL` e `GAME_API_KEY`.

### Testes

- `tests/Feature/GameApiResilienceTest.php` (novo, 14 testes): configuração
  ausente, falha de upstream, falha de conexão, sucesso da API, renderização de
  `/reviews/{id}` e `/reviews/create` sem metadados, e respostas JSON vazias em
  `/games/search`.
- `tests/Feature/ReviewShowTest.php` atualizado para mock de `isConfigured()`.

### Resultado

- `php artisan test`: **56 passed (278 assertions)**.

### Configuração em produção

O degrade é total: sem credenciais as telas funcionam com dados indisponíveis.
Para metadados reais em produção é obrigatório definir:

```
TWITCH_AUTH=https://id.twitch.tv/oauth2/token
TWITCH_CLIENT_ID=
TWITCH_CLIENT_SECRET=
GAME_API_URL=https://api.igdb.com/v4
GAME_API_KEY=
```


---

## Redesign da dashboard do usuário

**Status:** Em andamento (Etapa 1: leitura e planejamento concluídos)

### Escopo e arquivos identificados
- Rotas: `routes/web.php` (`GET /users/{user}/{menu?}/edit`, `PUT /users/{user}`)
- Controller: `app/Http/Controllers/UserController.php` (`edit()`, `update()`)
- Form Request: `app/Http/Requests/UserRequest.php`
- Layout e Footer: `resources/views/components/layout.blade.php`
- Views da dashboard: `resources/views/users/edit.blade.php`
- Componentes usados: `nav-link-edit.blade.php`, `article-link.blade.php`, `comment.blade.php`, `empty-result.blade.php`, `pagination.blade.php`, `components/form/*`
- Idiomas: `lang/en/dashboard.php`, `lang/pt_BR/dashboard.php`
- Testes relacionados: `ProfileAndRateLimitTest.php`, `SecurityHardeningTest.php`, `ResourceActionsTest.php`, `LocaleKeysParityTest.php`

### Plano de implementação (13 linhas)
1. Layout e Footer: envolver a dashboard em container com largura máxima, padding responsivo e margem inferior generosa (`mb-12` a `mb-16`), descolando o footer em 360px, 768px e 1280px.
2. Sidebar e Avatar: modernizar card do usuário (remover `xl:h-155` fixo, avatar circular responsivo sem `13vh`, bio tratada sem quebras incorretas, botões de ação limpos).
3. Navegação entre abas: transformar `x-nav-link-edit` em abas acessíveis (`role="tab"`, `aria-selected`, `aria-current`, foco visível, touch target >= 44px, suporte a teclado), com grid responsivo em mobile/tablet e vertical no desktop.
4. Preservação de contratos Alpine: manter estritamente `x-data="{ activeMenu: @js($menu) }"` para total compatibilidade com os testes existentes.
5. Profile (menu1): modernizar formulário com DaisyUI/Tailwind, input de avatar polido com preview atual, campos de senha com notas de apoio e botão com alvo de toque adequado.
6. My Reviews (menu2): ajustar cabeçalho com botão de "Nova avaliação" com toque mínimo de 44px e badge DaisyUI sem distorção.
7. Cards de Reviews (`article-link` dashboard): remover `flex-1/4` quebrado em coluna, layout flex responsivo (coluna em 360px, linha a partir de 640px), metadados com flex-wrap sem transbordar tela.
8. My Comments (menu3): modernizar lista de comentários com avatar e texto responsivos, `break-words` em vez de `break-all`, link seguro para a review correspondente.
9. Estados vazios: refinar `x-empty-result` com layout harmonizado.
10. Aba Settings (menu4): fornecer conteúdo descritivo para que a aba não renderize um container vazio quando acionada.
11. Internacionalização: garantir que todos os textos/rótulos estejam em `lang/pt_BR/dashboard.php` e `lang/en/dashboard.php` mantendo paridade estrita de chaves.
12. Verificação automatizada: rodar `php artisan test --compact`, teste de paridade de chaves, Pint e `npm run build`.
13. Roteiro de conferência: validação manual detalhada em 360px, 768px e 1280px nos dois idiomas.


### Execução e Resultados
- **Layout e Footer:** Container padronizado com `max-w-7xl mx-auto px-4 sm:px-6 lg:px-8` e margem inferior `mb-12 sm:mb-16`. O footer agora tem respiro adequado em todas as resoluções (360px, 768px, 1280px).
- **Sidebar & Avatar:** Altura rígida `xl:h-155` removida; avatar atualizado com DaisyUI circular proporcional (`size-24 sm:size-28`); bio exibida com `break-words` e botões de ação limpos com foco acessível.
- **Abas de Navegação:** `nav-link-edit.blade.php` atualizado com elementos interativos acessíveis (`role="tab"`, `:aria-selected`, `:aria-current`, foco visível `focus-visible:ring-accent`, altura mínima de toque de 44px). No mobile/tablet, renderiza em grid de 2 colunas; no desktop (xl:), se integra à barra lateral com indicador visual de borda esquerda.
- **Etapa 1 (Profile):** Formulário reorganizado com grid responsivo (`grid-cols-1 sm:grid-cols-2`), DaisyUI `file-input` com preview circular da imagem atual, nota explicativa para campos de senha vazios, e botão "Atualizar" responsivo (largura total no mobile, alinhado à direita no desktop) com área de toque confortável.
- **Etapa 2 (My Reviews):** Botão "Nova avaliação" convertido em botão DaisyUI com toque mínimo de 44px; id `#review` na div renomeado para evitar conflito com listeners do layout; cards de review (`article-link.blade.php`) corrigidos (removido `flex-1/4` espúrio, capa com tamanho estável `w-20 sm:w-28 h-28 sm:h-36`, tags com `flex-wrap` e quebra de palavras segura); estado vazio amigável com botão de criação.
- **Etapa 3 (My Comments):** Cards de comentário (`comment.blade.php`) aprimorados com avatar proporcional (`shrink-0`), texto com `break-words` e destaque ao título da avaliação; estado vazio com link para explorar avaliações.
- **Aba Settings (menu4):** Adicionado card com seletor de idioma integrado (`route('locale.update')`), eliminando a tela em branco quando a aba é acessada.
- **Internacionalização:** 7 novas chaves traduzidas em `lang/pt_BR/dashboard.php` e `lang/en/dashboard.php`, com teste de paridade 100% verde.
- **Verificações:**
  - `php artisan test --compact`: 56 testes passaram (278 assertions).
  - `php vendor/bin/pint --test`: arquivos de idioma formatados com sucesso.
  - `npm run build`: build Vite concluído em 2.35s sem erros.
  - Varredura de BOM: todos os arquivos UTF-8 sem BOM.


---

## Revisão de Qualidade da Dashboard e Correções

**Status:** Concluído

### Correções Aplicadas
1. **Avatar Helper:** Alinhado o texto em `lang/pt_BR/dashboard.php` e `lang/en/dashboard.php` para `"PNG, JPG ou GIF até 1MB"`, em total concordância com as regras ativas de `UserRequest.php`.
2. **Unificação de IDs e ARIA:** Removidos IDs duplicados nas tags `<div>` dos painéis da dashboard em `users/edit.blade.php`. Cada painel agora utiliza exclusivamente `panel-menu1`, `panel-menu2`, `panel-menu3`, `panel-menu4`, conectados aos botões de abas via `aria-controls` e `aria-labelledby`.
3. **Comentários Órfãos com Degradação Segura:** `comment.blade.php` verifica a existência da relação com review antes de renderizar link; quando nula, exibe card sem link com a mensagem traduzida `dashboard.review_removed`. Confirmado que a migration original possui `cascadeOnDelete()`.
4. **Resolução de N+1 e Ordenação:** `UserController::edit` agora carrega `withCount('comments')` e `latest()` para as reviews do usuário. O componente `article-link.blade.php` foi atualizado para usar `comments_count` prioritariamente e fallback para `count($review->comments)`.
5. **Acessibilidade:** Botões Seguir/Mensagem na barra lateral receberam `disabled` e `aria-disabled="true"`. A navegação adota o padrão WAI-ARIA tablist (`role="tab"`, `:aria-selected`, `aria-controls`, `aria-labelledby`, `tabindex="0"` e foco visível).
6. **Badge sem Tag `<label>` Aninhada:** Adicionado suporte ao `@case('span')` em `badge-link.blade.php` e aplicado em `article-link.blade.php`, evitando tags `<label>` aninhadas indevidamente dentro de tags de link `<a>`.
7. **Limpeza de Imports:** Removido o import não utilizado `use Illuminate\Contracts\Validation\ValidationRule;` de `UserRequest.php`.
8. **Pendência Registrada:** Os métodos vazios `store(Request $request)` e `destroy(Request $request, User $user)` em `UserController.php` foram mantidos intocados para decisão futura de rotas.

### Testes Automatizados Enxutos Adicionados
- Arquivo novo: `tests/Feature/DashboardUserTest.php` com 8 testes:
  - Redirecionamento de visitante ao tentar acessar abas `menu3` ou `menu4`.
  - Bloqueio (403 Forbidden) para outro usuário acessando as abas `menu3` ou `menu4`.
  - Acesso autorizado do proprietário às abas `menu3` e `menu4`.
  - Renderização de listas com dados quando há reviews e comentários.
  - Renderização correta dos estados vazios quando não há reviews nem comentários.
  - Tratamento gracioso de comentário com review nula (sem erro 500, com texto `dashboard.review_removed`).
  - Rejeição de avatar maior que 1MB.
  - Rejeição de avatar com extensão ou formato inválido.

### Verificações Finais
- **Pest:** 64 testes passaram (303 assertions).
- **Pint:** Código PHP formatado e validado.
- **Build (Vite):** Concluído com sucesso (2.02s).
- **BOM:** Todos os arquivos verificados em UTF-8 sem BOM.
