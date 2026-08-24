# Maskavo Minha Conta

Plugin WordPress da **Comunidade Maskavo** para a área logada do aluno: dados pessoais, assinatura, certificados, avaliações e troca de senha.

Integra Tutor LMS, Elementor e o ecossistema de plugins Maskavo (matrícula Hotmart / roles).

## O que faz

- Perfil do aluno (avatar, nome, sobrenome, e-mail somente leitura)
- Resumo de assinatura / planos
- Certificados de cursos concluídos (Tutor Certificate)
- Avaliações deixadas pelo aluno
- Alteração de senha
- Interface responsiva (menu em coluna no mobile; sidebar no desktop)

## Requisitos

- WordPress
- [Elementor](https://elementor.com/)
- [Tutor LMS](https://tutorlms.com/) (certificados / progresso)
- Ambiente Maskavo com roles/planos já configurados

## Integração pública

| Item | Valor |
|---|---|
| Plugin | `maskavo-minha-conta` |
| Widget Elementor | **Maskavo Minha Conta** (categoria Maskavo) |
| Shortcode | `[maskavo_minha_conta]` |
| Página sugerida | `/minha-conta/` |

## Segurança (resumo)

- Ações AJAX exigem usuário logado + nonce
- Nunca aceita `user_id` vindo do request — só o usuário atual
- Dados enviados ao front passam por presenters (whitelist)
- Rate limit básico em endpoints sensíveis

## Autor

Elisson Rodrigues — uso no projeto Comunidade Maskavo (`comunidademaskavo.com.br`).

## Licença

Uso interno / proprietário do projeto Comunidade Maskavo, salvo acordo em contrário.
