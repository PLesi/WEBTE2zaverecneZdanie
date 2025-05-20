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
                },
                'footer.text': 'Webové technológie - PDF editor aplikácia',
                'compress.title': 'Kompresia PDF',
                'compress.description': 'Nahrajte PDF súbor a nastavte úroveň kompresie pre optimalizáciu veľkosti.',
                'compress.drop_label': 'Presuňte PDF súbor sem alebo kliknite pre výber',
                'compress.upload_label': 'Nahrať PDF:',
                'compress.level_label': 'Úroveň kompresie (1-9):',
                'compress.submit': 'Komprimovať',
                'compress.preview_title': 'Náhľad komprimovaného PDF:',
                'compress.download': 'Stiahnuť',
                'compress.original_size': 'Pôvodná veľkosť: {{size}} KB',
                'compress.compressed_size': 'Komprimovaná veľkosť: {{size}} KB',
                'compress.error_no_file': 'Prosím, nahrajte PDF súbor.',
                'compress.error_failed': 'Kompresia zlyhala.'
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
                },
                'footer.text': 'Web Technologies - PDF Editor Application',
                'compress.title': 'PDF Compression',
                'compress.description': 'Upload a PDF file and set the compression level to optimize its size.',
                'compress.drop_label': 'Drag and drop a PDF file here or click to select',
                'compress.upload_label': 'Upload PDF:',
                'compress.level_label': 'Compression Level (1-9):',
                'compress.submit': 'Compress',
                'compress.preview_title': 'Compressed PDF Preview:',
                'compress.download': 'Download',
                'compress.original_size': 'Original Size: {{size}} KB',
                'compress.compressed_size': 'Compressed Size: {{size}} KB',
                'compress.error_no_file': 'Please upload a PDF file.',
                'compress.error_failed': 'Compression failed.'
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

    // Set the initial language based on localStorage or default to 'sk'
    const savedLanguage = localStorage.getItem('language') || 'sk';

    // Set the language in i18next
    i18next.init({
        lng: savedLanguage,
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


    window.changeLanguage = function (lng) {
        i18next.changeLanguage(lng, () => {
            updateContent();
            // Save the selected language to localStorage
            localStorage.setItem('language', lng);
        });
    };
})();