document.addEventListener("DOMContentLoaded", () => {
  let turnos = [];

  function renderTable() {
    const filterEstado = document.getElementById("filter-estado").value;
    const filterFecha = document.getElementById("filter-fecha").value;

    const filtered = turnos.filter((t) => {
      const matchEstado = filterEstado === "todos" || t.estado === filterEstado;
      const matchFecha = !filterFecha || t.fecha === filterFecha;
      return matchEstado && matchFecha;
    });

    document.getElementById("turnos-body").innerHTML = filtered.map((t) => `
      <tr>
        <td>#${t.id}</td>
        <td><strong>${t.fecha}</strong><br><small style="color:#6b7280;">${t.hora} hs</small></td>
        <td>${t.mascota_nombre}</td>
        <td>${t.dueno}</td>
        <td>${t.servicio_nombre}</td>
        <td><span class="badge badge-${t.estado}">${t.estado.toUpperCase()}</span></td>
        <td>
          <div class="table-actions">
            ${t.estado !== "completado" ? `<button type="button" class="btn btn-primary btn-sm u-text-xs" data-estado="completado" data-id="${t.id}">✓ Atendido</button>` : ""}
            ${t.estado !== "cancelado" ? `<button type="button" class="btn btn-danger btn-sm u-text-xs" data-estado="cancelado" data-id="${t.id}">✕ Cancelar</button>` : ""}
          </div>
        </td>
      </tr>
    `).join("");

    document.querySelectorAll("[data-estado]").forEach((btn) => {
      btn.addEventListener("click", () => updateEstado(Number(btn.dataset.id), btn.dataset.estado));
    });
  }

  async function updateEstado(id, estado) {
    const res = await fetch(`../api/turnos.php?id=${id}&action=estado`, {
      method: "PATCH",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ estado }),
    });
    if (res.ok) {
      turnos = turnos.map((t) => (t.id === id ? { ...t, estado } : t));
      renderTable();
    }
  }

  document.getElementById("filter-estado").addEventListener("change", renderTable);
  document.getElementById("filter-fecha").addEventListener("change", (e) => {
    document.getElementById("clear-fecha").style.display = e.target.value ? "inline" : "none";
    renderTable();
  });
  document.getElementById("clear-fecha").addEventListener("click", () => {
    document.getElementById("filter-fecha").value = "";
    document.getElementById("clear-fecha").style.display = "none";
    renderTable();
  });

  async function init() {
    const res = await fetch("../api/turnos.php?all=true");
    turnos = await res.json();
    renderTable();
  }

  init();
});
