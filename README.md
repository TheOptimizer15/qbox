# QBOX

Qbox is a professional and highly powerful multi-tenancy inventory management system with e-commerce features.

This software is built to help you monitor your activities even if you have multiple shops and workers. It leverages the power of Laravel to handle complex database schema actions properly and comes with the benefit of not monitoring an external website.

In simple words, you have access to an inventory management system and can directly create an online store for your clients to order online without ever needing a web developer to build a website. As it does this natively, you have the possibility to manage your products from the same system without the complexity of managing a website. It supports live chats and much more to keep your work inside the same system and everything related to it.

## Features Currently Implemented

* **User Authentication & Authorization**:
  * Secure Login, Registration, and Logout via Laravel Sanctum.
  * Role-based access control (Super Admin, Admin, Owner, Cashier) safeguarding internal routes.

* **Company Management**:
  * Capability to create, update, retrieve, and delete companies.
  * Strict access controls based on roles (e.g., only Super Admins can delete, Owners can manage).

* **Store Management**:
  * Full API resource endpoints (CRUD) for store entities.
  * Support for managing multiple stores securely.

* **Invitation System**:
  * Secure invitation workflow to onboard new workers to specific stores or companies.
  * Endpoints to allow invitees to accept or deny invitations.
  * Controls for owners to cancel pending unaccepted invitations.
