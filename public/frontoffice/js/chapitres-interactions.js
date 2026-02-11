// ===== INTERACTIONS DYNAMIQUES POUR LES CHAPITRES =====

document.addEventListener('DOMContentLoaded', function() {
    
    // ===== ANIMATION AU SCROLL =====
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('revealed');
            }
        });
    }, observerOptions);

    // Observer tous les éléments avec la classe scroll-reveal
    document.querySelectorAll('.chapter-card').forEach(card => {
        card.classList.add('scroll-reveal');
        observer.observe(card);
    });

    // ===== EFFET DE TYPING POUR LE TITRE =====
    const pageTitle = document.querySelector('.page-title');
    if (pageTitle) {
        const text = pageTitle.textContent;
        pageTitle.textContent = '';
        pageTitle.classList.add('typing-effect');
        
        let i = 0;
        const typeWriter = () => {
            if (i < text.length) {
                pageTitle.textContent += text.charAt(i);
                i++;
                setTimeout(typeWriter, 100);
            } else {
                pageTitle.classList.remove('typing-effect');
            }
        };
        
        setTimeout(typeWriter, 500);
    }

    // ===== EFFET DE PARALLAXE LÉGER =====
    window.addEventListener('scroll', function() {
        const scrolled = window.pageYOffset;
        const parallaxElements = document.querySelectorAll('.chapter-icon');
        
        parallaxElements.forEach(element => {
            const speed = 0.5;
            element.style.transform = `translateY(${scrolled * speed}px)`;
        });
    });

    // ===== ANIMATION DES COMPTEURS =====
    function animateCounter(element, target) {
        let current = 0;
        const increment = target / 50;
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            element.textContent = Math.floor(current);
        }, 20);
    }

    // ===== EFFET DE HOVER AVANCÉ =====
    document.querySelectorAll('.chapter-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-15px) scale(1.02)';
            
            // Ajouter un effet de lueur
            this.style.boxShadow = '0 25px 80px rgba(102, 126, 234, 0.3)';
        });

        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
            this.style.boxShadow = '';
        });
    });

    // ===== EFFET DE RIPPLE SUR LES BOUTONS =====
    document.querySelectorAll('.btn-modern').forEach(button => {
        button.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.classList.add('ripple');
            
            this.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });

    // ===== LAZY LOADING DES IMAGES =====
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.classList.remove('loading-skeleton');
                observer.unobserve(img);
            }
        });
    });

    document.querySelectorAll('img[data-src]').forEach(img => {
        imageObserver.observe(img);
    });

    // ===== ANIMATION DE PROGRESSION DE LECTURE =====
    if (document.querySelector('.chapter-detail-content')) {
        const progressBar = document.createElement('div');
        progressBar.className = 'reading-progress';
        progressBar.innerHTML = '<div class="reading-progress-fill"></div>';
        document.body.appendChild(progressBar);

        window.addEventListener('scroll', () => {
            const content = document.querySelector('.chapter-detail-content');
            const contentHeight = content.offsetHeight;
            const windowHeight = window.innerHeight;
            const scrollTop = window.pageYOffset;
            const contentTop = content.offsetTop;
            
            const progress = Math.min(
                Math.max((scrollTop - contentTop + windowHeight) / contentHeight, 0),
                1
            );
            
            document.querySelector('.reading-progress-fill').style.width = (progress * 100) + '%';
        });
    }

    // ===== EFFET DE PARTICULES AU CLIC =====
    document.addEventListener('click', function(e) {
        if (e.target.closest('.chapter-card')) {
            createParticles(e.clientX, e.clientY);
        }
    });

    function createParticles(x, y) {
        for (let i = 0; i < 6; i++) {
            const particle = document.createElement('div');
            particle.className = 'particle';
            particle.style.cssText = `
                position: fixed;
                width: 6px;
                height: 6px;
                background: #667eea;
                border-radius: 50%;
                pointer-events: none;
                z-index: 9999;
                left: ${x}px;
                top: ${y}px;
            `;
            
            document.body.appendChild(particle);
            
            const angle = (i / 6) * Math.PI * 2;
            const velocity = 100;
            const vx = Math.cos(angle) * velocity;
            const vy = Math.sin(angle) * velocity;
            
            let opacity = 1;
            let posX = x;
            let posY = y;
            
            const animate = () => {
                posX += vx * 0.02;
                posY += vy * 0.02;
                opacity -= 0.02;
                
                particle.style.left = posX + 'px';
                particle.style.top = posY + 'px';
                particle.style.opacity = opacity;
                
                if (opacity > 0) {
                    requestAnimationFrame(animate);
                } else {
                    particle.remove();
                }
            };
            
            requestAnimationFrame(animate);
        }
    }

    // ===== SMOOTH SCROLL POUR LES LIENS INTERNES =====
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // ===== GESTION DU THÈME SOMBRE (OPTIONNEL) =====
    const themeToggle = document.querySelector('.theme-toggle');
    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            document.body.classList.toggle('dark-theme');
            localStorage.setItem('darkTheme', document.body.classList.contains('dark-theme'));
        });

        // Charger le thème sauvegardé
        if (localStorage.getItem('darkTheme') === 'true') {
            document.body.classList.add('dark-theme');
        }
    }
});

// ===== CSS POUR LES EFFETS JAVASCRIPT =====
const style = document.createElement('style');
style.textContent = `
    .ripple {
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.6);
        transform: scale(0);
        animation: ripple-animation 0.6s linear;
        pointer-events: none;
    }

    @keyframes ripple-animation {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }

    .reading-progress {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: rgba(0, 0, 0, 0.1);
        z-index: 9999;
    }

    .reading-progress-fill {
        height: 100%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        width: 0%;
        transition: width 0.3s ease;
    }

    .particle {
        animation: particle-fade 1s ease-out forwards;
    }

    @keyframes particle-fade {
        to {
            transform: scale(0);
            opacity: 0;
        }
    }

    .dark-theme {
        --bg-light: #1a1a1a;
        --white: #2d2d2d;
        --text-dark: #ffffff;
        --text-light: #cccccc;
    }
`;
document.head.appendChild(style);