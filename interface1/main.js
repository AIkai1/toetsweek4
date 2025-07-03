document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuButton = document.querySelector('.mobile-menu-button');
    const mobileMenu = document.querySelector('.mobile-menu');
    
    if (mobileMenuButton && mobileMenu) {
        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    }

    const slides = document.querySelectorAll('.carousel-slide');
    const carouselContainer = document.querySelector('.carousel-container');
    const prevBtn = document.querySelector('.carousel-prev');
    const nextBtn = document.querySelector('.carousel-next');
    
    if (slides.length > 0 && carouselContainer) {
        let currentSlide = 0;
        const totalSlides = slides.length;

        function updateCarousel() {
            const translateX = -currentSlide * 100;
            carouselContainer.style.transform = `translateX(${translateX}%)`;
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                currentSlide = (currentSlide + 1) % totalSlides;
                updateCarousel();
            });
        }

        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
                updateCarousel();
            });
        }

        setInterval(() => {
            currentSlide = (currentSlide + 1) % totalSlides;
            updateCarousel();
        }, 5000);
    }

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

    const form = document.getElementById('contactForm');
    if (form) {
        const successMessage = document.getElementById('successMessage');
        const errorMessage = document.getElementById('errorMessage');

        function validateRequired(field) {
            const value = field.value.trim();
            if (value === '') {
                showError(field, 'Dit veld is verplicht');
                return false;
            }
            clearError(field);
            return true;
        }

        function validateEmail(field) {
            const value = field.value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (value === '') {
                showError(field, 'E-mailadres is verplicht');
                return false;
            }
            if (!emailRegex.test(value)) {
                showError(field, 'Voer een geldig e-mailadres in');
                return false;
            }
            clearError(field);
            return true;
        }

        function validatePhone(field) {
            const value = field.value.trim();
            
            if (value === '') {
                showError(field, 'Telefoonnummer is verplicht');
                return false;
            }
            
            const cleanPhone = value.replace(/[\s\-\(\)]/g, '');
            const digitCount = cleanPhone.replace(/[^\d]/g, '').length;
            
            if (digitCount < 10) {
                showError(field, 'Voer een geldig telefoonnummer in (minimaal 10 cijfers)');
                return false;
            }
            
            clearError(field);
            return true;
        }

        function validatePostcode(field) {
            const value = field.value.trim();
            if (value === '') {
                clearError(field);
                return true;
            }
            const postcodeRegex = /^[1-9][0-9]{3}[A-Z]{2}$/;
            if (!postcodeRegex.test(value.replace(/\s/g, ''))) {
                showError(field, 'Voer een geldige postcode in (bijv. 1234AB)');
                return false;
            }
            clearError(field);
            return true;
        }

        function validateCheckbox(field) {
            const errorDiv = document.getElementById('privacy-error');
            if (!field.checked) {
                if (errorDiv) {
                    errorDiv.textContent = 'Je moet akkoord gaan met de privacyverklaring';
                    errorDiv.classList.remove('hidden');
                }
                return false;
            }
            if (errorDiv) {
                errorDiv.classList.add('hidden');
            }
            return true;
        }

        function showError(field, message) {
            const errorDiv = field.parentNode.querySelector('.error-message');
            if (errorDiv) {
                errorDiv.textContent = message;
                errorDiv.classList.remove('hidden');
                field.classList.add('border-red-500');
            }
        }

        function clearError(field) {
            const errorDiv = field.parentNode.querySelector('.error-message');
            if (errorDiv) {
                errorDiv.classList.add('hidden');
                field.classList.remove('border-red-500');
            }
        }

        function showMessage(messageDiv) {
            if (messageDiv) {
                messageDiv.classList.remove('translate-x-full');
                setTimeout(() => {
                    messageDiv.classList.add('translate-x-full');
                }, 3000);
            }
        }
        const voornaamField = document.getElementById('voornaam');
        const achternaamField = document.getElementById('achternaam');
        const emailField = document.getElementById('email');
        const telefoonField = document.getElementById('telefoon');
        const postcodeField = document.getElementById('postcode');
        const vraagField = document.getElementById('vraag');
        const privacyField = document.getElementById('privacy');

        if (voornaamField) {
            voornaamField.addEventListener('blur', function() {
                validateRequired(this);
            });
        }


        if (achternaamField) {
            achternaamField.addEventListener('blur', function() {
                validateRequired(this);
            });
        }

        if (emailField) {
            emailField.addEventListener('blur', function() {
                validateEmail(this);
            });
        }

        if (telefoonField) {
            telefoonField.addEventListener('blur', function() {
                validatePhone(this);
            });
        }

        if (postcodeField) {
            postcodeField.addEventListener('blur', function() {
                validatePostcode(this);
            });
        }

        if (vraagField) {
            vraagField.addEventListener('blur', function() {
                validateRequired(this);
            });
        }

        if (privacyField) {
            privacyField.addEventListener('change', function() {
                validateCheckbox(this);
            });
        }

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            let isValid = true;

            if (achternaamField) {
                if (!validateRequired(achternaamField)) {
                    isValid = false;
                }
            }
            if (emailField) {
                if (!validateEmail(emailField)) {
                    isValid = false;
                }
            }
            if (telefoonField) {
                if (!validatePhone(telefoonField)) {
                    isValid = false;
                }
            }
            if (postcodeField) {
                if (!validatePostcode(postcodeField)) {
                    isValid = false;
                }
            }
            if (vraagField) {
                if (!validateRequired(vraagField)) {
                    isValid = false;
                }
            }
            if (privacyField) {
                if (!validateCheckbox(privacyField)) {
                    isValid = false;
                }
            }

            if (isValid) {
                setTimeout(() => {
                    showMessage(successMessage);
                    form.reset();
                    const errorMessages = form.querySelectorAll('.error-message');
                    errorMessages.forEach(msg => msg.classList.add('hidden'));
                    const errorFields = form.querySelectorAll('.border-red-500');
                    errorFields.forEach(field => field.classList.remove('border-red-500'));
                    const privacyError = document.getElementById('privacy-error');
                    if (privacyError) privacyError.classList.add('hidden');
                }, 500);
            } else {
                showMessage(errorMessage);
            }
        });
    }
});
