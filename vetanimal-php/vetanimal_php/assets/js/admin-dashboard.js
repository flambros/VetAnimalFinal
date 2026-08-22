document.addEventListener("DOMContentLoaded", () => {
  const hoyStr = new Date().toISOString().substring(0, 10);

  function renderTable(turnos) {
    document.getElementById("today-count").textContent = turnos.length;
    if (turnos.length === 0) {
      document.getElementById("today-empty").style.display = "block";
      document.getElementById("today-wrap").style.display = "none";
      return;
    }
    document.getElementById("today-empty").style.display = "none";
    document.getElementById("today-wrap").style.display = "block";

    document.getElementById("today-body").innerHTML = turnos.map((t) => `
      <tr>
        <td><strong>${t.hora} hs</strong></td>
        <td>${t.mascota_nombre}</td>
        <td>${t.dueno}</td>
        <td>${t.servicio_nombre}</td>
        <td><span class="badge badge-${t.estado}">${t.estado.toUpperCase()}</span></td>
        <td>
          <div class="table-actions">
            ${t.estado !== "completado" ? `<button type="button" class="btn btn-primary btn-sm" data-estado="completado" data-id="${t.id}" title="Marcar como completado">✓ Atendido</button>` : ""}
            ${t.estado !== "cancelado" ? `<button type="button" class="btn btn-danger btn-sm" data-estado="cancelado" data-id="${t.id}" title="Cancelar turno">✕</button>` : ""}
          </div>
        </td>
      </tr>
    `).join("");

    document.querySelectorAll("[data-estado]").forEach((btn) => {
      btn.addEventListener("click", () => updateEstado(Number(btn.dataset.id), btn.dataset.estado));
    });
  }

  let todayTurnos = [];

  async function updateEstado(id, estado) {
    const res = await fetch(`../api/turnos.php?id=${id}&action=estado`, {
      method: "PATCH",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ estado }),
    });
    if (res.ok) {
      todayTurnos = todayTurnos.map((t) => (t.id === id ? { ...t, estado } : t));
      renderTable(todayTurnos);
    }
  }

  async function init() {
    const [statsRes, turnosRes] = await Promise.all([
      fetch("../api/admin_stats.php"),
      fetch(`../api/turnos.php?all=true&fecha=${hoyStr}`),
    ]);
    const stats = await statsRes.json();
    todayTurnos = await turnosRes.json();

    document.getElementById("stat-hoy").textContent = stats.totalHoy;
    document.getElementById("stat-pendientes").textContent = stats.pendientes;
    document.getElementById("stat-pacientes").textContent = stats.totalPacientes;
    document.getElementById("stat-clientes").textContent = stats.totalClientes;

    renderTable(todayTurnos);
  }

  init();
});
