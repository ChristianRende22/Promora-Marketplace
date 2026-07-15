# Documentación del Dominio: PromoCodeEngine (Fase Dinámica)

## 1. Introducción y Arquitectura
Este documento describe el diseño y la implementación de la **Fase Dinámica (Reglas Configurables)** del motor de códigos promocionales (`PromoCodeEngine`). 

El sistema ha sido diseñado bajo los principios de la **Arquitectura Hexagonal (Ports and Adapters)**. El núcleo del dominio (las reglas y contratos) está completamente aislado de la infraestructura. No existe acoplamiento con el framework (Laravel), el ORM (Eloquent), ni con la capa de transporte (Controladores HTTP). 

Esta separación permite que las reglas de negocio sean extremadamente fáciles de probar, flexibles y que no dependan de una implementación concreta de orden de compra o base de datos.

## 2. Contratos Base (Puertos) y Value Objects

### `OrderContext` (Value Object)
Es un objeto de valor **inmutable** que transporta la información contextual de la orden que se está evaluando. Contiene:
* `buyerProfile`: Identificador del perfil del comprador.
* `categoryId`: Identificador de la categoría del servicio/producto.
* `currentOrders`: Array con los IDs de las órdenes que se están procesando actualmente, necesario para ser excluido en los cálculos de límites (evitando falsos positivos).

### `OrderableInterface` (Puerto de Entrada)
El motor de promociones no depende de un modelo `Order` concreto, sino de este contrato. Cualquier entidad de la plataforma (ej. un Carrito de Compras o un Modelo de Producción) que implemente este contrato puede ser evaluada.
* Expone únicamente lo necesario: `getSubtotal()` y `getOrderContext()`.

### `PromoCodeRepositoryInterface` (Puerto de Salida)
Para las reglas que necesitan consultar datos históricos (como límites de uso o restricciones), se diseñó este puerto. Las implementaciones concretas (en la capa de infraestructura) utilizarán Eloquent, pero el dominio solo conoce el contrato.

### `RuleSpecificationInterface`
Basado en el patrón **Specification**. Cada regla del sistema implementa este contrato, que expone un único método: `isSatisfiedBy(OrderableInterface $order): bool`. Si la regla no se cumple, arroja una `RuleValidationException` con un código de error semántico.

## 3. Reglas Implementadas

Cada regla fue implementada como una clase con una única responsabilidad, evaluando una condición de negocio estricta:

| Regla | Descripción | Código de Error Semántico |
| :--- | :--- | :--- |
| **`MinPurchaseAmountRule`** | Valida que el subtotal de la orden supere o sea igual a un monto mínimo exigido por el cupón. | `min_amount_required` |
| **`EligibleCategoriesRule`** | Valida que la categoría de la orden (`categoryId`) se encuentre dentro de la lista de categorías permitidas por el cupón (soporta jerarquía enviando IDs planos desde el caso de uso). | `invalid_code` |
| **`FirstOrderOnlyRule`** | Consulta al repositorio para asegurar que el comprador no tenga órdenes previas completadas en la plataforma. | `code_already_used` |
| **`UserUsageLimitRule`** | Verifica en el repositorio que la cantidad de usos de este cupón por parte del comprador específico no exceda el límite establecido. | `usage_limit_reached` |
| **`GlobalUsageLimitRule`** | Verifica en el repositorio que la cantidad global de usos de este cupón entre todos los compradores no exceda el límite máximo de la campaña. | `usage_limit_reached` |
| **`GlobalAmountLimitRule`** | Asegura que el monto total de dinero descontado históricamente por este cupón no supere el presupuesto (límite en dólares). | `maximum_discount_reached` |
| **`RestrictedUsageRule`** | Verifica si el cupón está asignado explícitamente a un subconjunto de usuarios y valida que el comprador actual pertenezca a esa lista. | `restricted_usage` |

> [!NOTE]
> En todas las consultas al repositorio (`PromoCodeRepositoryInterface`), se envía la propiedad `currentOrders` del contexto para poder omitirlas de los conteos históricos, garantizando precisión si la orden está actualmente en borrador o proceso.

## 4. Estrategia de Testing (TDD)

El 100% de las reglas se implementaron siguiendo **Test-Driven Development (TDD)** y se probaron de forma unitaria sin requerir base de datos ni frameworks de testing pesados.

### Herramientas de Pruebas (Fakes y Factories)
Se evitó el uso de *fixtures* estáticos. En su lugar, el estado se genera de forma declarativa:
* **`OrderContextFactory`**: Un factory de dominio encadenable para construir contextos en las pruebas (`OrderContextFactory::new()->withCategoryId(3)->create()`).
* **`FakeOrder`**: Un *test double* que simula una orden real y permite inyectarle un subtotal y un contexto predefinidos.
* **`FakePromoCodeRepository`**: Una implementación en memoria del repositorio que permite inyectar el comportamiento de la base de datos (por ejemplo, definir cuántos usos lleva un usuario) sin tocar SQL ni Eloquent.

Cada regla posee su clase de pruebas (ej. `MinPurchaseAmountRuleTest`) con:
1. Al menos un test que demuestra el caso en el que la orden cumple con las condiciones.
2. Al menos un test que demuestra el caso de bloqueo, asegurando que se arroje la excepción con el **código de error semántico correcto**.

## 5. Cumplimiento de Principios SOLID

* **S (Single Responsibility Principle)**: Cada clase de regla (`MinPurchaseAmountRule`, `FirstOrderOnlyRule`, etc.) tiene una única razón para cambiar. No mezclan lógicas.
* **O (Open/Closed Principle)**: El sistema está abierto a la extensión y cerrado a la modificación. Para agregar una nueva regla de promoción, basta con crear una nueva clase que implemente `RuleSpecificationInterface`. Ninguna regla existente ni el motor principal tendrán que modificarse.
* **L (Liskov Substitution Principle)**: Gracias a `OrderableInterface`, el motor puede procesar un `FakeOrder` de prueba con la misma confiabilidad que procesará un modelo real de Laravel en producción.
* **I (Interface Segregation Principle)**: Las interfaces son pequeñas y específicas. `OrderableInterface` obliga a implementar solo dos métodos (`getSubtotal` y `getOrderContext`).
* **D (Dependency Inversion Principle)**: Las reglas que requieren estado histórico dependen de la abstracción `PromoCodeRepositoryInterface` y no de modelos Eloquent. Las implementaciones reales se inyectarán desde la capa externa (Infraestructura).
