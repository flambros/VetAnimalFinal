/**
 * booking.js — lógica del wizard de reserva de turnos (3 pasos).
 */
document.addEventListener("DOMContentLoaded", () => {
  const ICON_MAP = { heart: "♡", scalpel: "🩹", flask: "🧪", syringe: "💉" };
  const BASE_TIMES = ["09:00","09:30","10:00","10:30","11:00","11:30","13:30","14:00","14:30","15:30","16:00"];
  const MONTH_NAMES = ["","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"];
  const DEFAULT_PET_IMG = "https://images.unsplash.com/photo-1552053831-71594a27632d?w=200&q=80";

  const state = {
    step: 1,
    pets: [],
    services: [],
    selectedPetId: null,
    selectedServiceId: null,
    currentMonth: (() => {
      const d = new Date();
      return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, "0")}`;
    })(),
    bookedDays: [],
    selectedDate: "",
    occupiedTimes: [],
    selectedTime: "",
  };

  function selectedPet() {
    return state.pets.find((p) => p.id === state.selectedPetId);
  }
  function selectedService() {
    return state.services.find((s) => s.id === state.selectedServiceId);
  }

  function showError(msg) {
    const box = document.getElementById("wizard-error");
    box.textContent = msg;
    box.style.display = "block";
  }
  function hideError() {
    document.getElementById("wizard-error").style.display = "none";
  }

  function goToStep(n) {
    state.step = n;
    document.getElementById("step-1").style.display = n === 1 ? "block" : "none";
    document.getElementById("step-2").style.display = n === 2 ? "block" : "none";
    document.getElementById("step-3").style.display = n === 3 ? "block" : "none";

    for (let i = 1; i <= 3; i++) {
      const el = document.getElementById(`step-indicator-${i}`);
      el.classList.remove("active", "done");
      if (i < n) el.classList.add("done");
      else if (i === n) el.classList.add("active");
      el.querySelector(".step-circle").textContent = i < n ? "✓" : i;
    }
    document.getElementById("line-1").classList.toggle("done", n > 1);
    document.getElementById("line-2").classList.toggle("done", n > 2);
    hideError();
  }

  // ---------- STEP 1 ----------
  function renderPets() {
    const grid = document.getElementById("pet-select-grid");
    if (state.pets.length === 0) {
      document.getElementById("pets-empty").style.display = "block";
      document.getElementById("pets-wrapper").style.display = "none";
      return;
    }
    document.getElementById("pets-empty").style.display = "none";
    document.getElementById("pets-wrapper").style.display = "block";
    grid.innerHTML = state.pets.map((p) => `
      <div class="option-card ${state.selectedPetId === p.id ? "selected" : ""}" data-pet-id="${p.id}">
        <img src="${p.foto || DEFAULT_PET_IMG}" alt="${p.nombre}">
        <div><strong>${p.nombre}</strong><small>${p.especie} · ${p.raza || "Sin raza"}</small></div>
      </div>
    `).join("");
    grid.querySelectorAll("[data-pet-id]").forEach((el) => {
      el.addEventListener("click", () => {
        state.selectedPetId = Number(el.dataset.petId);
        renderPets();
        updateStep1Button();
      });
    });
  }

  function renderServices() {
    const grid = document.getElementById("service-select-grid");
    grid.innerHTML = state.services.map((s) => `
      <div class="option-card ${state.selectedServiceId === s.id ? "selected" : ""}" data-service-id="${s.id}">
        <div class="icon-badge">${ICON_MAP[s.icono] || "🩺"}</div>
        <div><strong>${s.nombre}</strong><small>${s.descripcion}</small></div>
      </div>
    `).join("");
    grid.querySelectorAll("[data-service-id]").forEach((el) => {
      el.addEventListener("click", () => {
        state.selectedServiceId = Number(el.dataset.serviceId);
        renderServices();
        updateStep1Button();
      });
    });
  }

  function updateStep1Button() {
    document.getElementById("btn-step1-next").disabled = !(state.selectedPetId && state.selectedServiceId);
  }

  // ---------- STEP 2 ----------
  function renderCalendar() {
    const [yearStr, monthStr] = state.currentMonth.split("-");
    const yearNum = parseInt(yearStr, 10);
    const monthNum = parseInt(monthStr, 10);
    document.getElementById("cal-month-label").textContent = `${MONTH_NAMES[monthNum]} ${yearNum}`;

    const firstDayOfWeek = new Date(yearNum, monthNum - 1, 1).getDay();
    const totalDays = new Date(yearNum, monthNum, 0).getDate();
    const todayStr = new Date().toISOString().substring(0, 10);

    let html = "";
    ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sá"].forEach((d) => {
      html += `<div class="dow">${d}</div>`;
    });
    for (let i = 0; i < firstDayOfWeek; i++) html += `<div class="cal-day muted"></div>`;

    for (let d = 1; d <= totalDays; d++) {
      const dateFormatted = `${yearNum}-${String(monthNum).padStart(2, "0")}-${String(d).padStart(2, "0")}`;
      const isPast = dateFormatted < todayStr;
      const isSelected = state.selectedDate === dateFormatted;
      const isToday = dateFormatted === todayStr;
      const hasTurnos = state.bookedDays.includes(d);

      let cls = "cal-day";
      if (isPast) cls += " muted";
      if (isToday) cls += " today";
      if (isSelected) cls += " selected";
      if (hasTurnos) cls += " has-turnos";

      html += `<button type="button" class="${cls}" ${isPast ? "disabled" : ""} data-date="${dateFormatted}">${d}</button>`;
    }
    document.getElementById("cal-grid").innerHTML = html;

    document.querySelectorAll("#cal-grid [data-date]").forEach((btn) => {
      btn.addEventListener("click", () => {
        state.selectedDate = btn.dataset.date;
        state.selectedTime = "";
        renderCalendar();
        loadOccupiedTimes();
      });
    });
  }

  function renderTimes() {
    if (!state.selectedDate) {
      document.getElementById("times-empty").style.display = "block";
      document.getElementById("times-wrapper").style.display = "none";
      return;
    }
    document.getElementById("times-empty").style.display = "none";
    document.getElementById("times-wrapper").style.display = "block";
    document.getElementById("selected-date-label").textContent = state.selectedDate;

    document.getElementById("times-grid").innerHTML = BASE_TIMES.map((h) => {
      const isOccupied = state.occupiedTimes.includes(h);
      const isSelected = state.selectedTime === h;
      let cls = "time-slot";
      if (isOccupied) cls += " disabled";
      if (isSelected) cls += " selected";
      return `<button type="button" class="${cls}" ${isOccupied ? "disabled" : ""} data-time="${h}">${h} hs</button>`;
    }).join("");

    document.querySelectorAll("#times-grid [data-time]").forEach((btn) => {
      btn.addEventListener("click", () => {
        state.selectedTime = btn.dataset.time;
        renderTimes();
        updateSummary();
        updateStep2Button();
      });
    });
  }

  function updateSummary() {
    const pet = selectedPet();
    const service = selectedService();
    document.getElementById("summary-pet-img").src = (pet && pet.foto) || DEFAULT_PET_IMG;
    document.getElementById("summary-pet-name").textContent = pet ? pet.nombre : "";
    document.getElementById("summary-pet-detail").textContent = pet ? `${pet.especie} · ${pet.raza || ""}` : "";
    document.getElementById("summary-service-name").textContent = service ? service.nombre : "";
    document.getElementById("summary-service-detail").textContent = service ? service.descripcion : "";
    document.getElementById("step2-sub").textContent = `Elegí un horario conveniente para el ${service ? service.nombre : ""} de ${pet ? pet.nombre : ""}.`;

    const summarySelected = document.getElementById("summary-selected");
    if (state.selectedDate) {
      summarySelected.style.display = "block";
      document.getElementById("summary-selected-text").textContent =
        state.selectedDate + (state.selectedTime ? ` a las ${state.selectedTime} hs` : "");
      document.getElementById("summary-ok").style.display = state.selectedTime ? "block" : "none";
    } else {
      summarySelected.style.display = "none";
    }
  }

  function updateStep2Button() {
    document.getElementById("btn-step2-next").disabled = !(state.selectedDate && state.selectedTime);
  }

  async function loadBookedDates() {
    const res = await fetch(`api/turnos.php?action=booked-dates&mes=${state.currentMonth}`);
    state.bookedDays = await res.json();
    renderCalendar();
  }

  async function loadOccupiedTimes() {
    if (!state.selectedDate) {
      state.occupiedTimes = [];
      renderTimes();
      updateSummary();
      updateStep2Button();
      return;
    }
    const res = await fetch(`api/turnos.php?action=occupied-times&fecha=${state.selectedDate}`);
    state.occupiedTimes = await res.json();
    renderTimes();
    updateSummary();
    updateStep2Button();
  }

  // ---------- STEP 3 ----------
  function renderReview() {
    const pet = selectedPet();
    const service = selectedService();
    document.getElementById("review-pet-img").src = (pet && pet.foto) || DEFAULT_PET_IMG;
    document.getElementById("review-pet-name").textContent = pet ? pet.nombre : "";
    document.getElementById("review-pet-detail").textContent = pet ? `${pet.especie} · ${pet.raza || ""}` : "";
    document.getElementById("review-service-name").textContent = service ? service.nombre : "";
    document.getElementById("review-service-desc").textContent = service ? service.descripcion : "";
    document.getElementById("review-date").textContent = state.selectedDate;
    document.getElementById("review-time").textContent = state.selectedTime;

    document.getElementById("review-summary-pet").textContent = pet ? pet.nombre : "";
    document.getElementById("review-summary-service").textContent = service ? service.nombre : "";
    document.getElementById("review-summary-datetime").textContent = `${state.selectedDate} · ${state.selectedTime} hs`;
  }

  // ---------- Confirm ----------
  async function confirmBooking() {
    const btn = document.getElementById("btn-confirm");
    btn.disabled = true;
    btn.textContent = "Confirmando...";
    hideError();

    try {
      const res = await fetch("api/turnos.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          mascota_id: state.selectedPetId,
          servicio_id: state.selectedServiceId,
          fecha: state.selectedDate,
          hora: state.selectedTime,
          notas: document.getElementById("notas").value,
        }),
      });
      const data = await res.json();
      btn.disabled = false;
      btn.textContent = "Confirmar Turno ✓";

      if (!res.ok) {
        showError(data.error || "Error al reservar turno");
        return;
      }

      document.getElementById("step-3").style.display = "none";
      document.getElementById("step-confirmed").style.display = "block";
      document.getElementById("confirmed-text").innerHTML =
        `Reservamos el turno de <strong>${data.servicio_nombre}</strong> para <strong>${data.mascota_nombre}</strong> el ${data.fecha} a las ${data.hora} hs.`;
    } catch (e) {
      btn.disabled = false;
      btn.textContent = "Confirmar Turno ✓";
      showError("Error de red");
    }
  }

  // ---------- Nav buttons ----------
  document.getElementById("btn-step1-next").addEventListener("click", () => {
    if (!state.selectedPetId || !state.selectedServiceId) {
      showError("Elegí una mascota y un servicio para continuar.");
      return;
    }
    goToStep(2);
    loadBookedDates();
    renderTimes();
    updateSummary();
  });

  document.getElementById("btn-step2-back").addEventListener("click", () => goToStep(1));
  document.getElementById("btn-step2-next").addEventListener("click", () => {
    if (!state.selectedDate || !state.selectedTime) {
      showError("Elegí una fecha y un horario disponible.");
      return;
    }
    goToStep(3);
    renderReview();
  });
  document.getElementById("btn-step3-back").addEventListener("click", () => goToStep(2));
  document.getElementById("btn-confirm").addEventListener("click", confirmBooking);

  document.getElementById("cal-prev").addEventListener("click", () => {
    const [y, m] = state.currentMonth.split("-").map(Number);
    const d = new Date(y, m - 2, 1);
    state.currentMonth = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, "0")}`;
    loadBookedDates();
  });
  document.getElementById("cal-next").addEventListener("click", () => {
    const [y, m] = state.currentMonth.split("-").map(Number);
    const d = new Date(y, m, 1);
    state.currentMonth = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, "0")}`;
    loadBookedDates();
  });

  // ---------- Init ----------
  async function init() {
    const userId = window.CURRENT_USER_ID;
    const [petsRes, servicesRes] = await Promise.all([
      fetch(`api/pets.php?userId=${userId}`),
      fetch("api/services.php"),
    ]);
    state.pets = await petsRes.json();
    state.services = await servicesRes.json();

    if (state.pets.length > 0) state.selectedPetId = state.pets[0].id;
    if (state.services.length > 0) state.selectedServiceId = state.services[0].id;

    renderPets();
    renderServices();
    updateStep1Button();
  }

  init();
});
