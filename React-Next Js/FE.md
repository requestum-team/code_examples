# ReactJs, NextJ code-examples

We collect code examples to help you understand our coding culture and React-based approaches to writing code.

- [Eslint config](#tailwind-config)
- [Tailwind config](#tailwind-config)
- [RBAC - role-based access control](#rbac)
- [OpenSea SDK integration](#opensea-sdk-integration)

### [Eslint config](common/.eslintrc.js)

Common ESLint configuration used during the initial setup, including all the coding styles we follow during development.

### [Tailwind config](common/tailwind.config.ts)

Tailwind configuration includes theme creation for custom layouts, typography, animations, and more. Plugins were added,
and custom header classes were adjusted based on client designs.

### [RBAC](rbac)

Role-based access control samples Requestum team implemented on one of the projects, containing tsx templates for access
middlewares and types used within the app.

[AccessMiddleware](rbac/access.middleware.tsx)

[RBAC.types](rbac/RBAC.types.ts)

### [OpenSea SDK integration](opensea-sdk-integration)

Implement offer management functionality by integrating the [OpenSea sdk](https://opensea.io/) with the internal API
developed for the application, enabling the buying, selling, and creation of NFT tokens.

[use-opensea](open-sea/use-opensea.tsx)