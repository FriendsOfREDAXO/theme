# Theme
Redaxo 5 Addon zum Verwalten aller Projektdateien für Frontend und Backend.

## Ordner
Nach der Installation kann auf der Seite Einstellungen des Theme-Addons mit Klick auf den Button __Theme-Ordner installieren__ eine Ordnerstruktur erstellt werden. 
Die Ordnerstruktur wird auf der Ebene des Ordners __redaxo__ im Ordner __theme__ erstellt.

Der Ordner `theme` enthält zwei Ordner: `public` und `private`: 
* Im Ordner `public` können alle öffentlich zugänglichen Dateien, wie z.B. JavaScript und CSS abgelegt werden. 
* Im Ordner `private` können alle Dateien abgelegt werden, die vor dem Zugriff von außen geschützt werden sollen. Er enthält eine `.htaccess`, die den Zugriff beschränkt.

Die weiteren Unterordner sind ein Vorschlag zur Strukturierung der Projektdateien. Jede andere Variante ist auch möglich. 

Die vorgegebene Struktur hat den Vorteil, dass diese Ordner über [__PHP-Methoden__](#PHP-Methoden) ansprechbar sind, bzw. wie die entsprechenden Ordner eines Addons automatisch eingebunden werden:
* Alle im Ordner `lib` enthaltenen PHP-Klassen werden automatisch über __autoload__ eingebunden.
* Alle im Ordner `lang` enthaltenen Dateien mit der Endung `.lang` werden automatisch über __i18n__ eingebunden.
* Alle Dateien mit der Endung `.php` im Ordner `inc` werden per `include_once` in der `boot.php` des Addons eingebunden.
* Der Ordner `fragments` wird über `rex_fragment::addDirectory` eingebunden.
* Der Ordner `ytemplates` wird über `rex_yform::addTemplatePath` eingebunden, wenn __YForm__ installiert ist.
* Der Ordner `redaxo` ist ein Platzhalter für die Synchronisierungsdaten des Addons [__Developer__](#Developer).

## Dateien
Neben der `.htaccess` werden weitere Dateien erstellt, die - sofern sie nicht gelöscht wurden - vom Addon automatisch eingebunden werden:
* `backend.css` und `backend.js` werden auf jeder Seite des Redaxo-Backends geladen. So können auf einfache Weise zusätzliche Scripte, Stile oder Webfonts für das Backend eingebunden werden. Diese Funktion kann in den Einstellungen des Theme-Addons aktiviert oder deaktiviert werden.
* `functions.php` wird im Ordner `inc` angelegt *(siehe oben)* und dient nur als schnelle Starthilfe. Sie kann auch umbenannt, gelöscht oder durch andere Dateien ersetzt werden.

## Developer
Wenn das Addon __Developer__ installiert ist, gibt es die zusätzliche Option, die Synchronisierung der Templates, Module, Actions und YForm-E-Mail-Templates in den Theme-Ordner umzuleiten. Die Synchronisierung in den Data-Ordner wird dadurch deaktiviert. 

## PHP-Methoden
### Pfade / URL
#### theme_url
Analog zu `rex_url` werden relative URLs zurückgegeben.
```php
// $filename ist immer optional und kann auch ein Pfad sein. 
$filename = "path/to/file";

// Gibt eine URL im Ordner "theme" zurück. 
$url = theme_url::base($filename);

// Gibt eine URL im Ordner "theme/public/assets" zurück. 
$url = theme_url::assets($filename);
```
#### theme_path
Analog `rex_path` werden absolute Serverpfade zurückgegeben.
```php
// $filename ist immer optional und kann auch ein Pfad sein. 
$filename = "path/to/file";

// Gibt einen Pfad im Ordner "theme" zurück.
$path = theme_path::base($filename);

// Gibt einen Pfad im Ordner "theme/private/fragments" zurück.
$path = theme_path::fragments($filename);

// Gibt einen Pfad im Ordner "theme/private/inc" zurück.
$path = theme_path::inc($filename);

// Gibt einen Pfad im Ordner "theme/private/lang" zurück.
$path = theme_path::lang($filename);

// Gibt einen Pfad im Ordner "theme/private/lib" zurück.
$path = theme_path::lib($filename);

// Gibt einen Pfad im Ordner "theme/private/views" zurück.
$path = theme_path::views($filename);

// Gibt einen Pfad im Ordner "theme/private/ytemplates" zurück.
$path = theme_path::ytemplates($filename);

// Gibt einen Pfad im Ordner "theme/public/assets" zurück.
$path = theme_path::assets($filename);
```

### Daten
#### theme_setting
Eine einfache Registry mit der Möglichkeit, beim Abruf Default-Werte zu definieren.
Damit ist es z.B. möglich, in der Datei `functions.php` zentral Einstellungen zu definieren, die in Modulen oder Templates abgerufen werden können.
```php
// Erstellt einen Eintrag.
// Die Daten müssen immer als Array mit alphanumerischen Schlüsseln übergeben werden. 
// Wird die Methode mehrfach mit dem gleichen Schlüssel aufgerufen, 
// werden die Daten über array_merge zusammengeführt.
theme_setting::setKey($key, [
    'index1' => 'data1', 
    'index2' => 'data2', 
    ...
]);

// Holt einen Eintrag. Dabei kann ein Array mit Vorgabe-Werten übergeben werden.
// Die Arrays werden über array_merge() zusammengeführt. 
// Ist ein Schlüssel in beiden Arrays vorhanden, wird der des Default-Arrays 
// mit denen des ausgelesenen Arrays überschrieben.
theme_setting::getKey($key, [
    'index1' => 'data1', 
    'index2' => 'data2', 
    ...
]);
```

### Assets
#### theme_assets
Eine Klasse zur URL-Generierung der Web-Assets einer Website, wie z.B. CSS-Stylesheets, JavaScript-Dateien.

Es ist möglich, Assets über verschiedene Instanzen der Klasse zu gruppieren. Die Asset-Gruppen können entweder über Methoden der Klasse oder mithilfe einer eigenen [REX_VAR](#REX_VAR) an beliebiger Stelle im Quellcode der Website ausgegeben werden.

_**Wichtig:** einige Komfortfunktionen stehen nur bei Nutzung der [REX_VAR](#REX_VAR) zur Verfügung._

```php
// Erzeugt eine Instanz der Klasse. In der Regel genügt eine Instanz zur Verwaltung aller Assets einer Website.
// Bei Bedarf kann durch die Angabe von $id zwischen verschiedenen Instanzen unterschieden werden. 
// $id die Angabe ist optional. Ist $id nicht angegeben, wird die Kennung 'default' verwendet.
$assets = theme_assets::get($id);
```
##### Cache-Buster
Der Cache-Buster hilft, das Browser-Caching einer Website zu beeinflussen. Es handelt sich dabei um einen String, der an die URL einer Asset-Datei angehängt wird.
```php
// Der Cache-Buster wird stets für die gesamte Instanz gesetzt.
// Es gibt mehrere Optionen zur Auswahl:

// 1. Aktuelle Systemzeit
// Hiermit wird die Asset-Datei bei jedem Ladevorgang neu vom Server geholt.
// Der Dateiname wird um die Zeichenfolge '?t=XXXXXXXXXX' ergänzt. XXXXXXXXXX entspricht dabei dem Timestamp der aktuellen Systemzeit.
// ACHTUNG: Diese Einstellung ist nur während Entwicklung und Debugging sinnvoll.
$assets->setCacheBuster('time')

// 2. Änderungsdatum einer Datei
// Hiermit wird die Datei automatisch einmal neu vom Server geladen, wenn sie geändert wurde. 
// Der Dateiname wird um die Zeichenfolge '?f=XXXXXXXXXX' ergänzt. XXXXXXXXXX entspricht dabei dem Timestamp des Änderungsdatums der Datei.
$assets->setCacheBuster('filetime')

// 3. individueller String 
// Hiermit ist z.B. eine manuelle Versionierung der Assets möglich. 
// Der Dateiname wird um die Zeichenfolge '?v=XXXXXXXXXX' ergänzt. XXXXXXXXXX entspricht dabei dem angegebenen String.
$assets->setCacheBuster('1.0.0')

// 4. Cache-Buster löschen
$assets->setCacheBuster('')
```
##### CSS-Daten
Es können sowohl URLs von CSS-Dateien übergeben werden, als auch Inline-Styles.
```php
// Setzt die URL einer Datei
// - $id (string) ein String zur Kennung der Datei
// - $data (string) die URL der Datei. Hierfür kann auch theme_url verwendet werden
// - $media (string) das Media-Attribut der Datei, optional
// - $attributes (array) ein Array mit zusätzlichen Attributen, im Format ['Attribut' => 'Wert'], optional
$assets->setCss($id, $data, $media, $attributes);

// Setzt einen CSS-String
// - $id (string) ein String zur Kennung der Daten
// - $data (string) der CSS-String. Der String wird innerhalb von <style>-Tags ausgegeben 
// - $media (string) das Media-Attribut der Daten, optional
$assets->setCssInline($id, $data, $media);

// Löscht die URL einer Datei
// - $id (string) eine zuvor gesetzte Kennung der Datei
$assets->unsetCss($id);

// Löscht einen CSS-String
// - $id (string) eine zuvor gesetzte Kennung der Daten
$assets->unsetCssInline($id);

// Gibt die gesammelten CSS-Dateien der Instanz als <link>-Tags aus
echo $assets->getCss();

// Gibt die gesammelten CSS-Daten der Instanz als <link>-Tags aus
echo $assets->getCssInline();
```
##### JavaScript-Daten
Es können sowohl URLs von JavaScript-Dateien übergeben werden, als auch Inline-Skripte.
```php
// Setzt die URL einer Datei
// - $id (string) ein String zur Kennung der Datei
// - $data (string) die URL der Datei. Hierfür kann auch theme_url verwendet werden
// - $header (bool) Flag zur Ausgabe im Header. true gibt die Datei im Header aus, false im Footer
// - $attributes (array) ein Array mit zusätzlichen Attributen, im Format ['Attribut' => 'Wert'], optional
$assets->setJs($id, $data, $header, $attributes);

// Setzt einen JavaScript-String
// - $id (string) ein String zur Kennung der Daten
// - $data (string) der JavaScript-String. Der String wird innerhalb von <script>-Tags ausgegeben 
// - $header (bool) Flag zur Ausgabe im Header. true gibt die Datei im Header aus, false im Footer
// - $attributes (array) ein Array mit zusätzlichen Attributen, im Format ['Attribut' => 'Wert'], optional
$assets->setJsInline($id, $data, $header, $attributes);

// Löscht die URL einer Datei
// - $id (string) eine zuvor gesetzte Kennung der Datei
$assets->unsetJs($id);

// Löscht einen JavaScript-String
// - $id (string) eine zuvor gesetzte Kennung der Daten
$assets->unsetJsInline($id);

// Gibt die gesammelten JavaScript-Dateien der Instanz zurück
// - $header (bool) Flag zur Ausgabe im Header. true gibt die zum Header zugeordneten Daten aus, false die zum Footer zugeordneten
echo $assets->getJs($header);

// Gibt die gesammelten JavaScript-Daten der Instanz zurück
// - $header (bool) Flag zur Ausgabe im Header. true gibt die zum Header zugeordneten Daten aus, false die zum Footer zugeordneten
echo $assets->getJsInline($header);
```
##### HTML-Daten
Es können HTML-Strings übergeben werden.
```php
// - $id (string) ein String zur Kennung der Daten
// - $data der HTML-String. Der String wird unverändert ausgegeben 
// - $header (bool) Flag zur Ausgabe im Header. true gibt die Datei im Header aus, false im Footer
$assets->setHtml($id, $data, $header);

// Löscht einen HTMl-String
// - $id (string) eine zuvor gesetzte Kennung der Daten
$assets->unsetHtml($id);

// Gibt die HTML-Daten der Instanz zurück
// - $header (bool) Flag zur Ausgabe im Header. true gibt die Datei im Header aus, false im Footer
echo $assets->getHtml($header);
```

### Extension Points
Die Klasse `theme_assets` bietet mehrere __Extension Points__ zur Beeinflussung der Ausgabe:
- `THEME_ASSETS_CSS`
- `THEME_ASSETS_CSS_INLINE`
- `THEME_ASSETS_JS`
- `THEME_ASSETS_JS_INLINE`
- `THEME_ASSETS_HTML`

Über sie kann z.B. die [Minifizierung der Asset-Dateien](#Minify) umgesetzt werden.


## REX_VAR
Die REX_VAR können statt der Getter-Methoden von `theme_assets` innerhalb von Templates und Modulen verwendet werden.

Sie haben gegenüber den Methoden der Klasse den Vorteil, dass sie das rückwirkende Einschleusen von Daten in Header und Footer erlauben.

Damit ist es z.B. möglich, in Modulen Assets anzugeben, die im HTML-Header eines Templates eingebunden werden. Dafür wird der Extension-Point `OUTPUT_FILTER` genutzt.
```php
// - "id" die Kennung der Instanz, wie bei theme_assets::get() angegeben
// - "type" der Typ der Ausgabe. Er entspricht den Methodennamen, also css, cssinline, js, jsinline oder html
// - "header" Flag für die Header- oder Footer-Gruppe. 1 für Header, 0 für Footer
REX_THEME_ASSETS[id=instanz type=typ header=1]
```


## Minify
Es wird das Addon __FriendsOfREDAXO/minify__ zur Minifizierung der Dateien unterstützt.
```php
// Aktiviert die Unterstützung des Addons Minify.
// Es werden automatisch die Dateien einer Instanz gruppiert und minifiziert. 
// Muss vor theme_assets aufgerufen werden. Am besten am Anfang der Datei functions.php platzieren. 
theme_minify::init();
```


## Beispiele
### Beispiel 1: eine Instanz
#### functions.php
```php
theme_assets::getInstance()
    // Styles
    ->setCss('styles', theme_url::assets('styles/main.css'))
    // JavaScript im Header
    ->setJs('jquery', theme_url::assets('vendor/jquery/jquery.min.js'), true)
    ->setJsInline('inline', 'alert("Dies ist ein Beispiel!");', true)
    // JavaScript im Footer
    ->setJs('demo', theme_url::assets('vendor/demo/scripts/demo.js'))
    ->setJs('scripts', theme_url::assets('scripts/main.js'));
```
#### Template
Die Ausgabe richtet sich nach der Position der REX_VARs.
```html
<!DOCTYPE html>
<html>
    <head>
        ...
        REX_THEME_ASSETS[type=css]
        REX_THEME_ASSETS[type=js header=1]
        REX_THEME_ASSETS[type=js_inline header=1]
    </head>
    <body>
        ...
        REX_THEME_ASSETS[type=js]
    </body>
</html>
```
#### HTML
```html
<!DOCTYPE html>
<html>
    <head>
        ...
        <link media="all" href="/theme/public/assets/styles/main.css" rel="stylesheet" type="text/css" />
        <script src="/theme/public/assets/vendor/jquery/jquery.min.js"></script>
        <script data-key="script--matomo">/*<![CDATA[*/
            alert("Dies ist ein Beispiel!");
        /*]]>*/</script>
    </head>
    <body>
        ...
        <script src="/theme/public/assets/vendor/demo/scripts/demo.js"></script>
        <script src="/theme/public/assets/scripts/main.js"></script>
    </body>
</html>
```

### Beispiel 2: mehrere Instanzen
#### functions.php
```php
theme_assets::getInstance('libraries')
    ->setJs('jquery', theme_url::assets('vendor/jquery/jquery.min.js'), true)
    ->setJs('demo', theme_url::assets('vendor/demo/scripts/demo.js'));
    
theme_assets::getInstance('inline')->setJsInline('inline', 'alert("Dies ist ein Beispiel!");', true);
    
theme_assets::getInstance()
    ->setCss('styles', theme_url::assets('styles/main.css'))
    ->setJs('scripts', theme_url::assets('scripts/main.js'));
```
#### Template
Die Ausgabe richtet sich nach der Position der REX_VARs. 
Einige REX_VARs sind leer.
```html
<!DOCTYPE html>
<html>
    <head>
        ...
        <!--Instanz "libraries"-->
        REX_THEME_ASSETS[id=libraries type=css]
        REX_THEME_ASSETS[id=libraries type=js header=1]
        <!--Instanz "default"-->
        REX_THEME_ASSETS[type=css]
        REX_THEME_ASSETS[type=js header=1]
        <!--Instanz "inline"-->
        REX_THEME_ASSETS[id=inline type=css_inline]
        REX_THEME_ASSETS[id=inline type=js_inline header=1]
    </head>
    <body>
        ...
        <!--Instanz "libraries"-->
        REX_THEME_ASSETS[id=libraries type=js]
        <!--Instanz "default"-->
        REX_THEME_ASSETS[type=js]
    </body>
</html>
```
#### HTML
Die leeren REX_VARs werden übersprungen.
```html
<!DOCTYPE html>
<html>
    <head>
        ...
        <!--Instanz "libraries"-->
        <script src="/theme/public/assets/vendor/jquery/jquery.min.js"></script>
        <!--Instanz "default"-->
        <link media="all" href="/theme/public/assets/styles/main.css" rel="stylesheet" type="text/css" />
        <!--Instanz "inline"-->
        <script data-key="script--matomo">/*<![CDATA[*/
            alert("Dies ist ein Beispiel!");
        /*]]>*/</script>
    </head>
    <body>
        ...
        <!--Instanz "libraries"-->
        <script src="/theme/public/assets/vendor/demo/scripts/demo.js"></script>
        <!--Instanz "default"-->
        <script src="/theme/public/assets/scripts/main.js"></script>
    </body>
</html>
```

### Beispiel 3: Varianten
Einträge können ergänzt, überschrieben oder gelöscht werden.
#### functions.php
```php
theme_assets::getInstance()
    ->setJs('jquery', theme_url::assets('vendor/jquery/jquery.min.js'), true)
    ->setJs('scripts-main', theme_url::assets('scripts/main.js'))
    ->setJs('scripts-sub', theme_url::assets('scripts/sub.js'))
    ->setCss('styles', theme_url::assets('styles/main.css'));
```
#### Modul
- der Eintrag `styles-module` wird ergänzt
- der Eintrag `scripts-sub` wird überschrieben
- der Eintrag `jquery` wird gelöscht, der Eintrag
```php
theme_assets::getInstance()
    ->setCss('styles-module', theme_url::assets('styles/module.css'))
    ->setJs('scripts-sub', theme_url::assets('scripts/module.js'))
    ->unset('jquery', true);
```
#### Template
Die Ausgabe richtet sich nach der Position der REX_VARs.
```html
<!DOCTYPE html>
<html>
    <head>
        ...
        REX_THEME_ASSETS[type=css]
        REX_THEME_ASSETS[type=js header=1]
        REX_THEME_ASSETS[type=js_inline header=1]
    </head>
    <body>
        ...
        REX_THEME_ASSETS[type=js]
    </body>
</html>
```
#### HTML
```html
<!DOCTYPE html>
<html>
    <head>
        ...
        <link media="all" href="/theme/public/assets/styles/main.css" rel="stylesheet" type="text/css" />
        <link media="all" href="/theme/public/assets/styles/module.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        ...
        <script src="/theme/public/assets/scripts/main.js"></script>
        <script src="/theme/public/assets/scripts/module.js"></script>
    </body>
</html>
```
