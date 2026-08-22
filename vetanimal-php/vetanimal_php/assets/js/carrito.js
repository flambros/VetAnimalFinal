document.addEventListener("DOMContentLoaded", () => {
  const DEFAULT_IMG = "https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?w=200&q=80";

  function render() {
    const items = getCart();
    if (items.length === 0) {
      document.getElementById("empty-cart").style.display = "block";
      document.getElementById("cart-box").style.display = "none";
      return;
    }
    document.getElementById("empty-cart").style.display = "none";
    document.getElementById("cart-box").style.display = "block";

    document.getElementById("cart-items").innerHTML = items.map((item) => `
      <div class="u-flex u-items-center u-justify-between u-gap-4" style="padding:16px 0;">
        <div class="u-flex u-items-center u-gap-4">
          <img src="${item.product.imagen || DEFAULT_IMG}" alt="${item.product.nombre}" class="u-avatar" style="width:64px;height:64px;border-radius:12px;object-fit:cover;">
          <div>
            <h3 class="u-font-semibold u-text-base">${item.product.nombre}</h3>
            <p class="u-text-xs u-muted">Cantidad: ${item.quantity} × $${Number(item.product.precio).toFixed(2)}</p>
          </div>
        </div>
        <div class="u-flex u-items-center u-gap-4">
          <span class="u-font-bold u-text-base">$${(item.product.precio * item.quantity).toFixed(2)}</span>
          <button type="button" class="u-link u-text-red-dark u-text-sm u-font-semibold" data-remove="${item.product.id}">Eliminar</button>
        </div>
      </div>
    `).join("");

    document.getElementById("cart-total").textContent = `$${cartTotal(items).toFixed(2)}`;

    document.querySelectorAll("[data-remove]").forEach((btn) => {
      btn.addEventListener("click", () => {
        removeFromCart(Number(btn.dataset.remove));
        render();
      });
    });
  }

  document.getElementById("btn-clear-cart").addEventListener("click", () => {
    clearCart();
    render();
  });

  document.getElementById("btn-checkout").addEventListener("click", async () => {
    if (!window.IS_LOGGED_IN) {
      window.location.href = "login.php";
      return;
    }
    const items = getCart();
    if (items.length === 0) return;

    const errorBox = document.getElementById("checkout-error");
    errorBox.style.display = "none";
    const btn = document.getElementById("btn-checkout");
    btn.disabled = true;
    btn.textContent = "Procesando...";

    try {
      const res = await fetch("api/orders.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          usuario_id: window.CURRENT_USER_ID,
          items: items.map((i) => ({ productId: i.product.id, quantity: i.quantity })),
        }),
      });
      const data = await res.json();
      btn.disabled = false;
      btn.textContent = "Finalizar Compra ✓";

      if (!res.ok) {
        errorBox.textContent = data.error || "Error al procesar la compra";
        errorBox.style.display = "block";
        return;
      }

      clearCart();
      document.getElementById("view-cart").style.display = "none";
      document.getElementById("view-success").style.display = "block";
    } catch (e) {
      btn.disabled = false;
      btn.textContent = "Finalizar Compra ✓";
      errorBox.textContent = "Error de red al procesar el pago";
      errorBox.style.display = "block";
    }
  });

  render();
});
