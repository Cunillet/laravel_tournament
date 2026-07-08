# Laravel Tournaments

Sistema de gestión de torneos y partidas con sistema de puntuación configurable.

## Stack Tecnológico

| Capa | Tecnología |
|------|-----------|
| **Backend** | PHP 8.4 + Laravel 13.X+ |
| **Frontend** | React 19.X + Inertia.js 3.X |
| **CSS** | TailwindCSS 4 + SCSS (mobile-first) |
| **BD** | MySQL 8.4 |
| **Cache/Sesión** | Redis 7.4 |
| **Build** | Vite 8 + rolldown |
| **Servidor** | Nginx + PHP-FPM (Alpine) |

## Requisitos

- [Docker](https://docs.docker.com/engine/install/) + [Docker Compose](https://docs.docker.com/compose/install/)
- Puertos libres: `8080`, `3306`, `6379`

## Inicio Rápido

```bash
# 1. Clonar el repositorio
git clone <repo-url>
cd laravel_tournaments

# 2. Configurar variables de entorno
cp src/.env.example src/.env
# Editar src/.env con los valores adecuados (ver sección .env)

# 3. Iniciar contenedores
docker compose up -d

# 4. Instalar dependencias PHP
docker compose run --rm composer install

# 5. Instalar dependencias frontend
docker compose exec php npm install

# 6. Generar APP_KEY
docker compose run --rm artisan key:generate

# 7. Ejecutar migraciones
docker compose run --rm artisan migrate

# 8. Compilar assets (en segundo plano)
docker compose exec php npm run dev
```

La aplicación estará disponible en [http://localhost:8080](http://localhost:8080).

## Arquitectura del Proyecto

```
src/
├── app/
│   ├── Http/
│   │   ├── Controllers/       # 8 controladores
│   │   ├── Middleware/        # Admin, Manager, HandleInertiaRequests
│   │   └── Requests/         # 12 FormRequest (validación)
│   ├── Models/               # 13 modelos Eloquent
│   └── Services/             # 3 servicios
├── database/
│   └── migrations/           # 16 migraciones
├── resources/
│   └── js/
│       ├── components/       # 16 componentes React reutilizables
│       │   ├── game/         #   GameHeader, RoundDefList, ScoringRuleList
│       │   ├── match/        #   MatchHeader, PlayerChips, PhaseSection, ScoreInput, ...
│       │   └── tournament/   #   TournamentHeader, StandingsTable, RoundList, ...
│       ├── pages/            # 15 páginas Inertia
│       │   ├── Auth/         #   Login, Register
│       │   ├── Games/        #   Index, Show, Create, Edit + Partials
│       │   ├── Matches/      #   Index, Show
│       │   ├── Profile/      #   Show, Edit
│       │   └── Tournaments/  #   Index, Show, Create
│       └── scss/             # Estilos SCSS (mobile-first)
└── routes/
    └── web.php               # Todas las rutas
```

## Base de Datos

### Modelo Entidad-Relación

```
Game ──┬── RoundDefinition ── ScoringRule
       │                          │
       │                     ScoringSystem
       │
       └── GameMatch ──┬── MatchPlayer ──┐
                        │                 │
                        ├── MatchRound    │
                        │       │         │
                        │  MatchScore ◄───┘
                        │
                   TournamentMatch
                        │
              TournamentRound
                        │
                   Tournament ── TournamentPlayer
```

### Tablas principales

| Tabla | Descripción |
|-------|-------------|
| `games` | Juegos (nombre, descripción, objetivos) |
| `round_definitions` | Definición de rondas de un juego |
| `scoring_systems` | Sistemas de puntuación (p.ej. "Puntos", "Tiempo") |
| `scoring_rules` | Reglas de puntuación por juego/ronda |
| `game_matches` | Partidas (estado: pending, completed) |
| `match_players` | Jugadores de una partida |
| `match_rounds` | Rondas de una partida |
| `match_scores` | Puntuaciones por jugador/ronda/regla |
| `tournaments` | Torneos |
| `tournament_rounds` | Rondas de torneo |
| `tournament_matches` | Asignación partida → ronda de torneo |

## Roles y Permisos

| Rol | Valor | Acceso |
|-----|-------|--------|
| **Admin** | `role = 0` | CRUD completo de juegos + gestión de torneos |
| **Manager** | `role = 1` | Creación y gestión de torneos |
| **User** | `role = 2` | Unirse a torneos, jugar partidas |

## Comandos Útiles

```bash
# Consola Artisan
docker compose run --rm artisan <comando>

# Migraciones
docker compose run --rm artisan migrate
docker compose run --rm artisan migrate:fresh --seed

# Tests
docker compose run --rm artisan test

# Compilar assets (desarrollo)
docker compose exec php npm run dev

# Compilar assets (producción)
docker compose exec php npm run build

# Instalar dependencias PHP
docker compose run --rm composer install
docker compose run --rm composer update

# Logs
docker compose logs -f php nginx mysql
```

## Estructura de Rutas

| Middleware | Rutas |
|-----------|-------|
| `guest` | Login, Register |
| `auth` | Logout, Perfil, Partidas, Torneos (ver) |
| `auth + admin` | CRUD Juegos, Rondas, Reglas de puntuación |
| `auth + manager` | Crear/Gestionar Torneos |

## Convenciones

- `declare(strict_types=1)` en todo PHP
- PSR-12 para formato de código
- Clases `final` por defecto
- Validación mediante **FormRequest** (no `$request->validate()`)
- **Eloquent ORM** en lugar de Query Builder
- Tests con **PHPUnit** (patrón Given-When-Then)
- **Mobile-first** para estilos CSS
- Frontend con **Inertia.js + React** (sin Blade salvo layout base)
