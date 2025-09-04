<style>
    /* Referanslar Sayfası Özel Stiller */
    .references-hero {
        background: linear-gradient(rgba(0, 86, 179, 0.8), rgba(0, 51, 102, 0.8)), url('https://via.placeholder.com/1920x600/0056b3/ffffff?text=Referanslarımız');
        background-size: cover;
        background-position: center;
        color: var(--white-color);
        text-align: center;
        padding: 100px 0;
    }

    .references-hero h1 {
        font-size: 3rem;
        margin-bottom: 20px;
    }

    .references-hero p {
        font-size: 1.2rem;
        max-width: 800px;
        margin: 0 auto;
    }

    .references-section {
        padding: 80px 0;
        background-color: var(--white-color);
    }

    .references-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 30px;
        margin-top: 40px;
    }

    .reference-card {
        background-color: var(--white-color);
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        padding: 30px;
        text-align: center;
        transition: var(--transition);
    }

    .reference-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
    }

    .reference-logo {
        width: 150px;
        height: 100px;
        object-fit: contain;
        margin-bottom: 20px;
    }

    .reference-name {
        font-size: 1.2rem;
        color: var(--primary-color);
        margin: 0;
    }

    /* Mobil Uyumluluk */
    @media (max-width: 768px) {
        .references-hero h1 {
            font-size: 2rem;
        }

        .references-hero p {
            font-size: 1rem;
            padding: 0 20px;
        }

        .references-grid {
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            padding: 0 20px;
        }

        .reference-card {
            padding: 20px;
        }

        .reference-logo {
            width: 120px;
            height: 80px;
        }

        .reference-name {
            font-size: 1rem;
        }
    }
</style>