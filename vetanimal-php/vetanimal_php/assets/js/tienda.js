document.addEventListener("DOMContentLoaded", () => {
  const DEFAULT_IMG = "https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?w=300&q=80";
  const categories = ["Todos", "Medicamentos", "Bienestar y Estética", "Nutrición y Alimento"];
  let products = [];
  let search = "";
  let activeCategory = "Todos";

  function renderCategoryBar() {
    document.getElementById("category-bar").innerHTML = categories.map((cat) => `
      <button type="button" class="chip-filter ${activeCategory === cat ? "active" : ""}" data-cat="${cat}">${cat}</button>
    `).join("");
    document.querySelectorAll("[data-cat]").forEach((btn) => {
      btn.addEventListener("click", () => {
        activeCategory = btn.dataset.cat;
        renderCategoryBar();
        renderProducts();
      });
    });
  }

  function renderProducts() {
    const filtered = products.filter((p) => {
      const matchCat = activeCategory === "Todos" || p.categoria === activeCategory;
      const s = search.toLowerCase();
      const matchSearch = p.nombre.toLowerCase().includes(s) || (p.descripcion || "").toLowerCase().includes(s);
      return matchCat && matchSearch;
    });

    document.getElementById("product-grid").innerHTML = filtered.map((prod) => `
      <div class="product-card">
        <div class="product-thumb">
          ${prod.etiqueta ? `<span class="tag">${prod.etiqueta}</span>` : ""}
          <img src="${prod.imagen || DEFAULT_IMG}" alt="${prod.nombre}" style="max-height:144px; object-fit:contain;">
        </div>
        <div class="product-body">
          <h3>${prod.nombre}</h3>
          <p>${prod.descripcion || ""}</p>
          <div class="product-foot">
            <span class="price">$${Number(prod.precio).toFixed(2)}</span>
            <button type="button" class="cart-btn" title="Agregar al carrito" data-add="${prod.id}">+</button>
          </div>
        </div>
      </div>
    `).join("");

    document.querySelectorAll("[data-add]").forEach((btn) => {
      btn.addEventListener("click", () => {
        const prod = products.find((p) => p.id === Number(btn.dataset.add));
        addToCart(prod);
        updateFab();
      });
    });
  }

  function updateFab() {
    const items = getCart();
    const count = cartCount(items);
    const fab = document.getElementById("cart-fab");
    if (count > 0) {
      fab.style.display = "block";
      document.getElementById("cart-fab-count").textContent = count;
    } else {
      fab.style.display = "none";
    }
  }

  document.getElementById("search-input").addEventListener("input", (e) => {
    search = e.target.value;
    renderProducts();
  });

  async function init() {
    const res = await fetch("api/products.php");
    products = await res.json();
    renderCategoryBar();
    renderProducts();
    updateFab();
  }

  init();
});
