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

- **Tarefa inicial — detecção e estado base:** concluída. Stack identificada; execução de testes bloqueada pela ausência de `vendor`.
- **Fase 1 — auditoria (somente leitura, exceto este arquivo):** concluída em 2026-10-19 por inspeção estática. Relatório e prioridades abaixo; aguardar revisão do usuário antes da Fase 2.
- **Fase 2 — segurança e bugs (grupo A aprovado):** implementação concluída nos grupos 1 a 6; testes de regressão/autorização adicionados. A validação em runtime continua pendente pela ausência de `vendor/autoload.php`.
- **Fase 3 — internacionalização PT-BR/EN-US:** infraestrutura e grupos de autenticação, layout/navegação e telas principais concluídos; pendem e-mails/notificações/erros, formatação por idioma e verificações finais.
- **Fase 4 — estilo e responsividade:** corrigir apenas defeitos do grupo B aprovado, preservando identidade visual; conferir páginas nos dois idiomas e build quando aplicável.
- **Fase 5 — preparação da v1.0:** finalizar documentação em português, checklist de produção, changelog e checklist de release; sugerir commit/tag sem executá-los.

## Decisões e limites de trabalho

- Preservar identidade visual, arquitetura, rotas públicas, nomes de tabelas/colunas, migrations existentes e idioma padrão atual.
- Não adicionar, remover ou atualizar dependências sem aprovação. Não alterar dados reais nem executar comandos destrutivos no banco.
- Trabalhar uma fase por vez; ao concluir, atualizar este status e parar para aguardar o próximo pedido/aprovação.
- Escrever arquivos em UTF-8 sem BOM; não fazer commit.

## Pendências

- Instalar/restaurar dependências do projeto quando autorizado/necessário para executar os testes (`vendor/autoload.php` está ausente). A tarefa inicial não autoriza instalar pacotes.
- Executar verificações finais de PHP/lint, revisão manual das rotas/views e, quando as dependências estiverem disponíveis, rodar a suíte de testes.
- Glossário de domínio e traduções ambíguas: ainda não levantados; registrar durante a Fase 3.

## Fase 1 — resumo da auditoria

### Achados

- **Alta — XSS ao inserir comentários:** `resources/views/reviews/show.blade.php` concatena texto controlado por usuários em `insertAdjacentHTML`. Um comentário pode inserir marcação/script que executa no navegador de quem abre a avaliação. Correção proposta: criar nós DOM e usar `textContent` para os valores.
- **Alta — valor de rota dentro de expressão Alpine:** `resources/views/users/edit.blade.php` interpola `menu` em uma expressão JavaScript dentro de `x-data`. Escapamento HTML não é garantia suficiente nesse contexto. Correção proposta: serializar o valor para JavaScript com mecanismo próprio do Blade e limitar os valores aceitos.
- **Média — atribuição em massa global:** `app/Providers/AppServiceProvider.php` chama `Model::unguard()`, desativando a proteção `$fillable` declarada em `app/Models/User.php`. Hoje alguns controllers montam arrays explícitos, mas a proteção fica inativa para toda a aplicação. Correção proposta: retirar o unguard global e declarar campos permitidos por modelo.
- **Média — sem limite de tentativas:** não há `throttle` aparente nas rotas de login/registro nem no endpoint público que consulta IGDB (`/games/search`). Isso facilita tentativas de senha e abuso da cota da API. Correção proposta: limites por IP/identidade nas rotas adequadas.
- **Média — edição de perfil não conclui:** `app/Http/Controllers/UserController.php` usa regras `unique` sem ignorar o próprio usuário, preenche campos de senha com o hash existente e termina com `$user->save;` sem chamar o método. A edição tende a falhar na validação e, mesmo se passar, não persiste. Correção proposta: ignorar o próprio registro nas regras, deixar senha vazia quando não for trocada e salvar o modelo.
- **Média — rotas sem implementação ou views:** `UserController::index/show` apontam para `users.index` e `users.show`, que não existem; `ReviewController::edit/update` e `CommentController::index/update` estão vazios apesar de rotas ativas. Essas URLs podem terminar em erro em vez de resposta útil. Correção proposta: completar ou retirar apenas após confirmar o comportamento pretendido.
- **Média — testes de browser incoerentes com o registro:** `tests/Browser/ExampleTest.php` omite nickname e confirmação de senha, ambos exigidos, e espera redirecionamento diferente do controller. A cobertura de feature/autorização está ausente; há apenas um teste unitário trivial e esse exemplo de browser.
- **Média — erro JavaScript global e no estado vazio:** `resources/views/components/layout.blade.php` liga um listener a `#review` em toda página, embora esse elemento não exista em várias delas. `resources/views/reviews/index.blade.php` acessa o checkbox de spoilers mesmo quando a lista vazia não o renderiza. Correção proposta: proteger inicializações por existência do elemento.
- **Média — dashboard pode fazer consultas repetidas:** `UserController::edit` pagina comentários sem carregar previamente `review` e `user`, relações acessadas por `components/comment.blade.php`. Correção proposta: eager loading das relações usadas.
- **Média — formulários/fluxos faltantes para produção:** não há rotas de redefinição de senha nem verificação de e-mail; confirmar se recuperação de conta e verificação são requisitos da v1.0. O mail atual é apenas uma mensagem de boas-vindas.
- **Média — responsividade estática:** layout do cabeçalho e busca usa larguras fixas; filtros ficam em linha; dashboard e formulário de avaliação usam `flex-row` e larguras sem regras móveis claras. Risco de conteúdo comprimido/rolagem em telas estreitas. Correção proposta: ajustes localizados nas classes existentes, após conferir em navegador nos tamanhos pedidos.
- **Média — configuração de exemplo é local:** `.env.example` traz `APP_ENV=local`, `APP_DEBUG=true`, usuário MySQL `root` sem senha, `MAIL_MAILER=log`, e não declara variáveis `TWITCH_*`/`GAME_API_URL` que `config/services.php` consome. Deploy copiado sem revisão pode expor erros ou não enviar e-mail/consultar IGDB. `config/session.php` permite configurar cookie seguro, mas o exemplo não ativa essa opção.
- **Baixa — logs incluem e-mail:** `app/Jobs/NewUserNotification.php` registra o e-mail do novo usuário; remover ou reduzir dados pessoais dos logs.
- **Baixa — logout sem middleware `auth`:** `DELETE /logout` é público; a operação só encerra a sessão da requisição e usa CSRF, mas deveria ficar coerente com os demais fluxos autenticados.
- **Baixa — privacidade de SQLite no controle de versão:** `.gitignore` não ignora explicitamente bancos SQLite. Não há arquivo SQLite listado na árvore atual; o diretório de trabalho não contém `.git`, então o histórico e os arquivos efetivamente rastreados não puderam ser verificados.

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

- **A — corrigir antes da v1.0:** XSS na renderização de comentário; expressão Alpine não serializada; remover `Model::unguard()`; rate limit de login/registro/consulta IGDB; corrigir edição de perfil; decidir recuperação/verificação de conta; corrigir rotas que apontam para views/métodos vazios; alinhar e corrigir testes de registro/autorização.
- **B — responsividade/acessibilidade sem redesenho:** conferir tamanhos 360/768/1280px; ajustar quebra do cabeçalho, filtros, dashboard e formulário de avaliação; conferir foco visível, labels associadas, checkbox de spoilers, alvos de toque, contraste e `lang` correto.
- **C — pode ficar para v1.1:** inventário/tradução PT-BR/EN-US (Fase 3 já tem execução planejada separada), acabamento do README/CHANGELOG e lançamento, remoção de e-mail dos logs, índice de `game_id`, ajustes operacionais de backup/observabilidade.

## Fase 2 — progresso

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

## Fase 3 — infraestrutura (passo 2)

- **Decisões:** locales Laravel `pt_BR` e `en`; idioma padrão atual permanece `en`. Sem prefixos de URL, coluna de banco ou migration. A preferência escolhida fica em sessão e cookie; no primeiro acesso sem escolha, a ordem de preferência usa `Accept-Language` compatível e depois o fallback.
- **Implementação concluída neste passo:** middleware `SetLocale`, rota POST `/locale` com lista permitida e limite de tentativas, seletor acessível no layout, atributo `<html lang>` dinâmico e arquivos base equivalentes para autenticação, validação, paginação e rótulos do seletor.
- **Glossário inicial:** “review” = “avaliação”; “comment” = “comentário”; “nickname” = “apelido”; “spoiler” = “spoiler”. “Review” pode ser traduzido como “resenha”, mas “avaliação” foi escolhido por se referir ao conteúdo publicado no PixelRank; confirmar durante a revisão dos grupos de views.
- **Verificações:** lint passou no middleware, controller, bootstrap, rotas, arquivos de idioma e teste novo; `phpunit.xml` segue sem alterações nesta etapa. Conferência estática confirmou os mesmos nomes de arquivos e chaves nos cinco arquivos de idioma; os 17 arquivos novos/alterados estão sem BOM. Foi criado `tests/Feature/LocaleSwitchTest.php` para detecção/persistência, rejeição de locale inválido, validação traduzida e paridade das chaves.
- **Teste runtime:** `php artisan test --filter=LocaleSwitchTest` não iniciou por falta de `vendor/autoload.php`; o PHP CLI também emite o aviso conhecido de extensão `pdo_firebird` ausente. Nenhuma dependência ou migration foi alterada.

## Fase 3 — grupo de autenticação

- **Grupo concluído:** login, cadastro, solicitação e redefinição de senha e verificação de e-mail agora usam chaves de tradução em PT-BR/EN-US para títulos, descrições, labels, placeholders, botões, links e estados/mensagens flash. Incluídas as mensagens de login, cadastro e redefinição nos controllers. A resposta genérica de recuperação de senha foi mantida para não revelar se um endereço está cadastrado.
- **Arquivos de idioma:** `lang/en/auth_ui.php` e `lang/pt_BR/auth_ui.php`, com paridade de chaves conferida estaticamente.
- **Preservação visual:** nenhuma classe ou estrutura de apresentação foi intencionalmente alterada neste grupo.
- **Verificação:** `php -l` passou nos dois novos arquivos de idioma e nos três controllers alterados. A inspeção estática confirmou que as cópias dos formulários usam chaves de idioma para o texto visível; não apareceu diferença na conferência de paridade. Testes de aplicação seguem bloqueados pela ausência de `vendor/autoload.php`; permanece o aviso do PHP CLI para `pdo_firebird`.
- **Próximo passo:** grupo de autenticação concluído; avançar para layout/navegação e, depois, telas principais.

## Fase 3 — grupo de layout e navegação

- **Grupo concluído:** busca do cabeçalho, avatar, menu de conta, links para perfil/avaliações/comentários/cadastro de avaliação/configurações/início, sair, entrar e cadastro agora usam chaves de tradução PT-BR/EN-US.
- **Arquivos de idioma:** `lang/en/navigation.php` e `lang/pt_BR/navigation.php`; paridade de chaves conferida estaticamente.
- **Preservação visual:** apenas textos/atributos de texto fixo foram substituídos por traduções; HTML e classes foram mantidos.
- **Verificação:** `php -l` passou nos dois arquivos de idioma; inspeção estática confirmou uso das chaves nos textos de navegação e paridade da lista de chaves. Testes Laravel continuam bloqueados pela ausência de `vendor/autoload.php`.
- **Próximo passo:** grupo de navegação concluído; seguir para as telas principais conforme aprovação do usuário.

## Fase 3 — grupo de telas principais

- **Grupo concluído após aprovação:** listagem/filtros, criação/edição/detalhe de avaliação, busca/estados sem resultado, componentes de avaliações/comentários, perfil público e dashboard, incluindo campos, placeholders, labels e textos usados pelos scripts inline.
- **Arquivos de idioma criados:** `lang/en/reviews.php`, `lang/pt_BR/reviews.php`, `lang/en/dashboard.php` e `lang/pt_BR/dashboard.php`. Recomendações fixas são traduzidas por chaves; nomes/títulos/bios/comentários digitados por usuários e nomes/resumos de jogos da API permanecem conteúdo variável.
- **Glossário e ambiguidade:** manter `review` = `avaliação`. A recomendação `not_recommended` aparece como “Não recomendado” (EN: “Not Recommended”); revisar se a forma preferida é “Não recomendada” para concordar com avaliação ou “Não recomendado” referindo-se ao jogo.
- **Verificação:** `php -l` passou nos arquivos de idioma e controllers PHP conferidos; comparação estática confirmou paridade das chaves nos grupos `reviews`, `dashboard`, `auth_ui` e `navigation`. A busca estática por rótulos fixos nas views principais deixou apenas datas relativas e comentários HTML, tratados como conteúdo/data na etapa de formatação. Testes Laravel continuam bloqueados pela ausência de `vendor/autoload.php`; o PHP CLI segue avisando sobre `pdo_firebird` não instalado.
- **Ajustes do fluxo de mensagens:** textos de registro/reset de senha e confirmação de e-mail, além dos avisos de exclusão/atualização de perfil, foram conectados às chaves correspondentes.
- **Próximo passo:** aguardar autorização para traduzir e-mails/notificações/páginas de erro; em seguida, concluir a formatação de datas e a checagem final de strings da Fase 3.

## Fase 3 — e-mails, notificações, erros e formatação

- **E-mails/notificações:** boas-vindas, verificação de e-mail e redefinição de senha usam traduções PT-BR/EN-US. A preferência de idioma é capturada no job de boas-vindas enfileirado; links nativos de verificação/reset do Laravel foram preservados.
- **Páginas de erro:** adicionadas páginas traduzidas e amigáveis para 403, 404, 419, 500 e 503, sem expor detalhes da exceção.
- **Formatação:** `App\Support\LocaleFormatter` centraliza datas (`dd/mm/aaaa` em PT-BR e `mm/dd/yyyy` em EN-US), datas relativas e números. Reviews, comentários, datas de lançamento e notas usam o locale ativo. Data de lançamento ausente é tratada sem erro.
- **Moeda:** não há valores monetários renderizados nos fluxos atuais; não foi presumida uma moeda nem adicionado formato monetário sem dado de negócio para formatar.
- **Ambiguidades a revisar:** `review` = `avaliação`; `not_recommended` = “Não recomendado” (EN: “Not Recommended”), conforme o glossário anterior. Revisar a concordância de gênero em PT-BR.
- **Verificações:** pendentes lint, paridade de chaves e busca final de textos fixos. Testes Laravel permanecem indisponíveis sem `vendor/autoload.php`.
- **Próximo passo:** finalizar verificações estáticas da Fase 3 e entregar roteiro de revisão manual nos dois idiomas.

### Fechamento deste grupo

- `php -l` passou nos arquivos PHP alterados: formatter, notificações, `User`, `Review`, `Comment`, job/mailable e arquivos `mail.php`/`errors.php`. O CLI continua mostrando somente o aviso de extensão `pdo_firebird` ausente.
- Paridade estática das chaves de `mail.php` e `errors.php` passou entre `en` e `pt_BR`. Busca por textos residuais dos e-mails/erros e por formatação fixa `pt_BR` não encontrou usos hardcoded nas views/modelos alvo.
- Não foi possível compilar views nem executar testes Laravel: `vendor/autoload.php` não existe. Nenhum teste novo foi criado nesta etapa; a etapa de verificações automatizadas do plano continua pendente de aprovação.
- As páginas de erro usam HTML independente do layout, autenticação, banco e bundle de assets do app para reduzir o risco de falha durante o próprio tratamento do erro.

## Fase 3 — verificações automatizadas e busca final

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
