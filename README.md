# 💳 Flow Subscription for WooCommerce

Plugin para **WordPress + WooCommerce** que permite crear **planes y suscripciones** recurrentes integradas con la pasarela de pagos [Flow.cl](https://www.flow.cl).

Con este plugin podrás ofrecer planes mensuales, trimestrales o anuales directamente desde tu tienda WooCommerce, gestionando pagos automáticos, cancelaciones y renovaciones de forma segura.

---

## 🚀 Características principales

- 🧩 **Integración directa con Flow.cl**
  - Compatible con el API oficial de Flow.
  - Soporte para suscripciones, pagos únicos y recurrentes.

- 💼 **Creación de planes desde WooCommerce**
  - Define nombre, descripción, monto, periodo y recurrencia.
  - Asocia productos WooCommerce a un plan Flow automáticamente.

- 🔄 **Gestión de suscripciones**
  - Alta, baja y renovación automática de suscripciones.
  - Consulta del estado de pago y sincronización con Flow.
  - Webhooks automáticos para confirmar o cancelar pagos.

- 📬 **Notificaciones automáticas**
  - Correos automáticos al cliente ante renovación o cancelación.
  - Notificación al administrador ante nuevos suscriptores.

- 🧠 **Panel de control completo**
  - Visualiza suscripciones activas, pendientes o canceladas.
  - Acceso rápido al panel Flow del cliente y al detalle del plan.

- 🔐 **Seguridad**
  - Uso de claves API privadas/secretas.
  - Validación por firma digital SHA256 (según API Flow).

---

## ⚙️ Requisitos

- **WordPress** 6.0 o superior  
- **WooCommerce** 7.0 o superior  
- Cuenta activa en [Flow.cl](https://www.flow.cl) con acceso a API  
- PHP 8.0+  
- Extensiones `curl` y `openssl` habilitadas  

---

## 🧩 Instalación

1. Descarga el plugin o clona el repositorio:
   ```bash
   git clone https://github.com/tuusuario/flow-subscription-woocommerce.git
