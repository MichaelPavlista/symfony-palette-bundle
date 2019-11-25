# Symfony Palette Bundle
Symfony palette bundle je rozšíření pro Symfony Framework, které umožňuje jednoduchuché i pokročilé úpravy obrazových souborů včetně inteligentního generování miniatur a náhledů.

Palette u obrázků například umožňuje: změny rozměrů, vkládání vodoznaků, pokročilé transformace, nastavení průhlednosti a množství dalších efektních filtrů a funkcí.

## Instalace a integrace do Symfony
#### 1. Nejdříve Palette naistalujeme do projektu nejlépe pomocí [composeru](https://getcomposer.org/).

    php composer.phar require pavlista/symfony-palette-bundle

#### 2. Po té v Symfony do bundles.php zaregistrujeme bundle.

    SymfonyPaletteBundle\PaletteBundle::class => ['all' => true],

#### 3. Do konfigurace také přidáme sekci s nastavením rozšíření a správně ji vyplníme.

    palette:
      path: '%kernel.root_dir%/../public/files/thumb'
      url: 'http://website.com/files/thumb/'
      basePath: '/var/www/wevsute.com/public/files/'
      signingKey: '%uniqueSigningKey%'

- **path:** Je relativní nebo absolutní cesta ke složce do které se mají vygenerované miniatury a obrázky ukládat. Tato složka musí existovat a musí být do ní možné zapisovat!
- **url:** Absolutní url adresa s lomítkem na konci na které je složka s miniatury veřejně dostupná.
- **basepath:** Absolutní cesta k document rootu webu.
- **signingKey:** Náhodný klíč pro podepisování imageQuery


## Použití v Symfony
V Symfony je služba palette dostupná pod názvem **@palette.palette**.

V Twigu lze generovat miniatury a různé verze obrázků pomocí filtru palette na jehož vstupu musí být vždy cesta k souboru obrázku (ne url adresa) a palette query. Např.:

    <img src="{{ image|palette('Resize;100;150&Border;2;2;black') }}" />

Tento kód vygeneruje z obrázku miniaturu obrázku o rozměrech 100 x 150px s 2px černým rámečkem okolo.
Seznam všech možných filtrů a effektů včetně používání samotného Palette naleznete na [Githubu Palette](https://github.com/MichaelPavlista/palette)

## Důležité odkazy
- [Github Palette](https://github.com/MichaelPavlista/palette)
- [Github Nette Palette](https://github.com/MichaelPavlista/nette-palette)
- [Dokumentace Palette a jejích filtrů](http://palette.pavlista.cz/)

