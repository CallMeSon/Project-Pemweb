// ===== NAVBAR SCROLL EFFECT =====
let lastScrollTop = 0;
const navbar = document.getElementById('navbar');

window.addEventListener('scroll', function() {
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    
    // Add 'scrolled' class when user scrolls down
    if (scrollTop > 50) {
        navbar.classList.add('scrolled');
    } else {
        navbar.classList.remove('scrolled');
    }
    
    lastScrollTop = scrollTop;
});

// ===== SMOOTH SCROLL FOR ANCHOR LINKS =====
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            const navbarHeight = navbar.offsetHeight;
            const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - navbarHeight - 20;
            
            window.scrollTo({
                top: targetPosition,
                behavior: 'smooth'
            });
        }
    });
});

// ===== QUANTITY CONTROL FUNCTIONS =====
function increaseQty(button) {
    const input = button.parentElement.querySelector('.qty-input');
    let currentValue = parseInt(input.value) || 1;
    input.value = currentValue + 1;
    
    // Add animation
    input.style.transform = 'scale(1.2)';
    setTimeout(() => {
        input.style.transform = 'scale(1)';
    }, 200);
}

function decreaseQty(button) {
    const input = button.parentElement.querySelector('.qty-input');
    let currentValue = parseInt(input.value) || 1;
    
    if (currentValue > 1) {
        input.value = currentValue - 1;
        
        // Add animation
        input.style.transform = 'scale(1.2)';
        setTimeout(() => {
            input.style.transform = 'scale(1)';
        }, 200);
    }
}

// ===== PRODUCT CARD ANIMATION ON SCROLL =====
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver(function(entries) {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '0';
            entry.target.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                entry.target.style.transition = 'all 0.6s ease';
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }, 100);
            
            observer.unobserve(entry.target);
        }
    });
}, observerOptions);

// Observe all product cards
document.querySelectorAll('.product-card').forEach(card => {
    observer.observe(card);
});

// ===== ADD TO CART ANIMATION =====
document.querySelectorAll('.add-to-cart-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        const button = this.querySelector('.btn-add-cart');
        const originalText = button.innerHTML;
        
        // Change button text temporarily
        button.innerHTML = '<span>✓</span> Ditambahkan!';
        button.style.background = '#28a745';
        
        // Reset after 2 seconds
        setTimeout(() => {
            button.innerHTML = originalText;
            button.style.background = '';
        }, 2000);
    });
});

// ===== CATEGORY FILTER ANIMATION =====
document.querySelectorAll('.btn-filter').forEach(filter => {
    filter.addEventListener('click', function(e) {
        // Add loading animation
        const productGrid = document.querySelector('.product-grid');
        productGrid.style.opacity = '0.5';
        productGrid.style.transform = 'scale(0.98)';
        
        // Note: The actual page reload will happen, but this gives visual feedback
    });
});

// ===== HERO PARALLAX EFFECT =====
const heroContent = document.querySelector('.hero-content');

window.addEventListener('scroll', function() {
    const scrolled = window.pageYOffset;
    const parallaxSpeed = 0.5;
    
    if (heroContent && scrolled < 600) {
        heroContent.style.transform = `translateY(${scrolled * parallaxSpeed}px)`;
        heroContent.style.opacity = 1 - (scrolled / 600);
    }
});

// ===== LOADING ANIMATION =====
window.addEventListener('load', function() {
    document.body.style.opacity = '0';
    setTimeout(() => {
        document.body.style.transition = 'opacity 0.5s ease';
        document.body.style.opacity = '1';
    }, 100);
});

// ===== PREVENT DOUBLE CLICK ON SUBMIT =====
document.querySelectorAll('.add-to-cart-form').forEach(form => {
    form.addEventListener('submit', function() {
        const button = this.querySelector('.btn-add-cart');
        button.disabled = true;
        
        setTimeout(() => {
            button.disabled = false;
        }, 2000);
    });
});

// ===== MOBILE MENU TOGGLE (if needed in future) =====
function toggleMobileMenu() {
    const navbarMenu = document.querySelector('.navbar-menu');
    navbarMenu.classList.toggle('active');
}

// ===== CONSOLE GREETING =====
console.log('%c☕ Selamat datang di Kedai Kopi!', 'font-size: 20px; color: #6B4423; font-weight: bold;');
console.log('%cNikmati pengalaman berbelanja kopi terbaik!', 'font-size: 14px; color: #8B5A3C;');


// ===== MODAL QUANTITY CONTROL =====
let currentForm = null;

// Buka modal
function openQtyModal(button, productName) {
    currentForm = button.closest('.add-to-cart-form');
    document.getElementById('modalProductName').textContent = productName;
    document.getElementById('modalQtyInput').value = 1;
    
    const modal = document.getElementById('qtyModal');
    modal.style.display = 'block';
    modal.classList.add('show');
    
    console.log('Modal dibuka untuk:', productName);
}

// Tutup modal
function closeQtyModal() {
    const modal = document.getElementById('qtyModal');
    modal.style.display = 'none';
    modal.classList.remove('show');
    currentForm = null;
}

// Tambah quantity di modal
function increaseQtyModal() {
    const input = document.getElementById('modalQtyInput');
    let currentValue = parseInt(input.value) || 1;
    input.value = currentValue + 1;
}

// Kurangi quantity di modal
function decreaseQtyModal() {
    const input = document.getElementById('modalQtyInput');
    let currentValue = parseInt(input.value) || 1;
    if (currentValue > 1) {
        input.value = currentValue - 1;
    }
}

// Konfirmasi dan submit form
function confirmAddCart() {
    if (currentForm) {
        const qty = document.getElementById('modalQtyInput').value;
        currentForm.querySelector('.qty-hidden').value = qty;
        currentForm.submit();
    }
    closeQtyModal();
}

// Tutup modal jika klik di luar
document.addEventListener('click', function(event) {
    const modal = document.getElementById('qtyModal');
    if (modal && event.target === modal) {
        closeQtyModal();
    }
});

// ===== LOGIN ERROR HANDLER =====
function showLoginError() {
    const errorModal = document.getElementById('errorModal');
    document.getElementById('errorMessage').textContent = 
        'Anda harus login terlebih dahulu untuk melakukan pemesanan. Silakan login ke akun Anda.';
    errorModal.style.display = 'block';
    errorModal.classList.add('show');
    console.log('User belum login, tampilkan error modal');
}

function closeErrorModal() {
    const errorModal = document.getElementById('errorModal');
    errorModal.style.display = 'none';
    errorModal.classList.remove('show');
}

function redirectToLogin() {
    window.location.href = 'auth.php';
}

// Tutup error modal jika klik di luar
document.addEventListener('click', function(event) {
    const errorModal = document.getElementById('errorModal');
    if (errorModal && event.target === errorModal) {
        closeErrorModal();
    }
});