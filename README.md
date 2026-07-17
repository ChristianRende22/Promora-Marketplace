# Motor de Códigos Promocionales — TDR-PROMO-001

Examen Final ASD · Curso: Patrones de Diseño · Stack: **PHP 8.4 + PHPUnit** (dominio, sin framework) — Laravel 13 se integra únicamente en la capa de Infraestructura (aún no implementada).

## Estructura y patrones

| Carpeta | Contenido | Patrón / Estado |
| --- | --- | --- |
| `app/Contracts/` | `OrderableInterface`, `RuleSpecificationInterface`, `FixedValidationRuleInterface`, `PromoCodeRepositoryInterface`, `CategoryInterface`, `OrderCollectionInterface`, `BuyerProfileInterface` | Contratos (DIP) — ✅ implementado |
| `app/Entities/` | `PromoCode` (id, code, type, value, status, startsAt, endsAt) | Entidad de dominio — ✅ implementado |
| `app/Enums/` | `PromoCodeStatus` (draft, active, paused, expired) | — ✅ implementado |
| `app/Engine/` | `PromoCodeEngine` (evalúa las reglas configurables), `FixedRuleChain` (secuencia las reglas fijas) — son dos colaboradores independientes, invocados por separado desde el futuro `ValidatePromoCodeUseCase` | Chain of Responsibility (`FixedRuleChain`) — ✅ implementado |
| `app/Rules/Fixed/` | `CodeExistsRule`, `CodeWithinValidityPeriodRule`, `CodeIsActiveRule` (sección 2 del TDR) | Chain of Responsibility — ✅ implementado |
| `app/Rules/Configurable/` | Las 7 reglas de la sección 1.2 (`MinPurchaseAmountRule`, `EligibleCategoriesRule`, `FirstOrderOnlyRule`, `UserUsageLimitRule`, `GlobalUsageLimitRule`, `GlobalAmountLimitRule`, `RestrictedUsageRule`) | Specification — ✅ implementado |
| `app/Exceptions/` | `RuleValidationException` (código de error semántico + mensaje) | — ✅ implementado |
| `app/Factories/` | Factory Method que construye reglas configurables desde `promo_code_rules` (BD) en runtime | Factory Method — ⏳ pendiente |
| `app/Discounts/` | `FixedDiscount`, `PercentDiscount`, `TieredDiscount` (sección 6) | Strategy — ⏳ pendiente (diseño) |
| `app/PostCalculation/` | `max_discount_amount` (sección 1.3) | ⏳ pendiente (diseño) |
| `app/Http/` | Controller + FormRequest del endpoint de validación + cálculo | ⏳ pendiente |
| `database/factories/` | Factories de comprador, código promocional, historial de uso (sin fixtures estáticos) | ⏳ pendiente |
| `app/ValueObjects/` | `OrderContext` (inmutable), `BuyerProfile`, `OrderCollection` | — ✅ implementado (no cuenta como patrón GoF, ver nota) |
| `tests/Unit/Rules/Fixed/`, `tests/Unit/Rules/Configurable/`, `tests/Unit/Engine/` | 1 test por regla (caso bloqueado + permitido) + tests del motor | TDD — ✅ 28/28 en verde |
| `tests/Factories/`, `tests/Fakes/` | `PromoCodeFactory`, `OrderContextFactory`, `FakeOrder`, `FakeCategory`, `FakePromoCodeRepository` | Test doubles de dominio |
| `tests/Feature/` | Test del endpoint HTTP | ⏳ pendiente |
| `docs/` | Documento ASD y diagramas (`docs/diagrams/modelo_datos.mermaid`) | — |

> **Nota sobre Value Object:** `OrderContext`, `BuyerProfile` y `OrderCollection` son Value Objects (inmutables), pero **no se cuentan como patrón seleccionado** en la tabla ni en el ASD — Value Object es un patrón DDD/Fowler, no GoF, y el TDR exige justificar únicamente patrones GoF. Los patrones GoF seleccionados son: Specification (reglas configurables), Factory Method (`app/Factories/`, pendiente), Strategy (`app/Discounts/`, pendiente) y Chain of Responsibility (`FixedRuleChain`).

## Flujo (sección 5 del TDR)

**Fase 1 — Verificación (implementada):** siguiendo la "Colaboración entre patrones" del ASD, el caso de uso orquesta dos pasos secuenciales, no un solo Engine monolítico:

1. `FixedRuleChain::run(?PromoCode $promoCode)` corre las 3 reglas fijas en orden estricto (existencia, vigencia, estado activo), cortando en la primera que falla.
2. Solo si las 3 pasan, `PromoCodeRuleFactory` (Persona D, pendiente) construye las Specifications activas del código desde la BD, y `PromoCodeEngine::validate(OrderableInterface $order)` las evalúa como colección independiente.

Cualquier fallo, en cualquiera de los dos pasos, lanza `RuleValidationException` con el código de error semántico correspondiente (`invalid_code`, `expired_coupon`, `usage_limit_reached`, `maximum_discount_reached`, `min_amount_required`, `code_already_used`, `restricted_usage`).

**Fase 2 — Cálculo (pendiente):** estrategia de descuento por tipo (`fixed | percent | tiered`) → reglas post-cálculo (`max_discount_amount`) → monto final.

## Equipo

- Christian Renderos — Arquitectura y patrones (documento ASD) **y** núcleo del motor + reglas fijas (`PromoCodeEngine`, `FixedRuleChain`, reglas fijas)
- Alejandra Arriola — SOLID y contratos de integración
- Gabriel Martínez — Reglas configurables + factories
- Alisson Quijano — Endpoint HTTP, cálculo de descuento y persistencia (Eloquent), trade-offs

## Estado actual

Dominio de validación completo y testeado (27/27 tests en verde: `vendor/bin/phpunit`), alineado con el flujo descrito en el ASD (`FixedRuleChain` y `PromoCodeEngine` son colaboradores independientes, no uno anidado en el otro). Pendiente: Factory Method de reglas configurables (Persona D), estrategias de descuento y post-cálculo, capa de infraestructura Laravel (repositorio Eloquent, controller, service provider, `ValidatePromoCodeUseCase` — Persona E).
