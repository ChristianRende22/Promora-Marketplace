# Motor de Códigos Promocionales — TDR-PROMO-001

Examen Final ASD · Curso: Patrones de Diseño · Stack: **PHP / Laravel**

## Estructura y patrones

| Carpeta | Contenido | Patrón |
| --- | --- | --- |
| `app/Contracts/` | `OrderableInterface`, `ValidationRuleInterface`, `DiscountStrategyInterface` | Contratos (DIP) |
| `app/Engine/` | `PromoCodeEngine` — orquestador de validación y cálculo | — |
| `app/Rules/Fixed/` | Existencia, Vigencia temporal, Estado activo (sección 2) | Chain of Responsibility |
| `app/Rules/Configurable/` | Las 7 reglas de sección 1.2 (min_purchase_amount, eligible_categories, first_order_only, user_usage_limit, global_usage_limit, global_amount_limit, restricted_usage) | Strategy / Specification |
| `app/Factories/` | `RuleFactory` — instancia reglas desde configuración en BD en runtime | Factory Method |
| `app/Discounts/` | `FixedDiscount`, `PercentDiscount`, `TieredDiscount` (sección 6, solo diseño + endpoint) | Strategy |
| `app/PostCalculation/` | `MaxDiscountAmount` (sección 1.3, diseño) | Decorator / Pipeline |
| `app/ValueObjects/` | `OrderContext` (inmutable), `ValidationResult` | Value Object |
| `app/Exceptions/` | `PromoValidationException` + códigos de error semánticos (sección 7) | — |
| `app/Http/` | Controller + FormRequest del endpoint de validación + cálculo | — |
| `database/factories/` | Factories de comprador, código promocional, historial de uso (sin fixtures estáticos) | — |
| `tests/Unit/Rules/` | 1 test por regla: caso bloqueado + caso permitido | TDD |
| `tests/Feature/` | Test del endpoint HTTP | TDD |
| `docs/` | Documento ASD y diagramas UML | — |

## Flujo (sección 5 del TDR)

Fase 1 — Verificación: reglas fijas en orden estricto → reglas configurables activas → válido o excepción con código semántico.

Fase 2 — Cálculo: estrategia por tipo (fixed | percent | tiered) → reglas post-cálculo → monto final.

## Equipo

- Christian Renderos — Arquitectura y patrones
- Alejandra Arriola — SOLID y contratos de integración
- Melisa Rivas — Engine + reglas fijas
- Gabriel Martínez — Reglas configurables + factories
- Alisson Quijano — Endpoint HTTP, cálculo y persistencia (diseño), trade-offs
