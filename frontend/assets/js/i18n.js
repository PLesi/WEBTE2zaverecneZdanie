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
                'navbar.login': 'Prihlásenie',
                'navbar.register': 'Registrácia',
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
                'compress.upload_label': 'Nahraj PDF',
                'compress.level_label': 'Úroveň kompresie (1-9):',
                'compress.submit': 'Komprimovať',
                'compress.preview_title': 'Náhľad komprimovaného PDF:',
                'compress.download': 'Stiahnuť',
                'compress.original_size': 'Pôvodná veľkosť: {{size}} KB',
                'compress.compressed_size': 'Komprimovaná veľkosť: {{size}} KB',
                'compress.error_no_file': 'Prosím, nahrajte PDF súbor.',
                'compress.error_failed': 'Kompresia zlyhala.',

                'jpg_to_pdf.title': 'Konvertovať JPG do PDF s náhľadom',
                'jpg_to_pdf.upload_label': 'Nahraj JPG',
                'jpg_to_pdf.convert_button': 'Konvertovať na PDF',
                'jpg_to_pdf.converting': 'Konvertuje sa...',
                'jpg_to_pdf.download_button': 'Stiahnuť PDF',
                'jpg_to_pdf.error_invalid_file': 'Súbor musí byť vo formáte JPG.',
                'jpg_to_pdf.error_read_file': 'Chyba pri čítaní súboru.',
                'jpg_to_pdf.error_no_file': 'Prosím, vyberte JPG súbor.',
                'jpg_to_pdf.error_convert': 'Chyba pri konverzii: {{error}}',
                'jpg_to_pdf.error_failed': 'Chyba: {{error}}',
                'jpg_to_pdf.error_no_pdf': 'Nie je k dispozícii PDF na stiahnutie.',
                'jpg_to_pdf.success_message': 'Konverzia úspešná!',

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

                'merge.title': 'Spojiť dva PDF súbory',
                'merge.label_file1': 'Vyberte prvý PDF',
                'merge.label_file2': 'Vyberte druhý PDF',
                'merge.merge_button': 'Spojiť PDF',
                'merge.merging': 'Spája sa...',
                'merge.error_no_files': 'Prosím, vyberte oba PDF súbory.',
                'merge.error_invalid_file': 'Oba súbory musia byť vo formáte PDF.',
                'merge.error_merge': 'Chyba pri spájaní: {{error}}',
                'merge.error_failed': 'Chyba: {{error}}',
                'merge.success_message': 'Spojené PDF bolo úspešne stiahnuté!',

                'rotate.title': 'Rotovať všetky stránky PDF',
                'rotate.label_rotation': 'Uhol rotácie:',
                'rotate.option_90': '90°',
                'rotate.option_180': '180°',
                'rotate.option_270': '270°',
                'rotate.rotate_button': 'Rotovať',
                'rotate.download_button': 'Stiahnuť rotované PDF',
                'rotate.error_invalid_file': 'Prosím, vyberte platný PDF súbor.',
                'rotate.error_no_file': 'Prosím, najprv nahrajte PDF súbor.',
                'rotate.error_rotation': 'Chyba pri rotácii PDF: {{error}}',
                'rotate.error_failed': 'Rotácia zlyhala: {{error}}',
                'rotate.error_no_rotated_pdf': 'Nie je k dispozícii rotované PDF na stiahnutie.',

                'number.title': 'Číslovať stránky',
                'number.upload_label': 'Nahraj PDF',
                'number.label_position': 'Pozícia číslovania:',
                'number.option_bottomRight': 'Spodná pravá',
                'number.option_bottomLeft': 'Spodná ľavá',
                'number.option_topLeft': 'Horná ľavá',
                'number.option_topRight': 'Horná pravá',
                'number.label_font_size': 'Veľkosť písma:',
                'number.number_button': 'Číslovať stránky',
                'number.numbering': 'Čísluje sa...',
                'number.download_button': 'Stiahnuť číslované PDF',
                'number.error_invalid_file': 'Súbor musí byť vo formáte PDF.',
                'number.error_no_file': 'Prosím, vyberte PDF súbor.',
                'number.error_numbering': 'Chyba pri číslovaní: {{error}}',
                'number.error_failed': 'Chyba: {{error}}',
                'number.error_no_numbered_pdf': 'Nie je k dispozícii číslované PDF na stiahnutie.',

                'protect.title': 'Password Protect PDF',
                'protect.label_pdf': 'Nahrať PDF:',
                'protect.label_password': 'Zadať heslo:',
                'protect.download_button': 'Stiahnuť chránené PDF',
                'protect.original_size': 'Pôvodná veľkosť: {{size}} KB',
                'protect.error_missing_input': 'Prosím, nahrajte PDF a zadajte heslo.',
                'protect.error_protection_failed': 'Chránenie hesla zlyhalo.',
                'protect.error': 'Chyba: {{error}}',
                'protect.error_no_protected_pdf': 'Nie je k dispozícii chránené PDF na stiahnutie.',

                'edit.title': 'Editovať PDF',
                'edit.upload_label': 'Nahraj PDF',
                'edit.title_pen_width': 'Hrúbka pera:',
                'edit.title_pen_color': 'Farba pera:',
                'edit.label_page': 'Stránka:',
                'edit.prev_button': '<',
                'edit.next_button': '>',
                'edit.text_button': 'Text',
                'edit.title_text_size': 'Veľkosť textu:',
                'edit.title_text_color': 'Farba textu:',
                'edit.eraser_button': 'Guma',
                'edit.title_eraser_width': 'Hrúbka gumy:',
                'edit.clear_button': 'Vymazať všetko',
                'edit.save_button': 'Uložiť a odoslať',
                'edit.preview_title': 'Náhľady stránok',
                'edit.error_invalid_file': 'Prosím, vyberte platný PDF súbor.',
                'edit.error_load_pdf': 'Nepodarilo sa načítať PDF: {{error}}',
                'edit.error_read_file': 'Chyba pri čítaní súboru.',
                'edit.error_invalid_page': 'Neplatné číslo stránky.',
                'edit.error_load_page': 'Nepodarilo sa načítať stránku: {{error}}',
                'edit.error_no_pdf': 'Nie je načítaný žiadny PDF súbor.',
                'edit.error_no_file': 'Nie je vybratý žiadny súbor na odoslanie.',
                'edit.error_save_failed': 'Nepodarilo sa uložiť PDF: {{error}}',

                'delete_page.title': 'Odstrániť stránku',
                'delete_page.instruction': 'Kliknite na stránku pre jej odstránenie',
                'delete_page.placeholder_upload': 'Nahrať PDF súbor',
                'delete_page.download_button': 'Stiahnuť PDF',
                'delete_page.delete_label': 'ODSTRÁNIŤ',
                'delete_page.error_invalid_file': 'Prosím, vyberte platný PDF súbor.',
                'delete_page.error_remove': 'Chyba pri odstraňovaní stránky: {{error}}',
                'delete_page.error_failed': 'Nepodarilo sa odstrániť stránku: {{error}}',
                'delete_page.error_no_updated_pdf': 'Nie je k dispozícii aktualizované PDF na stiahnutie.',

                'split.title': 'Rozdeliť PDF',
                'split.instruction': 'Nahrať PDF na rozdelenie',
                'split.placeholder_upload': 'Nahraj PDF',
                'split.upload_button': 'Nahrať a rozdeliť',
                'split.uploading': 'Nahráva sa...',
                'split.download_button': 'Stiahnuť ZIP',
                'split.error_invalid_file': 'Súbor musí byť vo formáte PDF.',
                'split.error_response': 'Chyba pri spracovaní: {{error}}',
                'split.error_upload': 'Chyba pri nahrávaní: {{error}}',
                'split.error_no_zip': 'Nie je k dispozícii ZIP na stiahnutie.',
                'split.file_info': 'Súbor: {{file}}, veľkosť: {{size}} KB',

                'rearrange.title': 'Preskupiť stránky',
                'rearrange.instruction': 'Presuňte stránky pre zmenu poradia',
                'rearrange.placeholder_upload': 'Nahrať PDF súbor',
                'rearrange.download_button': 'Stiahnuť PDF',
                'rearrange.error_invalid_file': 'Prosím, vyberte platný PDF súbor.',
                'rearrange.error_rearrange': 'Chyba pri preskupovaní PDF: {{error}}',
                'rearrange.error_failed': 'Nepodarilo sa preskupiť PDF: {{error}}',
                'rearrange.error_no_pdf': 'Nie je k dispozícii preskupené PDF.',

                'profile.title': 'Správa API kľúča',
                'profile.user_info_title': 'Vaše informácie',
                'profile.user_id_label': 'ID Používateľa (zo Session):',
                'profile.username_label': 'Meno:',
                'profile.status_label': 'Status:',
                'profile.status_admin': 'Admin',
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
                'profile.error_db_user_data': 'Chyba pri načítaní dát používateľa: {{error}}',

                'login.title': 'Prihlásenie',
                'login.heading': 'Prihlásenie',
                'login.label_email': 'Email:',
                'login.label_password': 'Heslo:',
                'login.error_invalid_email': 'Zadajte platný email.',
                'login.error_empty': 'Vyplňte všetky polia.',
                'login.error_invalid': 'Nesprávny email alebo heslo.',
                'login.error_db': 'Chyba databázy. Skúste znova neskôr.',
                'login.submit_button': 'Prihlásiť sa',

                'register.title': 'Registrácia',
                'register.heading': 'Registrácia',
                'register.label_username': 'Meno:',
                'register.label_email': 'Email:',
                'register.label_password': 'Heslo:',
                'register.label_confirm_password': 'Potvrď heslo:',
                'register.error_invalid_email': 'Zadajte platný email.',
                'register.error_password_mismatch': 'Heslá sa nezhodujú.',
                'register.submit_button': 'Registrovať sa',

                "admin_access": {
                    "title": "Admin prístup",
                    "description": "Zadajte admin kľúč pre získanie admin privilégií:",
                    "input_placeholder": "Zadajte admin kľúč",
                    "button": "Overiť kľúč"
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
                'navbar.login': 'Login',
                'navbar.register': 'Register',
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
                'compress.upload_label': 'Upload PDF',
                'compress.level_label': 'Compression Level (1-9):',
                'compress.submit': 'Compress',
                'compress.preview_title': 'Compressed PDF Preview:',
                'compress.download': 'Download',
                'compress.original_size': 'Original Size: {{size}} KB',
                'compress.compressed_size': 'Compressed Size: {{size}} KB',
                'compress.error_no_file': 'Please upload a PDF file.',
                'compress.error_failed': 'Compression failed.',

                'jpg_to_pdf.title': 'Convert JPG to PDF with Preview',
                'jpg_to_pdf.upload_label': 'Upload JPG',
                'jpg_to_pdf.convert_button': 'Convert to PDF',
                'jpg_to_pdf.converting': 'Converting...',
                'jpg_to_pdf.download_button': 'Download PDF',
                'jpg_to_pdf.error_invalid_file': 'File must be a JPG image.',
                'jpg_to_pdf.error_read_file': 'Error reading the file.',
                'jpg_to_pdf.error_no_file': 'Please select a JPG file.',
                'jpg_to_pdf.error_convert': 'Conversion error: {{error}}',
                'jpg_to_pdf.error_failed': 'Error: {{error}}',
                'jpg_to_pdf.error_no_pdf': 'No PDF available to download.',
                'jpg_to_pdf.success_message': 'Conversion successful!',

                'merge.title': 'Merge Two PDF Files',
                'merge.label_file1': 'Select First PDF',
                'merge.label_file2': 'Select Second PDF',
                'merge.merge_button': 'Merge PDFs',
                'merge.merging': 'Merging...',
                'merge.error_no_files': 'Please select both PDF files.',
                'merge.error_invalid_file': 'Both files must be PDFs.',
                'merge.error_merge': 'Error merging PDFs: {{error}}',
                'merge.error_failed': 'Error: {{error}}',
                'merge.success_message': 'Merged PDF downloaded successfully!',

                'rotate.title': 'Rotate All PDF Pages',
                'rotate.label_rotation': 'Rotation Angle:',
                'rotate.option_90': '90°',
                'rotate.option_180': '180°',
                'rotate.option_270': '270°',
                'rotate.rotate_button': 'Rotate',
                'rotate.download_button': 'Download Rotated PDF',
                'rotate.error_invalid_file': 'Please select a valid PDF.',
                'rotate.error_no_file': 'Please upload a PDF first.',
                'rotate.error_rotation': 'Error rotating PDF: {{error}}',
                'rotate.error_failed': 'Rotation failed: {{error}}',
                'rotate.error_no_rotated_pdf': 'No rotated PDF available to download.',

                'number.title': 'Number All PDF Pages',
                'number.upload_label': 'Upload PDF',
                'number.label_position': 'Numbering Position:',
                'number.option_bottomRight': 'Bottom Right',
                'number.option_bottomLeft': 'Bottom Left',
                'number.option_topLeft': 'Top Left',
                'number.option_topRight': 'Top Right',
                'number.label_font_size': 'Font Size:',
                'number.number_button': 'Number Pages',
                'number.numbering': 'Numbering...',
                'number.download_button': 'Download Numbered PDF',
                'number.error_invalid_file': 'File must be a PDF.',
                'number.error_no_file': 'Please select a PDF file.',
                'number.error_numbering': 'Numbering error: {{error}}',
                'number.error_failed': 'Error: {{error}}',
                'number.error_no_numbered_pdf': 'No numbered PDF available to download.',

                'protect.title': 'Add Password to PDF',
                'protect.label_pdf': 'Upload PDF:',
                'protect.label_password': 'Enter Password:',
                'protect.download_button': 'Download protected PDF',
                'protect.original_size': 'Original Size: {{size}} KB',
                'protect.error_missing_input': 'Please upload a PDF and enter a password.',
                'protect.error_protection_failed': 'Password protection failed.',
                'protect.error': 'Error: {{error}}',
                'protect.error_no_protected_pdf': 'No protected PDF available to download.',

                'edit.title': 'PDF Editing',
                'edit.upload_label': 'Upload PDF',
                'edit.title_pen_width': 'Pen Width:',
                'edit.title_pen_color': 'Pen Color:',
                'edit.label_page': 'Page:',
                'edit.prev_button': '<',
                'edit.next_button': '>',
                'edit.text_button': 'Text',
                'edit.title_text_size': 'Text Size:',
                'edit.title_text_color': 'Text Color:',
                'edit.eraser_button': 'Eraser',
                'edit.title_eraser_width': 'Eraser Width:',
                'edit.clear_button': 'Clear All',
                'edit.save_button': 'Save and Send',
                'edit.preview_title': 'Page Previews',
                'edit.error_invalid_file': 'Please select a valid PDF file.',
                'edit.error_load_pdf': 'Failed to load PDF: {{error}}',
                'edit.error_read_file': 'Error reading the file.',
                'edit.error_invalid_page': 'Invalid page number.',
                'edit.error_load_page': 'Failed to load page: {{error}}',
                'edit.error_no_pdf': 'No PDF file loaded.',
                'edit.error_no_file': 'No file selected to send.',
                'edit.error_save_failed': 'Failed to save PDF: {{error}}',

                'delete_page.title': 'Delete Page',
                'delete_page.instruction': 'Click on a page to delete it',
                'delete_page.placeholder_upload': 'Upload PDF file',
                'delete_page.download_button': 'Download PDF',
                'delete_page.delete_label': 'DELETE',
                'delete_page.error_invalid_file': 'Please select a valid PDF.',
                'delete_page.error_remove': 'Error removing page: {{error}}',
                'delete_page.error_failed': 'Failed to remove page: {{error}}',
                'delete_page.error_no_updated_pdf': 'No updated PDF to download.',

                'split.title': 'Split PDF',
                'split.instruction': 'Upload PDF to Split',
                'split.placeholder_upload': 'Upload PDF',
                'split.upload_button': 'Upload and Split',
                'split.uploading': 'Uploading...',
                'split.download_button': 'Download ZIP',
                'split.error_invalid_file': 'File must be a PDF.',
                'split.error_response': 'Processing error: {{error}}',
                'split.error_upload': 'Upload error: {{error}}',
                'split.error_no_zip': 'No ZIP available to download.',
                'split.file_info': 'File: {{file}}, size: {{size}} KB',

                'rearrange.title': 'Rearrange Pages',
                'rearrange.instruction': 'Drag pages to rearrange order',
                'rearrange.placeholder_upload': 'Upload PDF file',
                'rearrange.download_button': 'Download PDF',
                'rearrange.error_invalid_file': 'Please select a valid PDF file.',
                'rearrange.error_rearrange': 'Error rearranging PDF: {{error}}',
                'rearrange.error_failed': 'Failed to rearrange PDF: {{error}}',
                'rearrange.error_no_pdf': 'No rearranged PDF available yet.',

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
                'profile.user_id_label': 'User ID:',
                'profile.username_label': 'Username:',
                'profile.status_label': 'Status:',
                'profile.status_admin': 'Admin',
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
                'profile.error_db_user_data': 'Error loading user data: {{error}}',

                'login.title': 'Login',
                'login.heading': 'Login',
                'login.label_email': 'Email:',
                'login.label_password': 'Password:',
                'login.error_invalid_email': 'Please enter a valid email.',
                'login.error_empty': 'Please fill in all fields.',
                'login.error_invalid': 'Invalid email or password.',
                'login.error_db': 'Database error. Please try again later.',
                'login.submit_button': 'Log In',

                'register.title': 'Registration',
                'register.heading': 'Registration',
                'register.label_username': 'Username:',
                'register.label_email': 'Email:',
                'register.label_password': 'Password:',
                'register.label_confirm_password': 'Confirm Password:',
                'register.error_invalid_email': 'Please enter a valid email.',
                'register.error_password_mismatch': 'Passwords do not match.',
                'register.submit_button': 'Register',

                "admin_access": {
                    "title": "Admin Access",
                    "description": "Enter the admin key to gain administrative privileges:",
                    "input_placeholder": "Enter admin key",
                    "button": "Verify Key"
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