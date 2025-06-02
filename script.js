// ===== DOM Ready =====
document.addEventListener('DOMContentLoaded', function() {
  // Initialize all components
  initDarkMode();
  initScrollReveal();
  initFormValidations();
  initInteractiveElements();
  initAnimations();
  initServiceWorker();
  initAnalytics();
});

// ===== Dark Mode Toggle =====
function initDarkMode() {
  const btnToggle = document.querySelector('.btn-toggle');
  const prefersDarkScheme = window.matchMedia('(prefers-color-scheme: dark)');
  
  // Set initial mode based on preference
  if (prefersDarkScheme.matches) {
    document.body.classList.add('dark-mode');
  }
  
  // Toggle dark mode
  btnToggle.addEventListener('click', function() {
    document.body.classList.toggle('dark-mode');
    
    // Save preference to localStorage
    const isDark = document.body.classList.contains('dark-mode');
    localStorage.setItem('darkMode', isDark);
    
    // Animate the toggle button
    this.classList.add('animate');
    setTimeout(() => this.classList.remove('animate'), 500);
  });
  
  // Check for saved preference
  if (localStorage.getItem('darkMode') === 'true') {
    document.body.classList.add('dark-mode');
  }
}

// ===== Scroll Reveal Animations =====
function initScrollReveal() {
  const revealElements = document.querySelectorAll('.reveal');
  
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('active');
        
        // Add floating animation to specific elements
        if (entry.target.dataset.animate === 'float') {
          entry.target.classList.add('floating');
        }
      }
    });
  }, {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
  });
  
  revealElements.forEach(el => observer.observe(el));
}

// ===== Form Validations =====
function initFormValidations() {
  const forms = document.querySelectorAll('form');
  
  forms.forEach(form => {
    form.addEventListener('submit', function(e) {
      let isValid = true;
      
      // Validate required fields
      const requiredFields = this.querySelectorAll('[required]');
      requiredFields.forEach(field => {
        if (!field.value.trim()) {
          field.classList.add('error');
          isValid = false;
          
          // Create error message
          if (!field.nextElementSibling?.classList.contains('error-message')) {
            const errorMsg = document.createElement('span');
            errorMsg.className = 'error-message';
            errorMsg.textContent = 'This field is required';
            errorMsg.style.color = '#e74c3c';
            errorMsg.style.fontSize = '0.8rem';
            errorMsg.style.marginTop = '0.25rem';
            errorMsg.style.display = 'block';
            field.after(errorMsg);
          }
        } else {
          field.classList.remove('error');
          if (field.nextElementSibling?.classList.contains('error-message')) {
            field.nextElementSibling.remove();
          }
        }
      });
      
      if (!isValid) {
        e.preventDefault();
        
        // Add shake animation to form
        this.classList.add('shake');
        setTimeout(() => this.classList.remove('shake'), 500);
      }
    });
  });
}

// ===== Interactive Elements =====
function initInteractiveElements() {
  // Add ripple effect to buttons
  const buttons = document.querySelectorAll('button, .button');
  buttons.forEach(button => {
    button.addEventListener('click', function(e) {
      // Create ripple element
      const ripple = document.createElement('span');
      ripple.className = 'ripple';
      
      // Position ripple
      const rect = this.getBoundingClientRect();
      const size = Math.max(rect.width, rect.height);
      const x = e.clientX - rect.left - size/2;
      const y = e.clientY - rect.top - size/2;
      
      // Style ripple
      ripple.style.width = ripple.style.height = `${size}px`;
      ripple.style.left = `${x}px`;
      ripple.style.top = `${y}px`;
      ripple.style.backgroundColor = 'rgba(255, 255, 255, 0.4)';
      ripple.style.position = 'absolute';
      ripple.style.borderRadius = '50%';
      ripple.style.transform = 'scale(0)';
      ripple.style.animation = 'ripple 600ms linear';
      
      // Add and remove ripple
      this.appendChild(ripple);
      setTimeout(() => ripple.remove(), 600);
    });
  });
  
  // Add hover effects to cards
  const cards = document.querySelectorAll('.product-card, .stats-card');
  cards.forEach(card => {
    card.addEventListener('mouseenter', () => {
      card.style.transform = 'translateY(-8px)';
      card.style.boxShadow = '0 15px 30px rgba(0, 0, 0, 0.15)';
    });
    
    card.addEventListener('mouseleave', () => {
      card.style.transform = 'translateY(0)';
      card.style.boxShadow = '0 5px 15px rgba(0, 0, 0, 0.1)';
    });
  });
}

// ===== Animations =====
function initAnimations() {
  // Add CSS for ripple animation
  const style = document.createElement('style');
  style.textContent = `
    @keyframes ripple {
      to {
        transform: scale(4);
        opacity: 0;
      }
    }
    @keyframes shake {
      0%, 100% { transform: translateX(0); }
      20%, 60% { transform: translateX(-5px); }
      40%, 80% { transform: translateX(5px); }
    }
    .shake {
      animation: shake 0.5s ease-in-out;
    }
  `;
  document.head.appendChild(style);
  
  // Typing animation for hero text
  const heroText = document.getElementById('typing-text');
  if (heroText) {
    const text = heroText.dataset.text || "Farmers Logistics System";
    let i = 0;
    
    function typeWriter() {
      if (i < text.length) {
        heroText.innerHTML += text.charAt(i);
        i++;
        setTimeout(typeWriter, 100);
      } else {
        heroText.classList.add('blink');
      }
    }
    
    typeWriter();
  }
}

// ===== Service Worker for PWA =====
function initServiceWorker() {
  if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
      navigator.serviceWorker.register('/sw.js').then(registration => {
        console.log('ServiceWorker registration successful');
      }).catch(err => {
        console.log('ServiceWorker registration failed: ', err);
      });
    });
  }
}

// ===== Analytics =====
function initAnalytics() {
  // Track page views
  window.dataLayer = window.dataLayer || [];
  function gtag() { dataLayer.push(arguments); }
  gtag('js', new Date());
  gtag('config', 'GA_MEASUREMENT_ID');
  
  // Track button clicks
  document.querySelectorAll('button, a').forEach(btn => {
    btn.addEventListener('click', function() {
      gtag('event', 'click', {
        'event_category': 'engagement',
        'event_label': this.textContent.trim()
      });
    });
  });
}

// ===== Utility Functions =====
function debounce(func, wait = 20, immediate = true) {
  let timeout;
  return function() {
    const context = this, args = arguments;
    const later = function() {
      timeout = null;
      if (!immediate) func.apply(context, args);
    };
    const callNow = immediate && !timeout;
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
    if (callNow) func.apply(context, args);
  };
}

function throttle(func, limit = 300) {
  let lastFunc;
  let lastRan;
  return function() {
    const context = this;
    const args = arguments;
    if (!lastRan) {
      func.apply(context, args);
      lastRan = Date.now();
    } else {
      clearTimeout(lastFunc);
      lastFunc = setTimeout(function() {
        if ((Date.now() - lastRan) >= limit) {
          func.apply(context, args);
          lastRan = Date.now();
        }
      }, limit - (Date.now() - lastRan));
    }
  };
}

// ===== Real-time Updates =====
function initRealTimeUpdates() {
  // Connect to WebSocket for live updates
  const socket = new WebSocket('wss://your-websocket-endpoint');
  
  socket.onmessage = function(event) {
    const data = JSON.parse(event.data);
    
    // Update order status in real-time
    if (data.type === 'orderUpdate') {
      const orderRow = document.querySelector(`tr[data-order-id="${data.orderId}"]`);
      if (orderRow) {
        orderRow.querySelector('.order-status').textContent = data.status;
        orderRow.querySelector('.order-status').className = `order-status status-${data.status.toLowerCase().replace(' ', '-')}`;
        
        // Add notification effect
        orderRow.classList.add('updated');
        setTimeout(() => orderRow.classList.remove('updated'), 2000);
      }
    }
  };
  
  // Reconnect if connection is lost
  socket.onclose = function() {
    setTimeout(initRealTimeUpdates, 5000);
  };
}

// ===== Export functions for module usage =====
if (typeof module !== 'undefined' && module.exports) {
  module.exports = {
    initDarkMode,
    initScrollReveal,
    initFormValidations,
    initInteractiveElements,
    initAnimations,
    debounce,
    throttle
  };
}