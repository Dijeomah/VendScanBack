# UI/UX Specifications & Design System

## Table of Contents
1. [Design Principles](#design-principles)
2. [Color System](#color-system)
3. [Typography](#typography)
4. [Component Library](#component-library)
5. [Page Layouts](#page-layouts)
6. [Responsive Design](#responsive-design)
7. [Accessibility](#accessibility)

---

## Design Principles

### 1. **Mobile-First**
- Primary users (customers) view menus on mobile devices
- Design for mobile, scale up for desktop
- Touch-friendly targets (minimum 44x44px)

### 2. **Fast & Lightweight**
- Optimize for 3G networks
- Lazy load images
- Progressive enhancement
- Target: < 3 seconds initial load

### 3. **Scannable Content**
- Clear visual hierarchy
- Use of whitespace
- Scannable menu items
- Quick decision-making

### 4. **Delightful Interactions**
- Smooth transitions
- Meaningful animations
- Instant feedback
- Optimistic UI updates

---

## Color System

### Primary Palette

```css
/* Primary - Brand Color */
--primary-50:  #eff6ff;
--primary-100: #dbeafe;
--primary-200: #bfdbfe;
--primary-300: #93c5fd;
--primary-400: #60a5fa;
--primary-500: #3b82f6;  /* Main primary */
--primary-600: #2563eb;
--primary-700: #1d4ed8;
--primary-800: #1e40af;
--primary-900: #1e3a8a;

/* Success - For active items, confirmations */
--success-50:  #f0fdf4;
--success-500: #22c55e;
--success-700: #15803d;

/* Warning - For alerts, pending states */
--warning-50:  #fffbeb;
--warning-500: #f59e0b;
--warning-700: #b45309;

/* Error - For errors, out of stock */
--error-50:  #fef2f2;
--error-500: #ef4444;
--error-700: #b91c1c;

/* Neutral - UI elements */
--gray-50:  #f9fafb;
--gray-100: #f3f4f6;
--gray-200: #e5e7eb;
--gray-300: #d1d5db;
--gray-400: #9ca3af;
--gray-500: #6b7280;
--gray-600: #4b5563;
--gray-700: #374151;
--gray-800: #1f2937;
--gray-900: #111827;
```

### Semantic Colors

```css
/* Backgrounds */
--bg-primary: #ffffff;
--bg-secondary: #f9fafb;
--bg-tertiary: #f3f4f6;

/* Text */
--text-primary: #111827;
--text-secondary: #6b7280;
--text-tertiary: #9ca3af;
--text-inverse: #ffffff;

/* Borders */
--border-light: #e5e7eb;
--border-default: #d1d5db;
--border-dark: #9ca3af;
```

### Application-Specific Colors

| Use Case | Color | Variable |
|----------|-------|----------|
| Item Available | Green | `--success-500` |
| Item Unavailable | Red | `--error-500` |
| Category Header | Blue | `--primary-600` |
| Price | Dark Gray | `--gray-900` |
| Vendor Accent | Custom (vendor-specific) | `--vendor-accent` |

---

## Typography

### Font Stack

```css
/* Primary Font - Interface */
--font-sans: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;

/* Secondary Font - Headings (Optional elegant touch) */
--font-serif: 'Playfair Display', Georgia, serif;

/* Monospace - Prices, codes */
--font-mono: 'JetBrains Mono', 'Courier New', monospace;
```

### Type Scale

```css
/* Headings */
--text-xs:   0.75rem;   /* 12px */
--text-sm:   0.875rem;  /* 14px */
--text-base: 1rem;      /* 16px */
--text-lg:   1.125rem;  /* 18px */
--text-xl:   1.25rem;   /* 20px */
--text-2xl:  1.5rem;    /* 24px */
--text-3xl:  1.875rem;  /* 30px */
--text-4xl:  2.25rem;   /* 36px */
--text-5xl:  3rem;      /* 48px */

/* Font Weights */
--font-normal:    400;
--font-medium:    500;
--font-semibold:  600;
--font-bold:      700;

/* Line Heights */
--leading-tight:  1.25;
--leading-normal: 1.5;
--leading-relaxed: 1.75;
```

### Typography Usage

| Element | Size | Weight | Line Height | Usage |
|---------|------|--------|-------------|-------|
| Page Title | `--text-3xl` | `--font-bold` | `--leading-tight` | Dashboard headings |
| Section Heading | `--text-2xl` | `--font-semibold` | `--leading-tight` | Category names |
| Card Title | `--text-xl` | `--font-semibold` | `--leading-normal` | Item names |
| Body Text | `--text-base` | `--font-normal` | `--leading-normal` | Descriptions |
| Caption | `--text-sm` | `--font-normal` | `--leading-normal` | Metadata |
| Label | `--text-sm` | `--font-medium` | `--leading-normal` | Form labels |
| Price | `--text-lg` | `--font-semibold` | `--leading-tight` | Item prices |

---

## Component Library

### 1. Button Component

```vue
<!-- Button.vue -->
<template>
  <button
    :class="buttonClasses"
    :disabled="disabled || loading"
  >
    <Icon v-if="loading" name="spinner" class="animate-spin" />
    <Icon v-else-if="icon" :name="icon" />
    <slot />
  </button>
</template>

<style>
/* Variants */
.btn-primary {
  bg: var(--primary-500);
  color: white;
  hover: var(--primary-600);
}

.btn-secondary {
  bg: var(--gray-100);
  color: var(--gray-900);
  hover: var(--gray-200);
}

.btn-outline {
  bg: transparent;
  border: 1px solid var(--border-default);
  color: var(--text-primary);
}

/* Sizes */
.btn-sm { padding: 0.5rem 1rem; font-size: 0.875rem; }
.btn-md { padding: 0.75rem 1.5rem; font-size: 1rem; }
.btn-lg { padding: 1rem 2rem; font-size: 1.125rem; }
</style>
```

**Usage:**
```vue
<Button variant="primary" size="md" @click="save">Save Item</Button>
<Button variant="outline" size="sm" icon="plus">Add</Button>
```

### 2. Menu Item Card Component

```vue
<!-- MenuItemCard.vue -->
<template>
  <div class="menu-item-card">
    <!-- Image -->
    <div class="item-image">
      <NuxtImg
        :src="item.image || '/placeholder-food.jpg'"
        :alt="item.title"
        width="120"
        height="120"
        loading="lazy"
      />
      <span v-if="!item.status" class="unavailable-badge">
        Unavailable
      </span>
    </div>

    <!-- Content -->
    <div class="item-content">
      <h3 class="item-title">{{ item.title }}</h3>
      <p class="item-description">{{ truncate(item.description, 80) }}</p>

      <!-- Meta -->
      <div class="item-meta">
        <span v-if="item.category" class="category-tag">
          {{ item.category.category_name }}
        </span>
      </div>
    </div>

    <!-- Price -->
    <div class="item-price">
      ${{ formatPrice(item.price) }}
    </div>
  </div>
</template>

<style scoped>
.menu-item-card {
  display: grid;
  grid-template-columns: 120px 1fr auto;
  gap: 1rem;
  padding: 1rem;
  border: 1px solid var(--border-light);
  border-radius: 0.5rem;
  transition: all 0.2s;
}

.menu-item-card:hover {
  border-color: var(--primary-300);
  box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
  cursor: pointer;
}

.item-image {
  position: relative;
  border-radius: 0.5rem;
  overflow: hidden;
  aspect-ratio: 1;
}

.unavailable-badge {
  position: absolute;
  top: 0.5rem;
  right: 0.5rem;
  padding: 0.25rem 0.5rem;
  background: var(--error-500);
  color: white;
  font-size: 0.75rem;
  border-radius: 0.25rem;
}

.item-title {
  font-size: var(--text-lg);
  font-weight: var(--font-semibold);
  color: var(--text-primary);
  margin-bottom: 0.5rem;
}

.item-description {
  font-size: var(--text-sm);
  color: var(--text-secondary);
  line-height: var(--leading-normal);
}

.item-price {
  font-size: var(--text-xl);
  font-weight: var(--font-bold);
  color: var(--primary-600);
  align-self: center;
}

/* Mobile */
@media (max-width: 640px) {
  .menu-item-card {
    grid-template-columns: 80px 1fr;
  }

  .item-price {
    grid-column: 2;
    justify-self: end;
    margin-top: 0.5rem;
  }
}
</style>
```

### 3. Category Section Component

```vue
<!-- CategorySection.vue -->
<template>
  <section :id="slugify(category.category_name)" class="category-section">
    <!-- Category Header -->
    <div class="category-header">
      <h2 class="category-title">{{ category.category_name }}</h2>
      <span class="item-count">{{ category.items?.length || 0 }} items</span>
    </div>

    <!-- Items Grid -->
    <div class="items-grid">
      <MenuItemCard
        v-for="item in category.items"
        :key="item.id"
        :item="item"
        @click="$emit('select-item', item)"
      />
    </div>
  </section>
</template>

<style scoped>
.category-section {
  margin-bottom: 3rem;
}

.category-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 1.5rem;
  padding-bottom: 0.75rem;
  border-bottom: 2px solid var(--primary-500);
}

.category-title {
  font-size: var(--text-2xl);
  font-weight: var(--font-bold);
  color: var(--primary-700);
}

.item-count {
  font-size: var(--text-sm);
  color: var(--text-secondary);
}

.items-grid {
  display: grid;
  gap: 1rem;
}
</style>
```

### 4. Modal Component

```vue
<!-- Modal.vue -->
<template>
  <Teleport to="body">
    <Transition name="modal">
      <div v-if="modelValue" class="modal-overlay" @click.self="close">
        <div class="modal-container">
          <!-- Header -->
          <div class="modal-header">
            <h3 class="modal-title">
              <slot name="title">Modal Title</slot>
            </h3>
            <button class="modal-close" @click="close">
              <Icon name="x" />
            </button>
          </div>

          <!-- Body -->
          <div class="modal-body">
            <slot />
          </div>

          <!-- Footer -->
          <div v-if="$slots.footer" class="modal-footer">
            <slot name="footer" />
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<style scoped>
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
  padding: 1rem;
}

.modal-container {
  background: white;
  border-radius: 0.75rem;
  max-width: 600px;
  width: 100%;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1);
}

.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1.5rem;
  border-bottom: 1px solid var(--border-light);
}

.modal-body {
  padding: 1.5rem;
}

.modal-footer {
  padding: 1rem 1.5rem;
  border-top: 1px solid var(--border-light);
  display: flex;
  gap: 0.75rem;
  justify-content: flex-end;
}

/* Transitions */
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-container {
  transition: transform 0.3s;
}

.modal-enter-from .modal-container,
.modal-leave-to .modal-container {
  transform: scale(0.95);
}
</style>
```

### 5. Input Component

```vue
<!-- Input.vue -->
<template>
  <div class="input-group">
    <label v-if="label" :for="id" class="input-label">
      {{ label }}
      <span v-if="required" class="text-error-500">*</span>
    </label>

    <div class="input-wrapper">
      <Icon v-if="icon" :name="icon" class="input-icon" />

      <input
        :id="id"
        v-model="modelValue"
        :type="type"
        :placeholder="placeholder"
        :disabled="disabled"
        :class="inputClasses"
        v-bind="$attrs"
      />

      <Icon v-if="error" name="alert-circle" class="input-icon-error" />
    </div>

    <p v-if="error" class="input-error">{{ error }}</p>
    <p v-else-if="hint" class="input-hint">{{ hint }}</p>
  </div>
</template>

<style scoped>
.input-group {
  margin-bottom: 1.5rem;
}

.input-label {
  display: block;
  font-size: var(--text-sm);
  font-weight: var(--font-medium);
  color: var(--text-primary);
  margin-bottom: 0.5rem;
}

.input-wrapper {
  position: relative;
}

.input {
  width: 100%;
  padding: 0.75rem 1rem;
  font-size: var(--text-base);
  border: 1px solid var(--border-default);
  border-radius: 0.5rem;
  transition: all 0.2s;
}

.input:focus {
  outline: none;
  border-color: var(--primary-500);
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.input:disabled {
  background: var(--gray-100);
  cursor: not-allowed;
}

.input.has-error {
  border-color: var(--error-500);
}

.input-icon {
  position: absolute;
  left: 1rem;
  top: 50%;
  transform: translateY(-50%);
  color: var(--text-secondary);
}

.input.has-icon {
  padding-left: 2.75rem;
}

.input-error {
  margin-top: 0.5rem;
  font-size: var(--text-sm);
  color: var(--error-500);
}

.input-hint {
  margin-top: 0.5rem;
  font-size: var(--text-sm);
  color: var(--text-secondary);
}
</style>
```

---

## Page Layouts

### 1. Public Menu Layout

```vue
<!-- layouts/public-menu.vue -->
<template>
  <div class="public-menu-layout">
    <!-- Vendor Header -->
    <header class="vendor-header">
      <div class="vendor-hero">
        <NuxtImg :src="vendor.hero" class="hero-image" />
      </div>

      <div class="vendor-info">
        <NuxtImg :src="vendor.logo" class="vendor-logo" />
        <div class="vendor-details">
          <h1 class="vendor-name">{{ vendor.business_name }}</h1>
          <p class="vendor-meta">
            ⭐ 4.5 · $$ · {{ vendor.cuisine_type }}
          </p>
        </div>
      </div>
    </header>

    <!-- Search Bar (Sticky) -->
    <div class="search-bar sticky">
      <Input
        v-model="searchQuery"
        placeholder="Search menu..."
        icon="search"
      />
    </div>

    <!-- Category Navigation (Sticky) -->
    <nav class="category-nav sticky">
      <button
        v-for="category in categories"
        :key="category.id"
        :class="{ active: activeCategory === category.id }"
        @click="scrollToCategory(category.id)"
      >
        {{ category.category_name }}
      </button>
    </nav>

    <!-- Menu Content -->
    <main class="menu-content">
      <slot />
    </main>

    <!-- Footer -->
    <footer class="menu-footer">
      <p>Powered by QR Menu Manager</p>
      <div class="social-links">
        <!-- Social icons -->
      </div>
    </footer>
  </div>
</template>

<style scoped>
.public-menu-layout {
  min-height: 100vh;
  background: var(--bg-secondary);
}

.vendor-header {
  background: white;
  margin-bottom: 1rem;
}

.vendor-hero {
  height: 200px;
  overflow: hidden;
}

.hero-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.vendor-info {
  padding: 1rem;
  display: flex;
  gap: 1rem;
  align-items: center;
  margin-top: -3rem;
  position: relative;
}

.vendor-logo {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  border: 4px solid white;
  box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
}

.vendor-name {
  font-size: var(--text-2xl);
  font-weight: var(--font-bold);
  margin-bottom: 0.25rem;
}

.search-bar {
  padding: 1rem;
  background: white;
  border-bottom: 1px solid var(--border-light);
}

.category-nav {
  display: flex;
  gap: 0.5rem;
  padding: 1rem;
  background: white;
  overflow-x: auto;
  border-bottom: 1px solid var(--border-light);
}

.category-nav button {
  padding: 0.5rem 1rem;
  border-radius: 2rem;
  white-space: nowrap;
  background: var(--gray-100);
  transition: all 0.2s;
}

.category-nav button.active {
  background: var(--primary-500);
  color: white;
}

.sticky {
  position: sticky;
  top: 0;
  z-index: 100;
}

.menu-content {
  padding: 1rem;
  max-width: 1200px;
  margin: 0 auto;
}

.menu-footer {
  text-align: center;
  padding: 2rem 1rem;
  background: white;
  margin-top: 2rem;
  border-top: 1px solid var(--border-light);
}
</style>
```

### 2. Dashboard Layout (Vendor/Admin)

```vue
<!-- layouts/dashboard.vue -->
<template>
  <div class="dashboard-layout">
    <!-- Sidebar -->
    <aside class="sidebar" :class="{ collapsed: sidebarCollapsed }">
      <div class="sidebar-header">
        <img src="/logo.svg" alt="Logo" class="logo" />
        <h2 v-if="!sidebarCollapsed">QR Menu</h2>
      </div>

      <nav class="sidebar-nav">
        <NuxtLink
          v-for="item in navItems"
          :key="item.path"
          :to="item.path"
          class="nav-item"
        >
          <Icon :name="item.icon" />
          <span v-if="!sidebarCollapsed">{{ item.label }}</span>
        </NuxtLink>
      </nav>

      <button class="sidebar-toggle" @click="toggleSidebar">
        <Icon :name="sidebarCollapsed ? 'chevron-right' : 'chevron-left'" />
      </button>
    </aside>

    <!-- Main Content -->
    <div class="main-content">
      <!-- Top Bar -->
      <header class="top-bar">
        <div class="breadcrumbs">
          <!-- Breadcrumb navigation -->
        </div>

        <div class="top-bar-actions">
          <!-- Notifications -->
          <button class="icon-button">
            <Icon name="bell" />
            <span class="badge">3</span>
          </button>

          <!-- User Menu -->
          <UserMenu />
        </div>
      </header>

      <!-- Page Content -->
      <main class="page-content">
        <slot />
      </main>
    </div>
  </div>
</template>

<style scoped>
.dashboard-layout {
  display: grid;
  grid-template-columns: 250px 1fr;
  min-height: 100vh;
}

.sidebar {
  background: var(--gray-900);
  color: white;
  display: flex;
  flex-direction: column;
  transition: width 0.3s;
}

.sidebar.collapsed {
  width: 80px;
}

.sidebar-header {
  padding: 1.5rem;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  display: flex;
  align-items: center;
  gap: 1rem;
}

.sidebar-nav {
  flex: 1;
  padding: 1rem 0;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem 1.5rem;
  color: rgba(255, 255, 255, 0.7);
  transition: all 0.2s;
}

.nav-item:hover,
.nav-item.router-link-active {
  background: rgba(255, 255, 255, 0.1);
  color: white;
}

.main-content {
  display: flex;
  flex-direction: column;
  background: var(--bg-secondary);
}

.top-bar {
  background: white;
  padding: 1rem 2rem;
  border-bottom: 1px solid var(--border-light);
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.page-content {
  flex: 1;
  padding: 2rem;
}

/* Mobile */
@media (max-width: 768px) {
  .dashboard-layout {
    grid-template-columns: 1fr;
  }

  .sidebar {
    position: fixed;
    left: 0;
    top: 0;
    bottom: 0;
    z-index: 1000;
    transform: translateX(-100%);
  }

  .sidebar.open {
    transform: translateX(0);
  }
}
</style>
```

---

## Responsive Design

### Breakpoints

```css
/* Tailwind-style breakpoints */
--breakpoint-sm: 640px;   /* Mobile landscape, small tablets */
--breakpoint-md: 768px;   /* Tablets */
--breakpoint-lg: 1024px;  /* Desktops */
--breakpoint-xl: 1280px;  /* Large desktops */
--breakpoint-2xl: 1536px; /* Extra large desktops */
```

### Mobile-First Approach

```css
/* Base styles (mobile) */
.menu-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 1rem;
}

/* Tablet */
@media (min-width: 768px) {
  .menu-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 1.5rem;
  }
}

/* Desktop */
@media (min-width: 1024px) {
  .menu-grid {
    grid-template-columns: repeat(3, 1fr);
    gap: 2rem;
  }
}
```

---

## Accessibility (A11y)

### WCAG 2.1 AA Compliance

1. **Color Contrast**
   - Minimum 4.5:1 for normal text
   - Minimum 3:1 for large text
   - Use tools like WebAIM Contrast Checker

2. **Keyboard Navigation**
   ```vue
   <button
     @click="handleClick"
     @keydown.enter="handleClick"
     @keydown.space="handleClick"
     tabindex="0"
   >
     Action
   </button>
   ```

3. **ARIA Labels**
   ```vue
   <button aria-label="Close modal" @click="close">
     <Icon name="x" aria-hidden="true" />
   </button>
   ```

4. **Focus Indicators**
   ```css
   .btn:focus-visible {
     outline: 2px solid var(--primary-500);
     outline-offset: 2px;
   }
   ```

5. **Screen Reader Support**
   ```vue
   <span class="sr-only">Loading...</span>
   <div aria-live="polite" aria-atomic="true">
     {{ statusMessage }}
   </div>
   ```

---

## Animation Guidelines

### Timing Functions

```css
--ease-in-out: cubic-bezier(0.4, 0, 0.2, 1);
--ease-out: cubic-bezier(0, 0, 0.2, 1);
--ease-in: cubic-bezier(0.4, 0, 1, 1);

/* Durations */
--duration-75:  75ms;
--duration-100: 100ms;
--duration-150: 150ms;
--duration-200: 200ms;
--duration-300: 300ms;
--duration-500: 500ms;
```

### Common Animations

```css
/* Fade In */
@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

/* Slide Up */
@keyframes slideUp {
  from {
    transform: translateY(10px);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

/* Pulse (for loading) */
@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.5; }
}
```

---

This design system provides the foundation for building consistent, accessible, and beautiful interfaces. Next document will cover the implementation architecture.
