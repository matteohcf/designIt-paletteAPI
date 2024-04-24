Palette IT API based on Php

Buongiorno Prof, come concordato in classe ho inserito nella cartella i seguenti file:
- File delle collection di postman (PaletteIT.postman_collection.json [deve solo modificare il dominio])
- File del database (design_palette.sql)
- Files delle API in Php (tutti i file php che vede, non tenga in considerazione getLikedPalette.php e getSavedPalette.php che mi servivano per l'app mobile)

Informazioni utili:
- Sito Web: palette.matteocarrara.it
- Le informazioni del database, il nome utente e la password per accedere ad esso sono nel file config.php

Tecnologie utilizzate:
- Front-End: React + Bootstrap
- Back-End: Php
- Database: MySql

Pagine nel sito web con le relative API che vengono chiamate:
- Home: pagina home selezionabile dalla navbar
    → getPalette.php (prende tutte le palette dal backend e in front-end vengono randomizzate e ne vengono visualizzate solo un tot)

- Palette: pagina palette selezionabile dalla navbar
    → getPalette.php (prende tutte le palette dal backend con i relativi dati)
    → addLike.php (quando viene messo like alla palette)
    → savePalette.php (quando viene salvata la palette)

- About (nessuna API)

- Login: icona account nella navbar
    → authGoogle.php (quando viene selezionato "Continua con Google")
    → login.php (esegue il login)

- Register: entrare nella sezione login e andare poi nel register
    → authGoogle.php (quando viene selezionato "Continua con Google" [come sopra])
    → register.php (esegue il register)

- Dashboard: pagina accessibile una volta fatto il login
    → getPaletteDashboard.php (prende le palette pubblicate dall'utente)
    → getPaletteFiltered.php (prende le palette con il like o salvate dall'utente)
    → addLike.php (quando viene messo like alla palette [come sopra])
    → savePalette.php (quando viene salvata la palette [come sopra])
    → creaPalette.php (quando viene pubblicata una nuova palette tramite il bottone Pubblica)
    → deletePalette.php (quando viene eliminata una palette esistente tramite l'icona X)