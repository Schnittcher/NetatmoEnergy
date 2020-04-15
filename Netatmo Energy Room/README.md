# Netatmo Energy Room
mit diesem Modul lassen sich die Räume aus dem Netatmo Energy Account in IP-Symcon darstellen.

### Inhaltsverzeichnis

1. [Funktionsumfang](#1-funktionsumfang)
2. [Voraussetzungen](#2-voraussetzungen)
3. [Einrichten der Instanzen in IP-Symcon](#4-einrichten-der-instanzen-in-ip-symcon)
5. [Statusvariablen und Profile](#5-statusvariablen-und-profile)
6. [WebFront](#6-webfront)
7. [PHP-Befehlsreferenz](#7-php-befehlsreferenz)

### 1. Funktionsumfang

* Über dieses Modul lässt sich das Heizverhalten der einzelnen Räume einstellen.

### 2. Vorraussetzungen

- IP-Symcon ab Version 5.2

### 4. Einrichten der Instanzen in IP-Symcon

Über den Konfigurator kann diese Instanz angelegt werden.

__Konfigurationsseite__:

Name     | Beschreibung
-------- | ------------------
Module ID| Die ID des Gerätes, wird beim Anlegen über den Konfigurator gefüllt.

### 5. Statusvariablen und Profile

Die Statusvariablen/Kategorien werden automatisch angelegt. Das Löschen einzelner kann zu Fehlfunktionen führen.

#### Statusvariablen

Name   | Typ     | Beschreibung
------ | ------- | ------------
Erreichbar|Boolean| Diese Variable zeigt an, ob das Gerät erreichbar ist.
Gemessene Temperatur|Float| Diese Variable zeigt die gemessene Temperatur an.
Geöffnete Fenster|Boolean| Diese Variable zeigt an, ob es geöffnete Fenster in dem Raum gibt.
Modus|Integer| Diese Variable zeigt den eingestellten Modus an und kann den diesen verändern.
Soll Temperatur|Float| Über diese Variable kann die Soll Temperatur eingestellt werden.
Vorwegnehmen|Boolean| ???

#### Profile

Name   | Typ
------ | -------
NA.SetPointMode| Integer

### 6. WebFront

Über das Webfront kann die Soll Temperatur eingestellt werden und der Modus verändert werden.

### 7. PHP-Befehlsreferenz

`RequestAction($VariablenID, $Value);`

Aktiviert einen Modus oder verändert die Temperatur, Value ist in diesem Fall der Wert aus dem Profil oder die Temperatur.

Beispiel Modus:

`RequestAction(12345, 1);`

`RequestAction(12345, 2);`

Beispiel Temperatur:

`RequestAction(56789, 20);`