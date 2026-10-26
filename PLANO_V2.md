# Plano V2 - Melhorias Futuras

Este plano documenta as implementações de Nível 2 e Nível 3 para a tela de configurações do usuário, deixadas para versões futuras após o Nível 1 (já implementado na V1.0).

## Nível 1 - Implementado (v1.0) ✅

- [x] Seção de Segurança: link redefinição de senha, reenvio de verificação de e-mail, status de verificação
- [x] Seção de Preferências de Exibição: toggle "Ocultar spoilers por padrão", toggle "Mostrar contagem de views"
- [x] Seção de Privacidade: toggle "Perfil privado", toggle "Esconder e-mail"
- [x] Migration: 4 colunas booleanas na tabela `users` (executada em 2026-10-22)
- [x] Validação via `UserRequest`
- [x] Salvar configurações via `UserController@update`
- [x] View menu4 atualizada com todas as seções
- [x] Componente `toggle-field` implementado
- [x] Traduções PT-BR/EN adicionadas
- [x] Verificado via `php artisan migrate:status` em 2026-10-23

---

## Nível 2 - Média complexidade (v1.1) 📋

### Seção de Notificações (in-app)
- [x] Migration: tabela `notifications` (Laravel Notifications) - 2026-10-23
- [x] Modelo: usar trait `Notifiable` no `User` (já presente)
- [x] Notificações de comentários nas minhas reviews (`CommentOnReviewNotification`)
- [x] Notificações de respostas aos meus comentários (`ReplyToCommentNotification` - campo `reply_to_id` adicionado à tabela comments em 2026-10-23)
- [x] Toggle: "Receber email com novidades" (newsletter) - coluna `newsletter` na tabela users
- [x] Componente de UI: painel/lista de notificações (sininho) no header
- [x] Controller: marcar notificação como lida, listar notificações não lidas (`NotificationController`)
- [x] View: `resources/views/notifications/index.blade.php`
- [x] Rotas: `notifications.index`, `notifications.read`, `notifications.read-all`

### Seção de Dados
- [x] Botão: "Exportar meus dados" (JSON com reviews, comentários, perfil)
- [x] Botão: "Deletar minha conta" (com confirmação e aviso de irreversibilidade)
- [x] Rota de exportação com nome de arquivo formatado
- [x] Exclusão em cascata (reviews → comentários → usuário)
- [x] Modal de confirmação DaisyUI
- [x] Traduções PT-BR/EN adicionadas

### Seeder de Dados Padrão
- [x] Seeder para gerar pelo menos 30 reviews fakes (32 criados)
- [x] Seeder para gerar pelo menos 10 comentários fakes em cada review (260 criados)
- [x] Disponibilizar dados padrão ao iniciar o sistema para visualização
- [x] CommentFactory criado
- [x] Model Comment com HasFactory
- [x] DatabaseSeeder atualizado

### Layout de Emails
- [x] Melhorar layout e conteúdo dos emails transacionais (boas-vindas, redefinição de senha, verificação de e-mail e demais notificações)
- [x] Substituir o layout padrão do Laravel por um template customizado com identidade visual do Pixel Rank

---

## Nível 3 - Complexo (v2.0) 📋

### Tema
- [x] Seletor de tema (claro/escuro/sistema) - implementado em 2026-10-23
- [x] Persistência de preferência de tema (localStorage + cookie + banco) - implementado em 2026-10-23
- [x] Seletor disponível apenas na dashboard do usuário (menu4/edit - Configurações), removido do dropdown do cabeçalho

### Contas Conectadas
- [ ] Integração com Steam
- [ ] Integração com PlayStation Network
- [ ] Integração com Xbox

---

## Notas de Implementação

- Notificações internas (Nível 2) usam a tabela `notifications` do Laravel e componente de UI no header com contador de não lidas.
- Exportação de dados (Nível 2) usa coleções do Laravel para gerar JSON.
- Deleção de conta (Nível 2) feita com exclusão em cascata de reviews e comentários do usuário.
- `ReplyToCommentNotification` agora está ativa com o campo `reply_to_id` na tabela `comments`.


---

## Atualização complementar — Home PixelRank (2026-10-26)

**Status:** Proposta A implementada.

- A Home ganhou a seção “Em conversa”, com uma avaliação visualmente maior e até quatro cards menores ao lado, ordenados por comentários publicados nos últimos 15 dias. Em telas estreitas, os cards se reorganizam abaixo da principal.
- Se não houver atividade recente, a faixa usa as avaliações mais vistas e informa a métrica. Se não houver avaliações, a faixa fica oculta.
- A seção reutiliza avaliações, comentários, visualizações e componentes existentes; não requer migration, endpoint, dependência ou sistema de votos.
- Filtros, ordenação e paginação do feed permanecem independentes dos destaques. O convite de publicação aparece depois do feed e o estado vazio preserva o convite original.
- A apresentação prioriza as capas e exibe poucas informações nos cards (jogo, título e nota), preservando o aviso de spoiler. A interface recebeu traduções PT-BR/EN-US e foco de teclado.
- Validação: 4 testes específicos aprovados (18 assertions) e build Vite concluído após o ajuste visual, em 2026-10-26.
