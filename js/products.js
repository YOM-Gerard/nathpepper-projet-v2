// Données des produits
const products = [
    {
        id: 1,
        name: "Poivre Noir de Kampot",
        description: "Un poivre d'exception du Cambodge, reconnu pour sa saveur unique et son arôme délicat.",
        price: 5.09,
        image: "./public/images/products/poivre-noir.jpg",
        category: "premium",
        specs: {
            "Origine": "Cambodge (Kampot)",
            "Type": "Poivre noir",
            "Intensité": "Moyenne",
            "Notes": "Fruité, floral",
            "Poids": "30g"
        },
        longDescription: "Le poivre de Kampot est cultivé dans la province de Kampot au Cambodge. Ce poivre bénéficie d'une Indication Géographique Protégée (IGP) depuis 2010. Il se distingue par ses notes florales et fruitées uniques, avec une intensité modérée qui en fait un excellent choix pour accompagner les viandes blanches et les fruits de mer."
    },
    {
        id: 2,
        name: "Poivre Rouge de Kampot",
        description: "Un poivre rouge rare et précieux, aux saveurs fruitées et légèrement sucrées.",
        price: 5.89,
        image: "./public/images/products/poivre-rouge.jpg",
        category: "rare",
        specs: {
            "Origine": "Cambodge (Kampot)",
            "Type": "Poivre rouge",
            "Intensité": "Forte",
            "Notes": "Fruité, boisé",
            "Poids": "30g"
        },
        longDescription: "Le Poivre Rouge de Kampot est le produit emblématique de l’appellation IGP, et de la région de Kampot. Vous apprécierez son arôme incroyable à la mouture et son goût fruité et boisé."
    },
    {
        id: 3,
        name: "Poivre Blanc de Kampot",
        description: "Un poivre blanc raffiné du Cambodge, aux notes subtiles et élégantes.",
        price: 6.89,
        image: "./public/images/products/poivre-blanc.jpg",
        category: "premium",
        specs: {
            "Origine": "Cambodge (Kampot)",
            "Type": "Poivre blanc",
            "Intensité": "Douce",
            "Notes": "Subtil, délicat",
            "Poids": "30g"
        },
        longDescription: "Le Poivre Blanc de Kampot est l’un des rares poivres au monde à être produit à partir des grains rouges à pleine maturité, développant ainsi un arôme subtil, avec des notes d’agrumes et d’eucalyptus."
    },
    {
        id: 4,
        name: "Poivre Vert de Kampot",
        description: "Le Poivre de Kampot Vert apporte une fraîcheur végétale et un piquant délicat.",
        price: 15.31,
        image: "./public/images/products/poivre-vert.jpg",
        category: "rare",
        specs: {
            "Origine": "Cambodge (Kampot)",
            "Type": "Poivre vert",
            "Intensité": "Puissant",
            "Notes": "Fraicheur, agrumes, menthe",
            "Poids": "30g"
        },
        longDescription: "Pour produire notre Poivre de Kampot vert déshydraté, nous veillons à cueillir les grappes de poivre de Kampot avant maturité et elles sont ensuite égrainées à la main le jour même de leur récolte. Jeunes et fragiles, ces beaux grains verts nécessitent un traitement délicat et manuel afin de ne pas abimer l’intégrité des grains. Ils sont ensuite ébouillantés et déshydratés à basse température pour conserver l’arôme exceptionnel et unique du poivre de Kampot vert."
    }
];

// Fonction pour afficher les produits
function displayProducts(productsToShow = products) {
    const productsGrid = document.getElementById('products-grid');
    if (!productsGrid) return;

    productsGrid.innerHTML = '';

    productsToShow.forEach(product => {
        const productCard = document.createElement('div');
        productCard.className = 'product-card';
        productCard.innerHTML = `
            <img src="${product.image}" alt="${product.name}" class="product-image" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzAwIiBoZWlnaHQ9IjI1MCIgdmlld0JveD0iMCAwIDMwMCAyNTAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIzMDAiIGhlaWdodD0iMjUwIiBmaWxsPSIjRjVGNUY1Ii8+CjxwYXRoIGQ9Ik0xNTAgMTI1TDE3NSAxMDBIMTI1TDE1MCAxMjVaIiBmaWxsPSIjQ0NDIi8+Cjwvc3ZnPgo='">
            <div class="product-info">
                <h3 class="product-name">${product.name}</h3>
                <p class="product-description">${product.description}</p>
                <div class="product-price">${product.price.toFixed(2)} €</div>
                <div class="product-actions">
                    <button class="btn-add-cart" onclick="addToCart(${product.id})">Ajouter au panier</button>
                </div>
            </div>
        `;

        // Ajouter l'événement de clic pour ouvrir le modal
        productCard.addEventListener('click', (e) => {
            if (!e.target.classList.contains('btn-add-cart')) {
                openProductModal(product);
            }
        });

        productsGrid.appendChild(productCard);
    });
}

// Fonction pour ouvrir le modal produit
function openProductModal(product) {
    const modal = document.getElementById('product-modal');
    const modalBody = document.getElementById('modal-body');
    
    modalBody.innerHTML = `
        <div class="product-modal-content">
            <div class="product-modal-image-container">
                <img src="${product.image}" alt="${product.name}" class="product-modal-image" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAwIiBoZWlnaHQ9IjQwMCIgdmlld0JveD0iMCAwIDQwMCA0MDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSI0MDAiIGhlaWdodD0iNDAwIiBmaWxsPSIjRjVGNUY1Ii8+CjxwYXRoIGQ9Ik0yMDAgMjAwTDI1MCAxNTBIMTUwTDIwMCAyMDBaIiBmaWxsPSIjQ0NDIi8+Cjwvc3ZnPgo='">
            </div>
            <div class="product-modal-info">
                <h2>${product.name}</h2>
                <div class="product-modal-price">${product.price.toFixed(2)} €</div>
                <p class="product-modal-description">${product.longDescription}</p>
                
                <div class="product-specs">
                    <h4>Caractéristiques</h4>
                    ${Object.entries(product.specs).map(([key, value]) => 
                        `<div class="spec-item">
                            <span><strong>${key}:</strong></span>
                            <span>${value}</span>
                        </div>`
                    ).join('')}
                </div>
                
                <div class="quantity-selector">
                    <button class="quantity-btn" onclick="changeQuantity(-1)">-</button>
                    <input type="number" id="modal-quantity" class="quantity-input" value="1" min="1" max="10">
                    <button class="quantity-btn" onclick="changeQuantity(1)">+</button>
                </div>
                
                <button class="btn-primary" onclick="addToCartFromModal(${product.id})" style="width: 100%;">
                    Ajouter au panier
                </button>
            </div>
        </div>
    `;
    
    modal.classList.add('show');
}

// Fonction pour changer la quantité dans le modal
function changeQuantity(change) {
    const quantityInput = document.getElementById('modal-quantity');
    if (!quantityInput) return;
    
    let currentQuantity = parseInt(quantityInput.value);
    let newQuantity = currentQuantity + change;
    
    if (newQuantity >= 1 && newQuantity <= 10) {
        quantityInput.value = newQuantity;
    }
}

// Fonction pour ajouter au panier depuis le modal
function addToCartFromModal(productId) {
    const quantityInput = document.getElementById('modal-quantity');
    const quantity = quantityInput ? parseInt(quantityInput.value) : 1;
    
    addToCart(productId, quantity);
    
    // Fermer le modal
    const modal = document.getElementById('product-modal');
    modal.classList.remove('show');
}

// Fonction pour filtrer les produits
function filterProducts(category) {
    let filteredProducts;
    
    if (category === 'all') {
        filteredProducts = products;
    } else {
        filteredProducts = products.filter(product => product.category === category);
    }
    
    displayProducts(filteredProducts);
}

// Fonction pour rechercher des produits
function searchProducts(searchTerm) {
    const filteredProducts = products.filter(product => 
        product.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
        product.description.toLowerCase().includes(searchTerm.toLowerCase())
    );
    
    displayProducts(filteredProducts);
}

// Fonction pour obtenir un produit par ID
function getProductById(id) {
    return products.find(product => product.id === id);
}

// Initialiser l'affichage des produits
document.addEventListener('DOMContentLoaded', function() {
    displayProducts();
});

// Exporter les fonctions pour les autres modules
window.products = products;
window.getProductById = getProductById;
window.displayProducts = displayProducts;
window.filterProducts = filterProducts;
window.searchProducts = searchProducts;