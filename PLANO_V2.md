# Plano V2 - Melhorias Futuras

Este plano documenta as implementações de Nível 2 e Nível 3 para a tela de configurações do usuário, deixadas para versões futuras após o Nível 1 (já implementado na V1.0).

## Nível 1 - Implementado (v1.0) ✅

- [x] Seção de Segurança: link redefinição de senha, reenvio de verificação de e-mail, status de verificação
- [x] Seção de Preferências de Exibição: toggle "Ocultar spoilers por padrão", toggle "Mostrar contagem de views"
- [x] Seção de Privacidade: toggle "Perfil privado", toggle "Esconder e-mail"
- [x] Migration: 4 colunas booleanas na tabela `users`
- [x] Validação via `UserRequest`
- [x] Salvar configurações via `UserController@update`

---

## Nível 2 - Média complexidade (v1.1) 📋

### Seção de Notificações (in-app)
- [ ] Migration: tabela `notifications` (Laravel Notifications)
- [ ] Modelo: usar trait `Notifiable` no `User` (já presente)
- [ ] Notificações de comentários nas minhas reviews
- [ ] Notificações de respostas aos meus comentários
- [ ] Toggle: "Receber email com novidades" (newsletter)
- [ ] Componente de UI: painel/lista de notificações (sininho) no header
- [ ] Controller: marcar notificação como lida, listar notificações não lidas

### Seção de Dados
- [ ] Botão: "Exportar meus dados" (JSON/CSV com reviews, comentários, perfil)
- [ ] Botão: "Deletar minha conta" (com confirmação e aviso de irreversibilidade)

### Seeder de Dados Padrão
- [ ] Seeder para gerar pelo menos 30 reviews fakes
- [ ] Seeder para gerar pelo menos 10 comentários fakes em cada review
- [ ] Disponibilizar dados padrão ao iniciar o sistema para visualização

### Layout de Emails
- [ ] Melhorar layout e conteúdo dos emails transacionais (boas-vindas, redefinição de senha, verificação de e-mail e demais notificações)
- [ ] Substituir o layout padrão do Laravel por um template customizado com identidade visual do Pixel Rank

---

## Nível 3 - Complexo (v2.0) 📋

### Tema
- [ ] Seletor de tema (claro/escuro/sistema)
- [ ] Persistência de preferência de tema (cookie/sessão/banco)

### Contas Conectadas
- [ ] Integração com Steam
- [ ] Integração com PlayStation Network
- [ ] Integração com Xbox

---

## Notas de Implementação

- Notificações internas (Nível 2) requerem a tabela `notifications` do Laravel e um componente de UI para exibição (ex: dropdown no header com contador de não lidas).
- Exportação de dados (Nível 2) pode usar `League\Csv` ou coleções do Laravel para gerar JSON/CSV.
- Deleção de conta (Nível 2) deve ser feita com soft delete ou exclusão em cascata de reviews e comentários do usuário.
