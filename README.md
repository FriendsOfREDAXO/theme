# Theme

Redaxo 5 Addon zum Verwalten aller Projektdateien für Frontend und Backend.

## Ordner
Nach der Installation kann auf der __Theme__-Einstellungsseite mit Klick auf den Button __Theme-Ordner installieren__ eine Ordner-Struktur erstellt werden. Dabei wird auf Ebene des Ordners __redaxo__ der Ordner __theme__ erstellt.

Er enthält zwei weitere Ordner: __public__ und __private__: 
* Im Ordner __public__ können alle öffentlich zugänglichen Dateien, wie z.B. JavaScript und CSS abgelegt werden. 
* Im Ordner __private__ können alle Dateien abgelegt werden, die vor dem Zugriff von außen geschützt werden sollen. Er enthält eine __.htaccess__, die den Zugriff beschränkt.

Die weiteren Unterordner sind ein Vorschlag für die Strukturierung der Projektdateien. Jede andere Variante ist auch möglich. 
Die vorgegebene Struktur hat den Vorteil, dass diese Ordner über PHP-Methoden einfach ansprechbar sind *(siehe unten)*, bzw. wie die entsprechenden Ordner eines Addons automatisch eingebunden werden:
* Alle im Ordner __lib__ enthaltenen PHP-Klassen werden automatisch über __autoload__ eingebunden.
* Alle im Ordner __lang__ enthaltenen Dateien mit der Endung __.lang__ werden automatisch über __i18n__ eingebunden.
* Alle Dateien mit der Endung __.php__ im Ordner __inc__ werden per __include_once__ in der __boot.php__ des Addons eingebunden.
* Der Ordner __redaxo__ ist ein Platzhalter für die Synchronisierungsdaten vom __Developer__ Addon *(siehe unten)*.

## Dateien
Neben der __.htaccess__ werden weitere Dateien erstellt, die - sofern sie nicht gelöscht wurden - vom Addon automatisch eingebunden werden:
* __backend.css__ und __backend.js__ werden auf jeder Seite des Redaxo-Backends geladen. So können auf einfache Weise zusätzliche Scripte, Stile oder Webfonts für das Backend eingebunden werden. Diese Funktion kann in den Einstellungen aktiviert oder deaktiviert werden.
* __functions.php__ wird im Ordner __inc__ angelegt *(siehe oben)* und dient nur als schnelle Starthilfe. Sie kann auch umbenannt, gelöscht oder durch andere Dateien ersetzt werden.

## Developer
Wenn das  __Developer__ Addon installiert ist, gibt es die Möglichkeit, die Synchronisierung der Templates, Module und Actions in den Theme-Ordner umzuleiten. Die Synchronisierung kann für jede der drei Gruppen einzeln aktiviert werden.
Wenn die Synchronisierung in den Theme-Ordner aktiviert ist, wird die entsprechenden Synchronisierung in den Data-Ordner deaktiviert, um das gegenseitige Überschreiben der Daten zu vermeiden. 

## API
Um die Verwaltung der Ordner zu erleichtern stehen folgende PHP-Klassen und -Methoden zur Verfügung:

### theme_url
Analog rex_url werden relative URLs zurückgegeben.

```php
// $filename ist immer optional und kann auch ein Pfad sein. 
$filename = "path/to/file";

// Gibt eine URL im Ordner "theme" zurück. 
$url = theme_url::base($filename);

// Gibt eine URL im Ordner "theme/public/assets" zurück. 
$url = theme_url::assets($filename);

```

### theme_path
Analog rex_path werden absolute Serverpfade zurückgegeben.

```php
// $filename ist immer optional und kann auch ein Pfad sein. 
$filename = "path/to/file";

// Gibt einen Pfad im Ordner "theme" zurück.
$path = theme_path::base($filename);

// Gibt einen Pfad im Ordner "theme/private/lib" zurück.
$path = theme_path::lib($filename);

// Gibt einen Pfad im Ordner "theme/private/inc" zurück.
$path = theme_path::inc($filename);

// Gibt einen Pfad im Ordner "theme/private/lang" zurück.
$path = theme_path::lang($filename);

// Gibt einen Pfad im Ordner "theme/private/fragments" zurück.
$path = theme_path::fragments($filename);

// Gibt einen Pfad im Ordner "theme/public/assets" zurück.
$path = theme_path::assets($filename);
```

### theme_setting
Eine einfache Registry mit der Möglichkeit beim Abruf Default-Werte zu definieren. Sie soll die Übergabe von Daten erleichtern. 
So ist es möglich, über z.B. __functions.php__ Modulen und Templates Einstellungen zu übergeben, z.B. können einem generischen Bildmodul je nach Kategorie oder Spaltenposition verschiedene Bildtypen übergeben werden.

```php
// Erstellt einen Eintrag. Die Daten müssen immer als alphanumerisches Array übergeben werden. 
// Wird die Methode mehrfach mit dem gleichen Schlüssel aufgerufen, werden die Daten über array_merge zusammengeführt.
theme_setting::setKey($key, [
    'index1' => 'data1', 
    'index2' => 'data2', 
    ...
]);

// Holt einen Eintrag. Dabei kann ein Array mit Vorgabe-Werten übergeben werden.
// Die Arrays werden über array_merge() zusammengeführt. 
// Ist ein Schlüssel in beiden Arrays vorhanden, wird der des Default-Arrays mit denen des ausgelesenen Arrays überschrieben.
theme_setting::getKey($key, [vorhanden bzw. 
    'index1' => 'data1', 
    'index2' => 'data2', 
    ...
]);
```
