(function () {
    const translations = {
        sk: {
            translation: {
                'navbar.brand': 'PDF Editor',
                'navbar.home': 'Domov',
                'navbar.operations': 'Operácie',
                'navbar.history': 'História',
                'navbar.manual': 'Príručka',
                'navbar.profile': 'Profil',
                'navbar.logout': 'Odhlásiť',
                'home.title': 'Vitajte v PDF Editore',
                'home.description': 'Jednoducho spracujte svoje PDF súbory – spájajte, editujte, mažte stránky a viac.',
                'operations': {
                    'compress': 'Komprimovať PDF',
                    'jpg_to_pdf': 'JPG do PDF',
                    'merge': 'Spojiť PDF',
                    'rotate': 'Rotovať stránky',
                    'number': 'Číslovať stránky',
                    'protect': 'Pridať heslo',
                    'edit': 'Editovať PDF',
                    'delete_page': 'Odstrániť stránku',
                    'split': 'Rozdeliť PDF',
                    'rearrange': 'Preskupiť stránky'
                }
            }
        },
        en: {
            translation: {
                'navbar.brand': 'PDF Editor',
                'navbar.home': 'Home',
                'navbar.operations': 'Operations',
                'navbar.history': 'History',
                'navbar.manual': 'Manual',
                'navbar.profile': 'Profile',
                'navbar.logout': 'Logout',
                'home.title': 'Welcome to PDF Editor',
                'home.description': 'Easily process your PDF files – merge, edit, delete pages, and more.',
                'operations': {
                    'compress': 'Compress PDF',
                    'jpg_to_pdf': 'JPG to PDF',
                    'merge': 'Merge PDF',
                    'rotate': 'Rotate Pages',
                    'number': 'Number Pages',
                    'protect': 'Add Password',
                    'edit': 'Edit PDF',
                    'delete_page': 'Delete Page',
                    'split': 'Split PDF',
                    'rearrange': 'Rearrange Pages'
                }
            }
        }
    };

    // Initialize i18next
    i18next.init({
        lng: 'sk',
        fallbackLng: 'en',
        resources: translations
    }, function (err, t) {
        if (err) {
            console.error('i18next initialization failed:', err);
            return;
        }
        updateContent();
    });

    // Update content with translations
    function updateContent() {
        document.querySelectorAll('[data-i18n]').forEach(element => {
            element.textContent = i18next.t(element.getAttribute('data-i18n'));
        });
    }

    // Expose changeLanguage globally
    window.changeLanguage = function (lng) {
        i18next.changeLanguage(lng, () => {
            updateContent();
        });
    };
})();