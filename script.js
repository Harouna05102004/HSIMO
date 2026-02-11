// ===========================
// CONFIGURATION
// ===========================

// Configuration EmailJS (vous devrez créer un compte sur emailjs.com)
const EMAILJS_CONFIG = {
    serviceID: 'service_shlir7z',  // À remplacer
    templateID: 'VOTRE_TEMPLATE_ID', // À remplacer
    publicKey: 'VOTRE_PUBLIC_KEY'    // À remplacer
};

// ===========================
// STOCKAGE DES ANNONCES
// ===========================

class PropertyManager {
    constructor() {
        this.properties = this.loadProperties();
    }

    loadProperties() {
        const saved = localStorage.getItem('properties');
        return saved ? JSON.parse(saved) : this.getDefaultProperties();
    }

    getDefaultProperties() {
        return {
            acheter: [
                {
                    id: 'buy1',
                    title: 'Villa d\'Exception',
                    location: 'Cap d\'Antibes',
                    price: '4 200 000',
                    rooms: 5,
                    bathrooms: 4,
                    surface: 320,
                    images: ['YOUR_BUY_PROPERTY_1_URL'],
                    description: 'Magnifique villa d\'exception avec vue panoramique sur la mer.',
                    type: 'acheter'
                },
                {
                    id: 'buy2',
                    title: 'Propriété Vue Mer',
                    location: 'Eze',
                    price: '3 850 000',
                    rooms: 4,
                    bathrooms: 3,
                    surface: 280,
                    images: ['YOUR_BUY_PROPERTY_2_URL'],
                    description: 'Superbe propriété avec vue mer exceptionnelle.',
                    type: 'acheter'
                }
            ],
            louer: [
                {
                    id: 'rent1',
                    title: 'Villa Contemporaine',
                    location: 'Saint-Tropez',
                    price: '8 500',
                    monthly: true,
                    rooms: 5,
                    bathrooms: 3,
                    surface: 250,
                    images: ['YOUR_RENTAL_PROPERTY_1_URL'],
                    description: 'Villa contemporaine avec piscine et jardin paysager.',
                    type: 'louer'
                },
                {
                    id: 'rent2',
                    title: 'Maison de Charme',
                    location: 'Antibes',
                    price: '6 800',
                    monthly: true,
                    rooms: 4,
                    bathrooms: 2,
                    surface: 200,
                    images: ['YOUR_RENTAL_PROPERTY_2_URL'],
                    description: 'Charmante maison proche du centre-ville.',
                    type: 'louer'
                }
            ]
        };
    }

    saveProperties() {
        localStorage.setItem('properties', JSON.stringify(this.properties));
    }

    addProperty(property, category) {
        property.id = 'prop_' + Date.now();
        if (!this.properties[category]) {
            this.properties[category] = [];
        }
        this.properties[category].push(property);
        this.saveProperties();
        return property;
    }

    getProperties(category) {
        return this.properties[category] || [];
    }

    getPropertyById(id) {
        for (let category in this.properties) {
            const found = this.properties[category].find(p => p.id === id);
            if (found) return found;
        }
        return null;
    }
}

const propertyManager = new PropertyManager();

// ===========================
// MODAL D'ANNONCES
// ===========================

class PropertyModal {
    constructor() {
        this.createModal();
        this.currentImageIndex = 0;
        this.images = [];
    }

    createModal() {
        const modalHTML = `
            <div id="propertyModal" class="modal">
                <div class="modal-content">
                    <span class="modal-close">&times;</span>
                    
                    <div class="modal-body">
                        <div class="modal-gallery">
                            <button class="gallery-nav prev">‹</button>
                            <div class="gallery-images">
                                <img id="modalImage" src="" alt="">
                            </div>
                            <button class="gallery-nav next">›</button>
                            <div class="gallery-indicators"></div>
                        </div>
                        
                        <div class="modal-info">
                            <h2 id="modalTitle"></h2>
                            <p id="modalLocation" class="modal-location"></p>
                            <p id="modalPrice" class="modal-price"></p>
                            
                            <div id="modalFeatures" class="modal-features"></div>
                            
                            <div class="modal-description">
                                <h3>Description</h3>
                                <p id="modalDescription"></p>
                            </div>
                            
                            <button class="btn-contact-modal">Contacter l'agent</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHTML);
        this.modal = document.getElementById('propertyModal');
        this.setupEventListeners();
    }

    setupEventListeners() {
        // Fermer le modal
        const closeBtn = this.modal.querySelector('.modal-close');
        closeBtn.addEventListener('click', () => this.close());

        // Fermer en cliquant en dehors
        window.addEventListener('click', (e) => {
            if (e.target === this.modal) {
                this.close();
            }
        });

        // Navigation des images
        const prevBtn = this.modal.querySelector('.gallery-nav.prev');
        const nextBtn = this.modal.querySelector('.gallery-nav.next');
        
        prevBtn.addEventListener('click', () => this.previousImage());
        nextBtn.addEventListener('click', () => this.nextImage());

        // Fermer avec Échap
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.modal.classList.contains('show')) {
                this.close();
            }
            if (e.key === 'ArrowLeft' && this.modal.classList.contains('show')) {
                this.previousImage();
            }
            if (e.key === 'ArrowRight' && this.modal.classList.contains('show')) {
                this.nextImage();
            }
        });

        // Bouton contact
        const contactBtn = this.modal.querySelector('.btn-contact-modal');
        contactBtn.addEventListener('click', () => {
            alert('Fonctionnalité de contact à implémenter');
        });
    }

    open(propertyId) {
        const property = propertyManager.getPropertyById(propertyId);
        if (!property) return;

        // Remplir les informations
        this.modal.querySelector('#modalTitle').textContent = property.title;
        this.modal.querySelector('#modalLocation').textContent = property.location;
        
        const priceText = property.monthly 
            ? `${property.price} € / mois`
            : `${property.price} €`;
        this.modal.querySelector('#modalPrice').textContent = priceText;
        
        this.modal.querySelector('#modalDescription').textContent = property.description;

        // Afficher les caractéristiques
        const featuresHTML = `
            <div class="feature-item">
                <span class="feature-icon">🛏️</span>
                <span class="feature-text">${property.rooms} chambres</span>
            </div>
            <div class="feature-item">
                <span class="feature-icon">🚿</span>
                <span class="feature-text">${property.bathrooms} salles de bain</span>
            </div>
            <div class="feature-item">
                <span class="feature-icon">📐</span>
                <span class="feature-text">${property.surface} m²</span>
            </div>
        `;
        this.modal.querySelector('#modalFeatures').innerHTML = featuresHTML;

        // Gérer les images
        this.images = property.images || [];
        this.currentImageIndex = 0;
        this.updateGallery();

        // Afficher le modal
        this.modal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    close() {
        this.modal.classList.remove('show');
        document.body.style.overflow = '';
    }

    updateGallery() {
        if (this.images.length === 0) return;

        const img = this.modal.querySelector('#modalImage');
        img.src = this.images[this.currentImageIndex];

        // Mettre à jour les indicateurs
        const indicators = this.modal.querySelector('.gallery-indicators');
        if (this.images.length > 1) {
            indicators.innerHTML = this.images.map((_, index) => 
                `<span class="indicator ${index === this.currentImageIndex ? 'active' : ''}"></span>`
            ).join('');
        } else {
            indicators.innerHTML = '';
        }

        // Gérer la visibilité des boutons de navigation
        const prevBtn = this.modal.querySelector('.gallery-nav.prev');
        const nextBtn = this.modal.querySelector('.gallery-nav.next');
        
        if (this.images.length <= 1) {
            prevBtn.style.display = 'none';
            nextBtn.style.display = 'none';
        } else {
            prevBtn.style.display = 'flex';
            nextBtn.style.display = 'flex';
        }
    }

    nextImage() {
        if (this.images.length <= 1) return;
        this.currentImageIndex = (this.currentImageIndex + 1) % this.images.length;
        this.updateGallery();
    }

    previousImage() {
        if (this.images.length <= 1) return;
        this.currentImageIndex = (this.currentImageIndex - 1 + this.images.length) % this.images.length;
        this.updateGallery();
    }
}

const propertyModal = new PropertyModal();

// ===========================
// AFFICHAGE DES ANNONCES
// ===========================

function renderProperties(category, containerId) {
    const container = document.getElementById(containerId);
    if (!container) return;

    const properties = propertyManager.getProperties(category);
    
    container.innerHTML = properties.map(property => `
        <div class="property-card-large" data-property-id="${property.id}">
            <div class="property-image-large">
                <img src="${property.images[0]}" alt="${property.title}">
                <span class="property-label">${property.title}</span>
            </div>
            <div class="property-details">
                <h3>${property.title}</h3>
                <p class="location">${property.location}</p>
                <p class="${property.monthly ? 'rental-price' : 'sale-price'}">
                    ${property.price} €${property.monthly ? ' / mois' : ''}
                </p>
            </div>
        </div>
    `).join('');

    // Ajouter les événements de clic
    container.querySelectorAll('.property-card-large').forEach(card => {
        card.addEventListener('click', () => {
            const propertyId = card.dataset.propertyId;
            propertyModal.open(propertyId);
        });
    });
}

// ===========================
// FORMULAIRE DE VENTE
// ===========================

function initSellForm() {
    const form = document.getElementById('sellForm');
    if (!form) return;

    form.addEventListener('submit', (e) => {
        e.preventDefault();

        const formData = new FormData(form);
        const images = [];
        
        // Gérer les images uploadées
        const fileInput = document.getElementById('photos');
        if (fileInput && fileInput.files.length > 0) {
            // En production, il faudrait uploader les images vers un serveur
            // Pour la démo, on utilise une URL placeholder
            for (let file of fileInput.files) {
                images.push('https://via.placeholder.com/800x600?text=' + encodeURIComponent(file.name));
            }
        } else {
            images.push('https://via.placeholder.com/800x600?text=Nouvelle+Propriété');
        }

        const property = {
            title: formData.get('title') || 'Nouvelle Propriété',
            location: formData.get('city'),
            price: formData.get('price') || '0',
            rooms: parseInt(formData.get('rooms')) || 0,
            bathrooms: parseInt(formData.get('bathrooms')) || 0,
            surface: parseInt(formData.get('surface')) || 0,
            description: formData.get('description'),
            images: images,
            type: 'acheter' // Par défaut en vente
        };

        propertyManager.addProperty(property, 'acheter');
        
        // Redirection vers la page de confirmation
        window.location.href = 'confirmation.html';
    });
}

// ===========================
// FORMULAIRE D'ESTIMATION
// ===========================

function initEstimationForm() {
    const form = document.getElementById('estimationForm');
    if (!form) return;

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const formData = new FormData(form);
        const data = {
            address: formData.get('address'),
            city: formData.get('city'),
            postal: formData.get('postal'),
            type: formData.get('type'),
            rooms: formData.get('rooms'),
            surface: formData.get('surface'),
            year: formData.get('year'),
            condition: formData.get('condition')
        };

        // Afficher un loader
        const submitBtn = form.querySelector('.btn-submit');
        const originalText = submitBtn.textContent;
        submitBtn.textContent = 'Envoi en cours...';
        submitBtn.disabled = true;

        try {
            await sendEstimationEmail(data);
            // Redirection vers la page de résultat
            window.location.href = 'estimation-result.html';
        } catch (error) {
            alert('Erreur lors de l\'envoi de l\'estimation. Veuillez réessayer.');
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
        }
    });
}

async function sendEstimationEmail(data) {
    // Option 1 : Utiliser EmailJS (gratuit et simple)
    if (typeof emailjs !== 'undefined') {
        const templateParams = {
            to_email: 'votre-email@example.com', // Votre email
            address: data.address,
            city: data.city,
            postal: data.postal,
            type: data.type,
            rooms: data.rooms,
            surface: data.surface,
            year: data.year,
            condition: data.condition
        };

        return emailjs.send(
            EMAILJS_CONFIG.serviceID,
            EMAILJS_CONFIG.templateID,
            templateParams,
            EMAILJS_CONFIG.publicKey
        );
    } else {
        // Option 2 : Utiliser un backend PHP (voir fichier send-email.php)
        const response = await fetch('send-email.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });

        if (!response.ok) {
            throw new Error('Erreur d\'envoi');
        }

        return response.json();
    }
}

// ===========================
// INITIALISATION
// ===========================

document.addEventListener('DOMContentLoaded', () => {
    // Initialiser les formulaires
    initSellForm();
    initEstimationForm();

    // Charger les annonces selon la page
    const path = window.location.pathname;
    
    if (path.includes('acheter.html')) {
        renderProperties('acheter', 'propertyContainer');
    } else if (path.includes('louer.html')) {
        renderProperties('louer', 'propertyContainer');
    }

    // Ajouter les événements de clic sur les cartes de la page d'accueil
    document.querySelectorAll('.property-card').forEach(card => {
        const btnView = card.querySelector('.btn-view');
        if (btnView) {
            btnView.addEventListener('click', (e) => {
                e.preventDefault();
                // Pour la démo, ouvrir la première annonce
                const properties = propertyManager.getProperties('acheter');
                if (properties.length > 0) {
                    propertyModal.open(properties[0].id);
                }
            });
        }
    });
});
