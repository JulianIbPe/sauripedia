// ============================================================
// quiz.js
// Lógica del quiz. Las preguntas ya no están acá: se descargan de
// datos/preguntas.json (antes ocupaban 1700 de las 1977 líneas del archivo).
// Separar datos de lógica permite agregar o corregir preguntas sin tocar código.
//
// Formato de cada pregunta en el JSON:
//   tipo "elegir":   { pregunta, opciones: [4 opciones], correcta: índice }
//   tipo "multiple": { pregunta, opciones: [...], correctas: [índices] }
//   tipo "vf":       { pregunta (afirmación), correcta: true / false, explicacion }
//   "ref" identifica el dato de la ficha: evita preguntar dos veces lo mismo en una partida.
// ============================================================

// En cada partida se eligen al azar estas cantidades del banco del nivel
const CONFIG_PARTIDA = { elegir: 6, multiple: 4, vf: 5 };

const NOMBRE_TIPO = {
    elegir: "Elige la opción correcta",
    multiple: "Múltiples opciones: marca todas las correctas",
    vf: "Verdadero o falso"
};

// Poner en false para que las preguntas / opciones salgan siempre en el mismo orden
const MEZCLAR_PREGUNTAS = true;
const MEZCLAR_OPCIONES = true;

// ============================================================
//  LÓGICA DEL QUIZ
// ============================================================
function mezclar(lista) {
    const copia = [...lista];
    for (let i = copia.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [copia[i], copia[j]] = [copia[j], copia[i]];
    }
    return copia;
}

// Convierte una pregunta del banco al formato que usa la pantalla
function prepararPregunta(p) {
    if (p.tipo === "vf") {
        return {
            tipo: "vf",
            pregunta: p.pregunta,
            opciones: ["Verdadero", "Falso"],
            correcta: p.correcta ? 0 : 1,
            correctas: [p.correcta ? 0 : 1],
            explicacion: p.explicacion
        };
    }
    const buenas = p.tipo === "multiple" ? p.correctas : [p.correcta];
    const lista = p.opciones.map((texto, i) => ({ texto, ok: buenas.includes(i) }));
    const final = MEZCLAR_OPCIONES ? mezclar(lista) : lista;
    const correctas = final.flatMap((o, i) => (o.ok ? [i] : []));
    return {
        tipo: p.tipo,
        pregunta: p.pregunta,
        opciones: final.map(o => o.texto),
        correcta: correctas[0],
        correctas
    };
}

// Elige las preguntas de una partida sin repetir el mismo dato de la ficha
function armarPartida(banco) {
    const usadas = new Set();
    const elegidas = [];

    for (const [tipo, cantidad] of Object.entries(CONFIG_PARTIDA)) {
        let n = 0;
        for (const p of mezclar(banco.filter(p => p.tipo === tipo))) {
            if (n >= cantidad) break;
            if (usadas.has(p.ref)) continue;
            usadas.add(p.ref);
            elegidas.push(p);
            n++;
        }
    }

    return (MEZCLAR_PREGUNTAS ? mezclar(elegidas) : elegidas).map(prepararPregunta);
}

function iniciarQuiz(banco) {

    const el = {
        juego: document.getElementById("quiz-juego"),
        zona: document.getElementById("zona-pregunta"),
        barraContenedor: document.querySelector("#quiz-juego .barra"),
        resultado: document.getElementById("quiz-resultado"),
        contador: document.getElementById("contador"),
        puntaje: document.getElementById("puntaje"),
        barra: document.getElementById("barra-progreso"),
        tipo: document.getElementById("tipo-pregunta"),
        pregunta: document.getElementById("pregunta"),
        opciones: document.getElementById("opciones"),
        mensaje: document.getElementById("mensaje"),
        siguiente: document.getElementById("btn-siguiente"),
        puntajeFinal: document.getElementById("puntaje-final"),
        mensajeFinal: document.getElementById("mensaje-final"),
        reiniciar: document.getElementById("btn-reiniciar")
    };

    let preguntas = [];
    let indice = 0;
    let puntaje = 0;
    let respondida = false;
    let marcadas = new Set();

    function empezar() {
        preguntas = armarPartida(banco);
        indice = 0;
        puntaje = 0;
        el.resultado.hidden = true;
        el.juego.hidden = false;
        mostrarPregunta();
    }

    function mostrarPregunta() {
        respondida = false;
        marcadas = new Set();
        const actual = preguntas[indice];

        el.contador.textContent = `Pregunta ${indice + 1} de ${preguntas.length}`;
        el.puntaje.textContent = `Puntaje: ${puntaje}`;
        actualizarBarra((indice / preguntas.length) * 100);
        el.tipo.textContent = NOMBRE_TIPO[actual.tipo];
        el.pregunta.textContent = actual.pregunta;
        el.mensaje.textContent = "";
        el.mensaje.className = "mensaje";
        // en "multiple" el mismo botón primero sirve para comprobar
        el.siguiente.textContent = "Comprobar";
        el.siguiente.disabled = true;
        el.siguiente.hidden = actual.tipo !== "multiple";
        el.opciones.innerHTML = "";
        el.opciones.classList.toggle("vf", actual.tipo === "vf");

        // Reinicia la animación de entrada de la pregunta (quitar clase + forzar reflow + volver a poner)
        el.zona.classList.remove("entra");
        void el.zona.offsetWidth;
        el.zona.classList.add("entra");

        actual.opciones.forEach((texto, i) => {
            const boton = document.createElement("button");
            boton.type = "button";
            boton.className = "opcion";
            boton.textContent = texto;
            if (actual.tipo === "multiple") {
                boton.setAttribute("aria-pressed", "false");
                boton.addEventListener("click", () => marcar(i, boton));
            } else {
                boton.addEventListener("click", () => responder(i));
            }
            el.opciones.appendChild(boton);
        });
    }

    function marcar(i, boton) {
        if (respondida) return;
        marcadas.has(i) ? marcadas.delete(i) : marcadas.add(i);
        boton.classList.toggle("seleccionada", marcadas.has(i));
        boton.setAttribute("aria-pressed", String(marcadas.has(i)));
        el.siguiente.disabled = marcadas.size === 0;
    }

    function responder(elegida) {
        if (respondida) return;
        respondida = true;

        const actual = preguntas[indice];
        const botones = [...el.opciones.children];
        botones.forEach(b => (b.disabled = true));
        actual.correctas.forEach(i => botones[i].classList.add("correcta"));

        if (actual.tipo === "multiple") {
            const acierto = marcadas.size === actual.correctas.length
                && actual.correctas.every(i => marcadas.has(i));
            marcadas.forEach(i => { if (!actual.correctas.includes(i)) botones[i].classList.add("incorrecta"); });
            if (acierto) {
                puntaje++;
                el.mensaje.textContent = "¡Correcto!";
                el.mensaje.classList.add("ok");
            } else {
                el.mensaje.textContent = `Incorrecto. Las correctas eran: ${actual.correctas.map(i => actual.opciones[i]).join(", ")}`;
                el.mensaje.classList.add("mal");
            }
        } else if (elegida === actual.correcta) {
            puntaje++;
            el.mensaje.textContent = actual.tipo === "vf" ? `¡Correcto! ${actual.explicacion}` : "¡Correcto!";
            el.mensaje.classList.add("ok");
        } else {
            botones[elegida].classList.add("incorrecta");
            el.mensaje.textContent = actual.tipo === "vf"
                ? `Incorrecto. ${actual.explicacion}`
                : `Incorrecto. La respuesta correcta era: ${actual.opciones[actual.correcta]}`;
            el.mensaje.classList.add("mal");
        }

        el.puntaje.textContent = `Puntaje: ${puntaje}`;
        el.siguiente.textContent = indice === preguntas.length - 1 ? "Ver resultado" : "Siguiente";
        el.siguiente.disabled = false;
        el.siguiente.hidden = false;
    }

    function siguiente() {
        if (!respondida) {
            if (preguntas[indice].tipo === "multiple" && marcadas.size > 0) responder();
            return;
        }
        indice++;
        if (indice < preguntas.length) {
            mostrarPregunta();
        } else {
            mostrarResultado();
        }
    }

    function actualizarBarra(porcentaje) {
        el.barra.style.width = `${porcentaje}%`;
        el.barraContenedor.setAttribute("aria-valuenow", String(Math.round(porcentaje)));
    }

    function mostrarResultado() {
        actualizarBarra(100);
        el.juego.hidden = true;
        el.resultado.hidden = false;

        const total = preguntas.length;
        const porcentaje = Math.round((puntaje / total) * 100);
        el.puntajeFinal.textContent = `${puntaje} / ${total} (${porcentaje}%)`;

        if (porcentaje === 100) {
            el.mensajeFinal.textContent = "¡Perfecto! Eres un verdadero paleontólogo.";
        } else if (porcentaje >= 70) {
            el.mensajeFinal.textContent = "¡Muy bien! Sabes bastante de dinosaurios.";
        } else if (porcentaje >= 40) {
            el.mensajeFinal.textContent = "Nada mal, pero repasa las fichas y vuelve a intentarlo.";
        } else {
            el.mensajeFinal.textContent = "Todavía te falta explorar. ¡Mira la sección de dinosaurios y prueba de nuevo!";
        }
    }

    el.siguiente.addEventListener("click", siguiente);
    el.reiniciar.addEventListener("click", empezar);

    empezar();
}

// ============================================================
//  ARRANQUE: descarga las preguntas del nivel y empieza la partida
// ============================================================
async function cargarQuiz() {
    const contenedor = document.getElementById("quiz");
    if (!contenedor) {
        return;
    }
    const juego = document.getElementById("quiz-juego");
    const nivel = contenedor.dataset.nivel;
    const raiz = document.documentElement.dataset.raiz || "";

    try {
        const respuesta = await fetch(raiz + "datos/preguntas.json");
        if (!respuesta.ok) {
            throw new Error("HTTP " + respuesta.status);
        }
        const quizzes = await respuesta.json();
        if (!quizzes[nivel]) {
            throw new Error("No existe el nivel de quiz: " + nivel);
        }
        juego.classList.add("listo");
        iniciarQuiz(quizzes[nivel]);
    } catch (error) {
        console.error("No se pudo cargar el quiz:", error);
        juego.querySelector(".quiz-cargando").textContent = "No se pudieron cargar las preguntas. Recarga la página para intentar de nuevo.";
    }
}

cargarQuiz();
