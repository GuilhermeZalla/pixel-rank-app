# Changelog

## [1.0.0] — 2026-10-20

### Adicionado
- Sistema de autenticação próprio (login, registro, logout, reset de senha, verificação de e-mail)
- Criação, edição, exclusão e listagem de reviews de jogos
- Sistema de comentários (criar, editar, excluir)
- Perfil e dashboard do usuário (dados, avatar, bio, senha, reviews e comentários próprios)
- Integração com IGDB API (busca de jogos, plataformas, capas, com cache)
- Filtros e ordenação de reviews (recomendação, nota, popularidade, data, spoilers)
- Sistema de idiomas: Português (BR) e Inglês (EUA), com seletor no cabeçalho
- Prós e contras nas reviews
- Enum de recomendação (Essential, Recommended, Mixed, Not Recommended)
- Policies de autorização (Review, Comment, User, Profile)
- Rate limiting em rotas de autenticação e busca
- Notificações por e-mail (verificação, reset de senha, boas-vindas)
- Testes automatizados (Pest)
- Páginas de erro personalizadas (404, 403, 419, 500, 503)

### Corrigido
- `ReviewController::show()`: retorna 404 para review inexistente em vez de erro 500
- `LoginController`: removido import indevido de `Dotenv\Exception\ValidationException`
- Dependências npm atualizadas (concurrently, nanoid, postcss, shell-quote, source-map-js)

### Documentação
- `.env.example`: adicionadas chaves TWITCH_* e GAME_API_*
- `README.md`: seções de Produção, Idiomas e instalação para Windows/Linux/Mac
- `CHANGELOG.md`: criado
- `docs/STATUS.md`: atualizado com auditoria técnica e progresso das fases