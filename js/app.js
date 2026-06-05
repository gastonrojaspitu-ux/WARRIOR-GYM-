// ========================= 
// INICIALIZAR AOS (Animate On Scroll)
// ========================= 
document.addEventListener('DOMContentLoaded', function() {
  if (typeof AOS !== 'undefined') {
    AOS.init({
      duration: 1000,
      once: false,
      mirror: true,
      offset: 200,
      easing: 'ease-in-out-cubic',
      delay: 0,
    });
  }
});

// ========================= 
// NAVBAR SCROLL EFFECT
// ========================= 
const navbar = document.querySelector('.navbar');
const navbarBrand = document.querySelector('.navbar-brand');

if (navbar) {
  window.addEventListener('scroll', function() {
    if (window.scrollY > 100) {
      navbar.classList.add('scrolled');
      if (navbarBrand) navbarBrand.style.fontSize = '1.4rem';
    } else {
      navbar.classList.remove('scrolled');
      if (navbarBrand) navbarBrand.style.fontSize = '1.8rem';
    }
  }, { passive: true });
}

// ========================= 
// SMOOTH SCROLL PARA LINKS
// ========================= 
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
  anchor.addEventListener('click', function(e) {
    const href = this.getAttribute('href');
    if (href !== '#') {
      e.preventDefault();
      const target = document.querySelector(href);
      if (target) {
        target.scrollIntoView({
          behavior: 'smooth',
          block: 'start'
        });
      }
    }
  });
});

// ========================= 
// CERRAR NAVBAR AL HACER CLICK EN LINK
// ========================= 
const navbarCollapse = document.querySelector('.navbar-collapse');
const navLinks = document.querySelectorAll('.nav-link:not(.btn-login)');

navLinks.forEach(link => {
  link.addEventListener('click', () => {
    if (navbarCollapse && navbarCollapse.classList.contains('show')) {
      navbarCollapse.classList.remove('show');
    }
  });
});

// ========================= 
// EFECTOS HOVER EN CARDS
// ========================= 
document.querySelectorAll('.benefit-card, .goal-card, .membership-card, .event-card, .testimonial-card').forEach(card => {
  card.addEventListener('mouseenter', function() {
    this.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
  });
});

// ========================= 
// CONTADOR PARA ESTADISTICAS
// ========================= 
let counted = false;

function countUp(element, target, duration = 2000) {
  let start = 0;
  const increment = target / (duration / 16);
  
  const counter = setInterval(() => {
    start += increment;
    if (start >= target) {
      element.textContent = target + '+';
      clearInterval(counter);
    } else {
      element.textContent = Math.floor(start) + '+';
    }
  }, 16);
}

const observerOptions = {
  threshold: 0.5,
  rootMargin: '0px'
};

const observer = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting && !counted) {
      const statBoxes = document.querySelectorAll('.stat-box h2');
      statBoxes.forEach((box, index) => {
        setTimeout(() => {
          const target = parseInt(box.textContent);
          countUp(box, target);
        }, index * 100);
      });
      counted = true;
      observer.unobserve(entry.target);
    }
  });
}, observerOptions);

const statsSection = document.querySelector('.stats-section');
if (statsSection) {
  observer.observe(statsSection);
}

// ========================= 
// EFECTOS PARALLAX SUAVE
// ========================= 
window.addEventListener('scroll', () => {
  const heroSlides = document.querySelectorAll('.hero-slide');
  heroSlides.forEach(slide => {
    const scrollPosition = window.scrollY;
    slide.style.backgroundPosition = `center calc(50% + ${scrollPosition * 0.5}px)`;
  });
}, { passive: true });

// ========================= 
// RETRASAR ANIMACIONES AOS AL CARGAR
// ========================= 
window.addEventListener('load', () => {
  if (typeof AOS !== 'undefined') {
    AOS.refresh();
  }
});

// ========================= 
// VALIDAR FORMULARIOS
// ========================= 
const forms = document.querySelectorAll('form');
forms.forEach(form => {
  form.addEventListener('submit', function(e) {
    if (!form.checkValidity()) {
      e.preventDefault();
      e.stopPropagation();
    }
    form.classList.add('was-validated');
  });
});

// ========================= 
// EFECTO RIPPLE EN BOTONES
// ========================= 
document.querySelectorAll('.btn').forEach(button => {
  button.addEventListener('click', function(e) {
    // Solo crear ripple en clicks del mouse, no en touch
    if (e.button !== 0) return;
    
    const ripple = document.createElement('span');
    const rect = this.getBoundingClientRect();
    const size = Math.max(rect.width, rect.height);
    const x = e.clientX - rect.left - size / 2;
    const y = e.clientY - rect.top - size / 2;
    
    ripple.style.width = ripple.style.height = size + 'px';
    ripple.style.left = x + 'px';
    ripple.style.top = y + 'px';
    ripple.classList.add('ripple');
    ripple.style.position = 'absolute';
    ripple.style.borderRadius = '50%';
    ripple.style.background = 'rgba(255, 255, 255, 0.6)';
    ripple.style.pointerEvents = 'none';
    
    this.style.position = 'relative';
    this.style.overflow = 'hidden';
    this.appendChild(ripple);
    
    setTimeout(() => ripple.remove(), 600);
  });
});

// ========================= 
// LAZY LOADING DE IMÁGENES
// ========================= 
if ('IntersectionObserver' in window) {
  const imageObserver = new IntersectionObserver((entries, observer) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const img = entry.target;
        if (img.dataset.src) {
          img.src = img.dataset.src;
          img.classList.add('loaded');
        }
        observer.unobserve(img);
      }
    });
  });
  
  document.querySelectorAll('img[data-src]').forEach(img => {
    imageObserver.observe(img);
  });
}

// ========================= 
// SCROLL TO TOP BUTTON
// ========================= 
const scrollToTopBtn = document.querySelector('.scroll-to-top');
if (scrollToTopBtn) {
  window.addEventListener('scroll', () => {
    if (window.scrollY > 300) {
      scrollToTopBtn.classList.add('show');
    } else {
      scrollToTopBtn.classList.remove('show');
    }
  }, { passive: true });

  scrollToTopBtn.addEventListener('click', () => {
    window.scrollTo({
      top: 0,
      behavior: 'smooth'
    });
  });
}

// ========================= 
// PERFORMANCE - Defer non-critical scripts
// ========================= 
console.log('✅ Warrior Gym - Scripts cargados correctamente');
console.log('📊 Performance: DOMContentLoaded completado');
