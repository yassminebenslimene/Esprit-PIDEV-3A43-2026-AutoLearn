/* ========================================
   AMELIORATIONS NAVBAR & SIDEBAR - Ilef
   JavaScript pour interactions
   ======================================== */

document.addEventListener('DOMContentLoaded', function() {
    
    // ===== SIDEBAR TOGGLE (Mobile) =====
    initSidebarToggle();
    
    // ===== LANGUAGE MENU =====
    initLanguageMenu();
    
    // ===== SEARCH BOX (Mobile) =====
    initSearchBox();
    
    // ===== ACTIVE PAGE INDICATOR =====
    initActivePageIndicator();
    
    // ===== TOOLTIPS =====
    initTooltips();
    
    // ===== KEYBOARD NAVIGATION =====
    initKeyboardNavigation();
    
    // ===== RESPONSIVE HANDLERS =====
    initResponsiveHandlers();
});

// ===== SIDEBAR TOGGLE =====
function initSidebarToggle() {
    // Créer le bouton burger s'il n'existe pas
    if (!document.querySelector('.sidebar-toggle')) {
        const toggleBtn = document.createElement('button');
        toggleBtn.className = 'sidebar-toggle';
        toggleBtn.setAttribute('aria-label', 'Toggle sidebar');
        toggleBtn.innerHTML = `
            <span></span>
            <span></span>
            <span></span>
        `;
        document.body.appendChild(toggleBtn);
        
        // Créer l'overlay
        const overlay = document.createElement('div');
        overlay.className = 'sidebar-overlay';
        document.body.appendChild(overlay);
    }
    
    const toggleBtn = document.querySelector('.sidebar-toggle');
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.querySelector('.sidebar-overlay');
    
    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', function() {
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
            toggleBtn.classList.toggle('active');
            
            // Animation du burger
            const spans = toggleBtn.querySelectorAll('span');
            if (sidebar.classList.contains('active')) {
                spans[0].style.transform = 'rotate(45deg) translateY(8px)';
                spans[1].style.opacity = '0';
                spans[2].style.transform = 'rotate(-45deg) translateY(-8px)';
            } else {
                spans[0].style.transform = '';
                spans[1].style.opacity = '';
                spans[2].style.transform = '';
            }
        });
        
        // Fermer au clic sur l'overlay
        overlay.addEventListener('click', function() {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
            toggleBtn.classList.remove('active');
            
            const spans = toggleBtn.querySelectorAll('span');
            spans[0].style.transform = '';
            spans[1].style.opacity = '';
            spans[2].style.transform = '';
        });
    }
}

// ===== LANGUAGE MENU =====
function initLanguageMenu() {
    const languageToggle = document.getElementById('language-toggle');
    const languageMenu = document.getElementById('languageMenu');
    
    if (languageToggle && languageMenu) {
        languageToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            languageMenu.classList.toggle('active');
            languageMenu.style.display = languageMenu.classList.contains('active') ? 'block' : 'none';
        });
        
        // Fermer au clic extérieur
        document.addEventListener('click', function(e) {
            if (!languageToggle.contains(e.target) && !languageMenu.contains(e.target)) {
                languageMenu.classList.remove('active');
                languageMenu.style.display = 'none';
            }
        });
        
        // Fermer avec Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && languageMenu.classList.contains('active')) {
                languageMenu.classList.remove('active');
                languageMenu.style.display = 'none';
            }
        });
    }
}

// ===== SEARCH BOX (Mobile) =====
function initSearchBox() {
    const searchBox = document.querySelector('.search-box');
    const searchInput = document.querySelector('.search-input');
    
    if (searchBox && searchInput && window.innerWidth <= 480) {
        const searchIcon = searchBox.querySelector('.search-icon');
        
        if (searchIcon) {
            searchIcon.addEventListener('click', function() {
                searchBox.classList.toggle('active');
                if (searchBox.classList.contains('active')) {
                    searchInput.focus();
                }
            });
            
            // Fermer si on clique en dehors
            document.addEventListener('click', function(e) {
                if (!searchBox.contains(e.target)) {
                    searchBox.classList.remove('active');
                }
            });
        }
    }
}

// ===== ACTIVE PAGE INDICATOR =====
function initActivePageIndicator() {
    const currentPath = window.location.pathname;
    const navLinks = document.querySelectorAll('.nav-link');
    
    navLinks.forEach(link => {
        const linkPath = new URL(link.href).pathname;
        
        if (currentPath === linkPath || currentPath.startsWith(linkPath + '/')) {
            link.classList.add('active');
            
            // Ouvrir la section parente si elle est collapsible
            const parentSection = link.closest('.nav-section');
            if (parentSection) {
                parentSection.classList.add('active');
            }
        }
    });
}

// ===== TOOLTIPS =====
function initTooltips() {
    const navBtns = document.querySelectorAll('.nav-btn');
    
    navBtns.forEach(btn => {
        if (btn.hasAttribute('title')) {
            btn.setAttribute('data-tooltip', btn.getAttribute('title'));
            btn.classList.add('tooltip');
            btn.removeAttribute('title'); // Enlever le tooltip natif
        }
    });
}

// ===== KEYBOARD NAVIGATION =====
function initKeyboardNavigation() {
    const navLinks = document.querySelectorAll('.nav-link');
    
    navLinks.forEach((link, index) => {
        link.addEventListener('keydown', function(e) {
            // Flèche bas : aller au lien suivant
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                const nextLink = navLinks[index + 1];
                if (nextLink) nextLink.focus();
            }
            
            // Flèche haut : aller au lien précédent
            if (e.key === 'ArrowUp') {
                e.preventDefault();
                const prevLink = navLinks[index - 1];
                if (prevLink) prevLink.focus();
            }
            
            // Home : aller au premier lien
            if (e.key === 'Home') {
                e.preventDefault();
                navLinks[0].focus();
            }
            
            // End : aller au dernier lien
            if (e.key === 'End') {
                e.preventDefault();
                navLinks[navLinks.length - 1].focus();
            }
        });
    });
}

// ===== RESPONSIVE HANDLERS =====
function initResponsiveHandlers() {
    let resizeTimer;
    
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.querySelector('.sidebar-overlay');
            const toggleBtn = document.querySelector('.sidebar-toggle');
            
            // Réinitialiser la sidebar sur desktop
            if (window.innerWidth > 1024) {
                if (sidebar) sidebar.classList.remove('active');
                if (overlay) overlay.classList.remove('active');
                if (toggleBtn) {
                    toggleBtn.classList.remove('active');
                    const spans = toggleBtn.querySelectorAll('span');
                    spans[0].style.transform = '';
                    spans[1].style.opacity = '';
                    spans[2].style.transform = '';
                }
            }
            
            // Réinitialiser la search box
            const searchBox = document.querySelector('.search-box');
            if (window.innerWidth > 480 && searchBox) {
                searchBox.classList.remove('active');
            }
        }, 250);
    });
}

// ===== SMOOTH SCROLL =====
function smoothScroll(target) {
    const element = document.querySelector(target);
    if (element) {
        element.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }
}

// ===== UTILITY FUNCTIONS =====

// Fermer tous les dropdowns
function closeAllDropdowns() {
    const dropdowns = document.querySelectorAll('.language-menu');
    dropdowns.forEach(dropdown => {
        dropdown.classList.remove('active');
        dropdown.style.display = 'none';
    });
}

// Vérifier si un élément est visible
function isElementVisible(element) {
    return element.offsetWidth > 0 && element.offsetHeight > 0;
}

// Obtenir la largeur de la fenêtre
function getWindowWidth() {
    return window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
}

// Exporter les fonctions pour utilisation externe
window.sidebarImprovements = {
    closeAllDropdowns,
    isElementVisible,
    getWindowWidth,
    smoothScroll
};

// ===== CONSOLE LOG (Development) =====
console.log('✅ Navbar & Sidebar Improvements loaded');
console.log('📱 Window width:', getWindowWidth());
