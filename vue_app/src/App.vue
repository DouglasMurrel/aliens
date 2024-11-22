<template>
  <h1>Hello App!</h1>
  <p>
    <strong>Current route path:</strong> {{ $route.fullPath }}
  </p>
  <nav>
    <RouterLink to="/signup">Sign Up</RouterLink>
    <RouterLink to="/signin">Sign In</RouterLink>
  </nav>
  <main>
    <RouterView />
  </main>
</template>