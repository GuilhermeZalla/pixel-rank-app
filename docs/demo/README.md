# Capturas e demonstrações do PixelRank

`record.mjs` abre a aplicação em Chromium e registra navegação e interações reais. `seed.php` prepara somente o conjunto fictício usado nessa gravação. O processo cria um SQLite temporário, gera uma senha aleatória para as contas verificadas, desativa o envio de e-mails e a integração de metadados Twitch/IGDB e encerra o servidor e o navegador ao concluir. O banco e o `.env` de desenvolvimento são preservados.

Requisitos: dependências PHP da aplicação, build do Vite, Node.js, Playwright com Chromium instalado e FFmpeg com suporte a GIF. As ferramentas de captura podem ser instaladas em uma pasta separada; não precisam ser adicionadas às dependências do projeto.

Na raiz, com as ferramentas disponíveis:

```powershell
npm.cmd run build
$env:PLAYWRIGHT_MODULE='C:/caminho/para/ferramentas/node_modules/playwright/index.mjs'
$env:FFMPEG_PATH='C:/caminho/para/ffmpeg.exe'
node docs/demo/record.mjs
```

Se Playwright estiver disponível para importação e FFmpeg estiver no PATH, as duas variáveis são opcionais. Para instalar Playwright fora do projeto, use uma pasta própria de ferramentas e execute nela `npm.cmd install playwright` e `npx.cmd playwright install chromium`.

Opções de ambiente:

| Variável | Finalidade |
| --- | --- |
| `PHP_BINARY` | Executável PHP; padrão `php` |
| `PHP_ARGS` | Lista JSON de argumentos extras do PHP, quando necessário para o ambiente local |
| `PLAYWRIGHT_MODULE` | Caminho absoluto do `index.mjs` do Playwright |
| `FFMPEG_PATH` | Executável FFmpeg; padrão `ffmpeg` |
| `BROWSER_PATH` | Executável Chromium alternativo |
| `DEMO_PORT` | Porta local livre; padrão `8137` |

O processo desativa OPcache e a leitura adicional de arquivos INI apenas nos subprocessos PHP de captura. O `php.ini` principal continua sendo usado e precisa habilitar as extensões do Laravel, incluindo OpenSSL e PDO SQLite. `PHP_ARGS` permite carregar uma extensão nesse processo sem editar a configuração global.

As saídas são quatro GIFs em `docs/demo/`, 14 PNGs em `docs/screenshots/` e `capture-report.json`, com dimensões, quantidade de quadros e checagem de imagens. Os GIFs usam capturas sucessivas do navegador, com quatro quadros por segundo e repetição contínua. Os desktops são reduzidos para 960 px de largura; o celular mantém 390 px e aparece no README com largura de 300 px.

As capturas estáticas usam viewport de 1440 × 1000 no desktop e 390 × 844 no celular. O script verifica carregamento das páginas, estilos e ausência de rolagem horizontal. As interações incluem publicação de comentário, curtida, resposta e navegação pelas abas do painel. A criação de avaliação é capturada como formulário; não há publicação com respostas simuladas do IGDB.

As capas continuam vindo do serviço de imagens do IGDB; os avatares padrão vêm de UI Avatars. A conexão com esses serviços deve estar disponível para que as imagens carreguem. Os personagens, contas, textos e discussões são fictícios; as marcas e capas de jogos pertencem aos respectivos titulares.

O relatório registra uma falha preexistente na URL da capa da página individual de avaliação: as diretivas Blade dentro do atributo `src` do componente chegam literalmente ao HTML. A Home e os avatares carregaram corretamente na gravação final. As capturas mantêm esse comportamento real; a ocorrência está detalhada em `docs/STATUS.md`.
