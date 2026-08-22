document.addEventListener("DOMContentLoaded", () => {
  const DEFAULT_PET_IMG = "https://images.unsplash.com/photo-1552053831-71594a27632d?w=500&q=80";
  let pets = [];
  let selectedPetId = null;
  let pet = null;

  async function loadPets() {
    const endpoint = window.CURRENT_USER_ROL === "veterinario"
      ? "api/pets.php?all=true"
      : `api/pets.php?userId=${window.CURRENT_USER_ID}`;
    const res = await fetch(endpoint);
    pets = await res.json();

    if (pets.length === 0) {
      document.getElementById("empty-no-pets").style.display = "block";
      document.getElementById("hist-content").style.display = "none";
      return;
    }

    document.getElementById("empty-no-pets").style.display = "none";
    document.getElementById("hist-content").style.display = "block";
    selectedPetId = pets[0].id;

    if (pets.length > 1) {
      const wrap = document.getElementById("pet-selector-wrap");
      wrap.style.display = "flex";
      const sel = document.getElementById("pet-selector");
      sel.innerHTML = pets.map((p) => `<option value="${p.id}">${p.nombre} (${p.especie})</option>`).join("");
      sel.addEventListener("change", () => {
        selectedPetId = Number(sel.value);
        loadPetDetail();
      });
    }

    loadPetDetail();
  }

  async function loadPetDetail() {
    const [petRes, consultasRes, vacunasRes, estudiosRes] = await Promise.all([
      fetch(`api/pets.php?id=${selectedPetId}`),
      fetch(`api/consultas.php?mascota_id=${selectedPetId}`),
      fetch(`api/vacunas.php?mascota_id=${selectedPetId}`),
      fetch(`api/estudios.php?mascota_id=${selectedPetId}`),
    ]);
    pet = await petRes.json();
    const consultas = await consultasRes.json();
    const vacunas = await vacunasRes.json();
    const estudios = await estudiosRes.json();

    renderPetCard();
    renderTimeline(consultas);
    renderVacunas(vacunas);
    renderEstudios(estudios);
    renderAlergias();

    document.getElementById("btn-add-consulta").style.display =
      window.CURRENT_USER_ROL === "veterinario" ? "inline-flex" : "none";
  }

  function renderPetCard() {
    document.getElementById("pet-photo").src = pet.foto || DEFAULT_PET_IMG;
    document.getElementById("pet-name").textContent = pet.nombre;
    document.getElementById("pet-especie").textContent = pet.especie;
    document.getElementById("pet-raza").textContent = pet.raza || "No especificada";
    document.getElementById("pet-edad").textContent = pet.edad ? `${pet.edad} años` : "-";
    document.getElementById("pet-peso").textContent = pet.peso ? `${pet.peso} kg` : "-";
    document.getElementById("pet-estado").textContent = pet.estado_salud;
    if (pet.dueno) {
      document.getElementById("pet-dueno-row").style.display = "flex";
      document.getElementById("pet-dueno").textContent = pet.dueno;
    } else {
      document.getElementById("pet-dueno-row").style.display = "none";
    }
    document.getElementById("modal-pet-name").textContent = pet.nombre;
  }

  function renderTimeline(consultas) {
    const list = document.getElementById("timeline-list");
    if (consultas.length === 0) {
      list.innerHTML = `<p style="text-align:center; color:#6b7280; padding:24px 0;">Aún no hay registros médicos anotados para esta mascota.</p>`;
      return;
    }
    list.innerHTML = consultas.map((c) => `
      <div class="timeline-item ${c.tipo.toLowerCase()}">
        <div class="timeline-dot"></div>
        <div class="timeline-body">
          <div class="timeline-top">
            <span class="timeline-date">${c.fecha}</span>
            <span class="tag tag-${c.tipo.toLowerCase()}">${c.tipo}</span>
          </div>
          <h4>${c.titulo}</h4>
          <p>${c.descripcion}</p>
          <div class="timeline-doc">👨‍⚕️ Atendido por: ${c.vet_nombre}</div>
        </div>
      </div>
    `).join("");
  }

  function renderVacunas(vacunas) {
    const box = document.getElementById("vacunas-list");
    if (vacunas.length === 0) {
      box.innerHTML = `<p class="u-text-sm u-muted">Sin registros de vacunas.</p>`;
      return;
    }
    box.innerHTML = vacunas.map((v) => `
      <div class="mini-card">
        <div><strong>${v.nombre}</strong><small>Refuerzo: ${v.fecha_refuerzo || "No programado"}</small></div>
        <span class="tag ${v.estado === "AL_DIA" ? "tag-control" : "tag-emergencia"}">${v.estado === "AL_DIA" ? "Al día" : "Vencida"}</span>
      </div>
    `).join("");
  }

  function renderEstudios(estudios) {
    const box = document.getElementById("estudios-list");
    if (estudios.length === 0) {
      box.innerHTML = `<p class="u-text-sm u-muted">Sin estudios registrados.</p>`;
      return;
    }
    box.innerHTML = estudios.map((e) => `
      <div class="mini-card">
        <div><strong>${e.nombre}</strong><small>${e.fecha}</small></div>
        <button type="button" class="icon-btn" title="Ver Informe" onclick="alert('Visualización de informe digital.')">📄</button>
      </div>
    `).join("");
  }

  function renderAlergias() {
    const box = document.getElementById("alergias-list");
    if (pet.alergias) {
      box.innerHTML = pet.alergias.split(",").map((a) => `<span class="chip">${a.trim()}</span>`).join("");
    } else {
      box.innerHTML = `<span class="u-text-sm u-muted">Ninguna registrada</span>`;
    }
    document.getElementById("condiciones-text").textContent = pet.condiciones_cronicas || "Ninguna.";
  }

  // Modal
  document.getElementById("btn-add-consulta").addEventListener("click", () => {
    document.getElementById("modal-consulta").style.display = "flex";
  });
  document.getElementById("btn-cancel-consulta").addEventListener("click", () => {
    document.getElementById("modal-consulta").style.display = "none";
  });

  document.getElementById("consulta-form").addEventListener("submit", async (e) => {
    e.preventDefault();
    const titulo = document.getElementById("consulta-titulo").value.trim();
    const descripcion = document.getElementById("consulta-desc").value.trim();
    if (!titulo || !descripcion) return;

    const btn = document.getElementById("btn-save-consulta");
    btn.disabled = true;
    btn.textContent = "Guardando...";

    try {
      const res = await fetch("api/consultas.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          mascota_id: selectedPetId,
          veterinario_id: window.CURRENT_USER_ID,
          tipo: document.getElementById("consulta-tipo").value,
          titulo,
          descripcion,
        }),
      });
      await res.json();
      btn.disabled = false;
      btn.textContent = "Guardar Registro";
      document.getElementById("modal-consulta").style.display = "none";
      document.getElementById("consulta-titulo").value = "";
      document.getElementById("consulta-desc").value = "";
      loadPetDetail();
    } catch (e) {
      btn.disabled = false;
      btn.textContent = "Guardar Registro";
    }
  });

  loadPets();
});
