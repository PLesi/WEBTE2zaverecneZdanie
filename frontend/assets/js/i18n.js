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
                'compress.error_failed': 'Kompresia zlyhala.',

                'manual.heading': 'Príručka PDF Editora',
                'manual.operations': 'Operácie:',
                'manual.compress.title': '1. Komprimovať PDF',
                'manual.compress.description': 'Na kompresiu PDF súboru vyberte súbor a nastavte úroveň kompresie (1-9).',
                'manual.jpg_to_pdf.title': '2. JPG do PDF',
                'manual.jpg_to_pdf.description': 'Na prevod JPG obrázkov do PDF vyberte obrázky a nastavte poradie.',
                'manual.merge.title': '3. Spojiť PDF',
                'manual.merge.description': 'Na spojenie viacerých PDF súborov vyberte súbory a nastavte poradie.',
                'manual.rotate.title': '4. Rotovať stránky',
                'manual.rotate.description': 'Na otočenie stránok PDF súboru vyberte súbor a nastavte uhol rotácie.',
                'manual.number.title': '5. Číslovať stránky',
                'manual.number.description': 'Na číslovanie stránok PDF súboru vyberte súbor a nastavte formát číslovania.',
                'manual.protect.title': '6. Pridať heslo',
                'manual.protect.description': 'Na pridanie hesla do PDF súboru vyberte súbor a zadajte heslo.',
                'manual.edit.title': '7. Editovať PDF',
                'manual.edit.description': 'Na editáciu PDF súboru vyberte súbor a nastavte požadované úpravy.',
                'manual.delete_page.title': '8. Odstrániť stránku',
                'manual.delete_page.description': 'Na odstránenie stránky z PDF súboru vyberte súbor a nastavte číslo stránky na odstránenie.',
                'manual.split.title': '9. Rozdeliť PDF',
                'manual.split.description': 'Na rozdelenie PDF súboru vyberte súbor a nastavte rozsah stránok na rozdelenie.',
                'manual.rearrange.title': '10. Preskupiť stránky',
                'manual.rearrange.description': 'Na preskupenie stránok PDF súboru vyberte súbor a nastavte nové poradie stránok.',
                'manual.download_button': 'Stiahnuť ako PDF',

                'profile.title': 'Správa API kľúča',
                'profile.user_info_title': 'Vaše informácie',
                'profile.user_id_label': 'ID Používateľa (zo Session):',
                'profile.username_label': 'Meno (z DB):',
                'profile.status_label': 'Status (z DB):',
                'profile.status_admin': '{{isAdmin}}',
                'profile.api_key_title': 'API kľúč',
                'profile.api_key_db_label': 'API kľúč uložený v databáze:',
                'profile.api_key_session_label': 'API kľúč uložený v session:',
                'profile.api_key_warning': 'Kliknite pre vygenerovanie nového API kľúča. Starý bude neplatný.',
                'profile.generate_button': 'Vygenerovať a uložiť nový API kľúč',
                'profile.message': '{{message}}',
                'profile.status_admin_true': 'Administrátor',
                'profile.status_admin_false': 'Bežný užívateľ',
                'profile.session_missing': 'Chýba v session',
                'profile.session_expired': 'Session vypršala / neplatná',
                'profile.error_user_not_found': 'Chyba: Používateľ s ID {{userId}} sa nenašiel v databáze. Vaša session môže byť neplatná.',
                'profile.error_no_user_id': 'Chyba: ID používateľa nie je nastavené v session. Prosím, nastavte ho (simulované prihlásenie).',
                'profile.success_api_key_changed': 'API kľúč bol úspešne zmenený v DB aj v session!',
                'profile.error_api_key_update': 'Chyba pri aktualizácii API kľúča v databáze (možno starý kľúč neexistuje?).',
                'profile.error_api_key_generation': 'Chyba pri generovaní nového API kľúča.',
                'profile.error_db_api_key_change': 'Chyba DB pri zmene API kľúča: {{error}}',
                'profile.error_db_user_data': 'Chyba pri načítaní dát používateľa: {{error}}'
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
                    'number': 'Add Page Numbers',
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
                'compress.error_failed': 'Compression failed.',

                'manual.heading': 'PDF Editor manual',
                'manual.operations': 'Operations:',
                'manual.compress.title': '1. Compress PDF',
                'manual.compress.description': 'To compress a PDF file, select the file and set the compression level (1-9).',
                'manual.jpg_to_pdf.title': '2. JPG to PDF',
                'manual.jpg_to_pdf.description': 'To convert JPG images to PDF, select the images and set their order.',
                'manual.merge.title': '3. Merge PDF',
                'manual.merge.description': 'To merge multiple PDF files, select the files and set their order.',
                'manual.rotate.title': '4. Rotate Pages',
                'manual.rotate.description': 'To rotate pages of a PDF file, select the file and set the rotation angle.',
                'manual.number.title': '5. Number Pages',
                'manual.number.description': 'To number pages of a PDF file, select the file and set the numbering format.',
                'manual.protect.title': '6. Add Password',
                'manual.protect.description': 'To add a password to a PDF file, select the file and enter the password.',
                'manual.edit.title': '7. Edit PDF',
                'manual.edit.description': 'To edit a PDF file, select the file and make the desired changes.',
                'manual.delete_page.title': '8. Delete Page',
                'manual.delete_page.description': 'To delete a page from a PDF file, select the file and specify the page number to delete.',
                'manual.split.title': '9. Split PDF',
                'manual.split.description': 'To split a PDF file, select the file and set the page range to split.',
                'manual.rearrange.title': '10. Rearrange Pages',
                'manual.rearrange.description': 'To rearrange pages of a PDF file, select the file and set the new page order.',
                'manual.download_button': 'Download as PDF',

                'profile.title': 'API Key Management',
                'profile.user_info_title': 'Your Information',
                'profile.user_id_label': 'User ID (from Session):',
                'profile.username_label': 'Username (from DB):',
                'profile.status_label': 'Status (from DB):',
                'profile.status_admin': '{{isAdmin}}',
                'profile.api_key_title': 'API Key',
                'profile.api_key_db_label': 'API Key Stored in Database:',
                'profile.api_key_session_label': 'API Key Stored in Session:',
                'profile.api_key_warning': 'Click to generate a new API key. The old one will become invalid.',
                'profile.generate_button': 'Generate and Save New API Key',
                'profile.message': '{{message}}',
                'profile.status_admin_true': 'Administrator',
                'profile.status_admin_false': 'Regular User',
                'profile.session_missing': 'Missing in session',
                'profile.session_expired': 'Session expired / invalid',
                'profile.error_user_not_found': 'Error: User with ID {{userId}} not found in the database. Your session may be invalid.',
                'profile.error_no_user_id': 'Error: User ID is not set in session. Please set it (simulated login).',
                'profile.success_api_key_changed': 'API key successfully changed in DB and session!',
                'profile.error_api_key_update': 'Error updating API key in database (perhaps the old key doesn’t exist?).',
                'profile.error_api_key_generation': 'Error generating a new API key.',
                'profile.error_db_api_key_change': 'Database error while changing API key: {{error}}',
                'profile.error_db_user_data': 'Error loading user data: {{error}}'
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