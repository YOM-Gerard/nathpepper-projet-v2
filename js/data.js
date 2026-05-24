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