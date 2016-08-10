# Theme

Redaxo 5 Addon zum Verwalten aller Projektdateien für Frontend und Backend.

![Screenshot](https://raw.githubusercontent.com/FriendsOfREDAXO/theme/assets/theme.png)

## Ordner
Nach der Installation kann in auf der __Theme__-Einstellungsseite mit Klick auf den Button __Theme-Ordner installieren__ die Ordner-Struktur erstellt werden. Dabei wird auf Ebene des Ordners __redaxo__ der Ordner __theme__ erstellt.

Er enthält zwei weitere Ordner: __public__ und __private__: 
* Im Ordner __public__ können alle öffentlich zugänglichen Dateien, wie JavaScript und CSS hinterlegt werden. 
* Im Ordner __private__ alle Dateien, die vor dem Zugriff von außen geschützt werden sollen. Er enthält eine __.htaccess__, die den Zugriff beschränkt.

Die weiteren Unterordner sind ein Vorschlag für die Strukturierung der Projektdateien. Jede andere Variante ist auch möglich. 
Die vorgegebene Struktur hat den Vorteil, dass diese Ordner über PHP-Methoden einfach ansprechbar sind *(siehe unten)*, bzw. automatisch eingebunden werden:
* Der Ordner __lib__ wird über __autoload__ eingebunden.
* Der Ordner __lang__ wird über __i18n__ eingebunden
* Der Ordner __redaxo__ ist ein Platzhalter für die Synchronisierungsdaten vom __Developer__ Addon *(siehe unten)*.

## Dateien
Neben der __.htaccess__ werden weitere Dateien erstellt, die - sofern sie nicht gelöscht wurden - vom Addon automatisch eingebunden werden:
* __backend.css__ und __backend.js__ werden auf jeder Seite des Redaxo-Backends geladen. So können auf einfache Weise zusätzliche Scripte, Stile oder Webfonts für das Projekt eingebunden werden. Diese Funktion kann in den Einstellungen deaktiviert werden.
* __Functions.php__ wird im Frontend und Backend geladen. Hier können eigene PHP-Skripte hinterlegt werden.

## Developer
Wenn das  __Developer__ Addon installiert ist, gibt es die Möglichkeit, die Synchronisation der Templates, Module und Actions in den Theme-Ordner umzuleiten. Die Synchronisation kann für jede der drei Gruppen einzeln aktiviert werden.
Wenn die Synchronisation in den Theme-Ordner aktiviert ist, wird die entsprechenden Synchronisation in den Data-Ordner deaktiviert, um das gegenseitige Überschreiben der Daten zu vermeiden. 

## API
Um die Verwaltung der Ordner zu erleichtern stehen folgende PHP-Klassen und -Methoden zur Verfügung:

### theme_url
Analog rex_url werden relative URLs zurückgegeben.

```php
// Gibt eine URL im Ordner "theme" zurück. 
$url = theme_url::base($filename);

// Gibt eine URL im Ordner "theme/public/assets" zurück.
$url = theme_url::assets($filename);

// $filename ist immer optional.
```

### theme_path
Analog rex_path werden absolute Serverpfade zurückgegeben.

```php
// Gibt einen Pfad im Ordner "theme" zurück.
$path = theme_url::base($filename);

// Gibt einen Pfad im Ordner "theme/private/lib" zurück.
$path = theme_url::lib($filename);

// Gibt einen Pfad im Ordner "theme/private/lang" zurück.
$path = theme_url::lang($filename);

// Gibt einen Pfad im Ordner "theme/private/views" zurück.
$path = theme_url::views($filename);

// Gibt einen Pfad im Ordner "theme/public/assets" zurück.
$path = theme_url::assets($filename);

// $filename ist immer optional.
```

### theme_setting
Eine einfache Registry mit der Möglichkeit beim Abruf Default-Werte zu definieren. Sie soll die Übergabe von Daten erleichtern. 
So ist es möglich, über "Functions.php" Modulen und Templates projektspezifische Einstellungen zu übergeben, z.B. können einem generischen Bildmodul je nach Kategorie oder Spaltenposition verschiedene Bildtypen übergeben werden.

```php
// Erstellt einen Eintrag. Die Daten müssen immer als alphanumerisches Array übergeben werden. 
// Wird die Methode mehrfach mit dem gleichen Schlüssel aufgerufen, werden die Daten über array_merge zusammengeführt.
theme_setting::setKey($key, array(
    'index1' => 'data1', 
    'index2' => 'data2', 
    ...
));

// Holt einen Eintrag. Dabei muss ein Array mit Default-Werten übergeben werden. 
// Die Daten des Default-Arrays werden mit den geholten Daten überschrieben, sofern die Daten-Schlüssel gleich sind.
theme_setting::getKey($key, array(
    'index1' => 'data1', 
    'index2' => 'data2', 
    ...
));
```
