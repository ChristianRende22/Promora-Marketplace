# Documentación del Dominio: PromoCodeEngine (Fase Dinámica)

## 1. Introducción y Arquitectura
Este documento describe el diseño y la implementación de la **Fase Dinámica (Reglas Configurables)** del motor de códigos promocionales (`PromoCodeEngine`). 

El sistema ha sido diseñado bajo los principios de la **Arquitectura Hexagonal (Ports and Adapters)** y sigue una estructura de directorios plana en la raíz de `app/` para integrarse nativamente con Laravel sin sacrificar el aislamiento. El núcleo del dominio (las reglas y contratos) está completamente aislado de la infraestructura. No existe acoplamiento con el framework (Laravel), el ORM (Eloquent), ni con la capa de transporte (Controladores HTTP). 

Esta separación permite que las reglas de negocio sean extremadamente fáciles de probar, flexibles y que no dependan de una implementación concreta de base de datos.

## 2. Contratos Base (Puertos) y Value Objects

### `OrderContext` (Value Object)
Es un objeto de valor **inmutable** cuya única responsabilidad (SRP) es portar la información contextual requerida por las reglas:
* `buyerProfile`: Instancia de `BuyerProfileInterface`.
* `category`: Instancia de `CategoryInterface`.
* `currentOrders`: Instancia de `OrderCollectionInterface` con las órdenes en proceso actual (para evitar falsos positivos en límites históricos).

### `Interfaces de Dominio` (Puertos de Entrada)
* **`OrderableInterface`**: Expone `getSubtotal()` y `getOrderContext()`. Cualquier entidad que lo implemente (ej. Carrito o Checkout) puede ser evaluada.
* **`BuyerProfileInterface`**: Provee identidad (`getId()`) y estatus (`isFirstOrder()`) del comprador.
* **`CategoryInterface`**: Abstrae jerarquías y permite evaluar parentesco nativo (`isDescendantOfOrEquals()`) sin usar ORMs relacionales.
* **`OrderCollectionInterface`**: Colección inmutable de órdenes del dominio (aislada de `Illuminate\Support\Collection`).

### `PromoCodeRepositoryInterface` (Puerto de Salida)
Contrato para consultar históricos transaccionales (límites de uso, restricciones, montos descontados) sin depender de Eloquent en las reglas.

### `RuleSpecificationInterface`
Basado en el patrón **Specification**. Cada regla implementa este contrato, que expone el método `isSatisfiedBy(OrderableInterface $order): bool`. Si falla, arroja una `RuleValidationException` con un código de error semántico.

## 3. Reglas Implementadas (en `app/Rules/Configurable/`)

| Regla | Descripción | Código de Error Semántico |
| :--- | :--- | :--- |
| **`MinPurchaseAmountRule`** | Valida que el subtotal de la orden iguale o supere el monto mínimo exigido. | `min_amount_required` |
| **`EligibleCategoriesRule`** | Valida que la categoría cumpla con las permitidas utilizando abstracción nativa de jerarquías (`CategoryInterface`). | `invalid_code` |
| **`FirstOrderOnlyRule`** | Valida de forma local mediante `$context->buyerProfile->isFirstOrder()` que sea la primera compra. | `code_already_used` |
| **`UserUsageLimitRule`** | Verifica en el repositorio el límite de usos consumidos por el usuario, excluyendo la orden actual. | `usage_limit_reached` |
| **`GlobalUsageLimitRule`** | Verifica en el repositorio la cantidad global de usos, excluyendo las órdenes actuales. | `usage_limit_reached` |
| **`GlobalAmountLimitRule`** | Asegura que el monto total descontado históricamente no supere el presupuesto. | `maximum_discount_reached` |
| **`RestrictedUsageRule`** | Valida mediante el repositorio que el `$buyerProfile->getId()` pertenezca a la lista de usuarios permitidos. | `restricted_usage` |

## 4. Estrategia de Testing (TDD)

El 100% de las reglas se implementaron siguiendo **Test-Driven Development (TDD)** mediante `PHPUnit`.

### Herramientas de Pruebas (Fakes y Factories)
Se generaron en `tests/Fakes` y `tests/Factories`:
* **`OrderContextFactory`**: Un factory de dominio encadenable para construir contextos predefinidos (`OrderContextFactory::new()->withIsFirstOrder(true)->create()`).
* **`FakeOrder` & `FakeCategory`**: *Test doubles* para simular una orden o categoría e inyectar un estado controlado.
* **`FakePromoCodeRepository`**: Una implementación en memoria que permite definir usos y listas blancas sin tocar SQL.

Cada regla posee su clase de pruebas (ej. `MinPurchaseAmountRuleTest`) con tests de éxito y bloqueo que validan específicamente la emisión del código de error semántico esperado.

## 5. Cumplimiento de Principios SOLID

* **S (Single Responsibility)**: Cada regla evalúa una única condición.
* **O (Open/Closed)**: Añadir nuevas promociones solo implica crear una nueva clase que implemente `RuleSpecificationInterface`.
* **L (Liskov Substitution)**: Las reglas procesan `FakeOrder` o `FakeCategory` con la misma solidez que instancias reales de Laravel en producción.
* **I (Interface Segregation)**: Interfaces especializadas sin métodos sobrantes.
* **D (Dependency Inversion)**: Las validaciones históricas dependen de abstracciones (`PromoCodeRepositoryInterface`) cuyas implementaciones reales se inyectan desde fuera.
