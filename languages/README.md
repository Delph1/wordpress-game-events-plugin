# Bunkersnack Game Manager - Translations

This directory contains translation files for the Bunkersnack Game Manager plugin.

## Translation Files

- `bunkersnack-game-manager-sv_SE.po` - Swedish translation source file (Portable Object format)
- `bunkersnack-game-manager-sv_SE.mo` - Swedish translation compiled file (Machine Object format - binary)

## How to Compile .mo Files

The .mo files are binary compiled versions of the .po files and are required for WordPress to use the translations.

### On Linux/Mac:
```bash
cd languages/
msgfmt -o bunkersnack-game-manager-sv_SE.mo bunkersnack-game-manager-sv_SE.po
```

### Using WordPress CLI:
```bash
wp i18n make-mo languages/ --no-purge
```

### Using Online Tools:
You can also use online translation tools like:
- https://localise.biz/ (supports .po/.mo files)
- https://hosted.webtranslateit.com/

## Adding New Languages

To add a new language translation:

1. Create a new .po file with the appropriate language code (e.g., `bunkersnack-game-manager-fr_FR.po` for French)
2. Use a translation tool like Poedit to create the translation
3. Compile the .po file to .mo format using msgfmt
4. Place both files in this directory

## Translation Strings

All translatable strings in the plugin use the text domain: `bunkersnack-game-manager`

When extracting strings for translation, use:
```bash
wp i18n extract languages/ --text-domain=bunkersnack-game-manager
```

## Swedish Translation

The Swedish translation (sv_SE) is included and covers:
- Admin menu items
- Form labels
- Buttons
- Help text
- Shortcode output
- Error messages

To use the Swedish translation, ensure your WordPress installation has Swedish (Sverige) language selected or the website language is set to Swedish.
