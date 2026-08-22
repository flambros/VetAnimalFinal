document.addEventListener("DOMContentLoaded", () => {
  const DEFAULT_PET_IMG = "https://images.unsplash.com/photo-1552053831-71594a27632d?w=200&q=80";
  let pets = [];

  function renderTable() {
    const s = document.getElementById("search-input").value.toLowerCase();
    const filtered = pets.filter((p) =>
      p.nombre.toLowerCase().includes(s) ||
      p.especie.toLowerCase().includes(s) ||
      (p.dueno && p.dueno.toLowerCase().includes(s)) ||
      (p.raza && p.raza.toLowerCase().includes(s))
    );

    document.getElementById("pets-body").innerHTML = filtered.map((p) => `
      <tr>
        <td>
          <div class="u-flex u-items-center u-gap-3">
            <img src="${p.foto || DEFAULT_PET_IMG}" alt="${p.nombre}" class="u-avatar u-avatar-40">
            <strong>${p.nombre}</strong>
          </div>
        </td>
        <td>${p.especie}<br><small style="color:#6b7280;">${p.raza || "-"}</small></td>
        <td>${p.edad ? p.edad + " años" : "-"}<br><small style="color:#6b7280;">${p.peso ? p.peso + " kg" : "-"}</small></td>
        <td><strong>${p.dueno || "Cliente"}</strong><br><small style="color:#6b7280;">${p.telefono || "-"}</small></td>
        <td><span class="status-pill">${p.estado_salud}</span></td>
        <td><a href="../historial.php" class="btn btn-outline btn-sm u-text-xs">📋 Historial</a></td>
      </tr>
    `).join("");
  }

  document.getElementById("search-input").addEventListener("input", renderTable);

  async function init() {
    const res = await fetch("../api/pets.php?all=true");
    pets = await res.json();
    renderTable();
  }

  init();
});
