/**
 * main.js
 * Se carga en TODAS las páginas públicas. Se encarga de:
 *  - Leer el carrito guardado en localStorage y actualizar el badge del header.
 *  - Exponer funciones helper de carrito reutilizadas por tienda.js / carrito.js.
 */

const CART_KEY = "vet_cart";

/** Devuelve el carrito actual: [{ product: {...}, quantity: N }, ...] */
function getCart() {
  try {
    const raw = localStorage.getItem(CART_KEY);
    return raw ? JSON.parse(raw) : [];
  } catch (e) {
    return [];
  }
}

function saveCart(items) {
  localStorage.setItem(CART_KEY, JSON.stringify(items));
  updateCartBadge();
}

function addToCart(product) {
  const items = getCart();
  const existing = items.find((i) => i.product.id === product.id);
  if (existing) {
    existing.quantity += 1;
  } else {
    items.push({ product, quantity: 1 });
  }
  saveCart(items);
}

function removeFromCart(productId) {
  const items = getCart().filter((i) => i.product.id !== productId);
  saveCart(items);
}

function clearCart() {
  saveCart([]);
}

function cartTotal(items) {
  return items.reduce((acc, i) => acc + i.product.precio * i.quantity, 0);
}

function cartCount(items) {
  return items.reduce((acc, i) => acc + i.quantity, 0);
}

function updateCartBadge() {
  const items = getCart();
  const count = cartCount(items);
  const link = document.getElementById("cart-nav-link");
  const badge = document.getElementById("cart-count-badge");
  if (!link || !badge) return;
  if (count > 0) {
    link.style.display = "inline-flex";
    badge.textContent = count;
  } else {
    link.style.display = "none";
  }
}

document.addEventListener("DOMContentLoaded", updateCartBadge);
