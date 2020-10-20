**Caution:** this is still work in progress.

---

# Web application

A Nuxt.js frontend which is rendered from server-side and acts as an SPA (Single Page Application) once loaded.

**All commands have to be run in the `webapp` service (`make webapp`).**

## Hot Reloading

The `webapp` service run the command `yarn dev`. This command watch for file changes, recompile those files and 
automatically refresh your browser.

This command may also show ESLint errors and warnings; you can fix them using `yarn lint:fix`.
