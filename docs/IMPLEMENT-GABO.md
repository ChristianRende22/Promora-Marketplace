# Plan de Integración: Motor de Códigos Promocionales (Fase Dinámica)

## Propósito del Documento
Este documento establece la guía de integración para conectar la lógica de validación de la **Fase Dinámica** (reglas configurables y *factories* de dominio) con el orquestador principal, los contratos de entidades y la capa de infraestructura.

La base del dominio ya ha sido desarrollada utilizando TDD y cumple estrictamente con el principio de Responsabilidad Única y la Arquitectura Hexagonal.

---

## Estado Actual del Dominio (Completado)
Se ha finalizado la programación de los siguientes componentes en el dominio, los cuales **no deben ser modificados**:

1. **Contratos Base:** `RuleSpecificationInterface`, `OrderableInterface`, `PromoCodeRepositoryInterface`.
2. **Value Objects:** `OrderContext` (Inmutable).
3. **Excepciones:** `RuleValidationException` (maneja los códigos de error semánticos).
4. **Reglas Configurables (Patrón Specification):**
   - `MinPurchaseAmountRule`
   - `EligibleCategoriesRule`
   - `FirstOrderOnlyRule`
   - `UserUsageLimitRule`
   - `GlobalUsageLimitRule`
   - `GlobalAmountLimitRule`
   - `RestrictedUsageRule`
5. **Entorno TDD:** `OrderContextFactory`, `FakeOrder`, `FakePromoCodeRepository`.

---

## Instrucciones de Integración por Miembro del Equipo

### Para Christian Renderos (Engine + Reglas Fijas)
Tu responsabilidad es construir el `PromoCodeEngine` (el orquestador).
* **Integración con las reglas:** El motor no debe instanciar las reglas configurables. Debe recibir una colección de objetos que implementen `RuleSpecificationInterface`.
* **Ejecución (Chain of Responsibility):** Dentro del método principal de validación, debes iterar sobre estas reglas y ejecutar el método `isSatisfiedBy(OrderableInterface $order)`.
* **Manejo de Errores:** Captura la `RuleValidationException` si una regla falla para detener la validación.

### Para Alisson Quijano (Endpoint HTTP, Cálculo y Persistencia)
Tu trabajo conecta el dominio aislado con el framework Laravel (Infraestructura).
* **Persistencia (El Repositorio):** Crea `EloquentPromoCodeRepository` implementando el `PromoCodeRepositoryInterface` que ya está en el dominio. Aquí harás las consultas a MySQL para obtener los historiales.
* **Orquestación y Framework:** Debes crear el `ValidatePromoCodeUseCase` y registrar el `PromoCodeServiceProvider` en Laravel para inyectar las dependencias del motor.
* **Endpoint HTTP:** El `PromoCodeController` debe ejecutar el caso de uso y mapear las excepciones a las respuestas JSON correctas.

### Para Alejandra Arriola (SOLID y Contratos de Integración)
Tu labor asegura que el sistema exterior hable el mismo idioma que el motor.
* **Integración de Órdenes:** Cualquier modelo de Eloquent (ej. `Order`) que pase al motor debe implementar estrictamente el `OrderableInterface`.
* **Mapeo del Contexto:** Asegúrate de que el método `getOrderContext()` de tus modelos reales instancie correctamente el objeto inmutable `OrderContext`.



---

## Archivos Futuros a Crear o Modificar en el Repositorio

* `app/Domain/PromoCode/PromoCodeEngine.php` *(Christian)*
* `app/Application/PromoCode/UseCases/ValidatePromoCodeUseCase.php` *(Alisson)*
* `app/Infrastructure/Persistence/EloquentPromoCodeRepository.php` *(Alisson)*
* `app/Infrastructure/Http/Controllers/PromoCodeController.php` *(Alisson)*
* `app/Providers/PromoCodeServiceProvider.php` *(Alisson)*