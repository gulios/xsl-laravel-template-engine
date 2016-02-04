## XSLT/XML template engine for laravel 5.
This is modified version of https://github.com/krowinski/laravel-xslt

### Changes:
- added default preferences to XML(paths, language info) depends on config data
- changed method names
- validate input values(htmlentities)
- validate loaded xml(html_entity_decode)
- removed forms
- removed breadcrumbs

### Installation:

##### Add to composer.json
```bash
"gulios/xsl-laravel-template-engine": "dev-master"
```
###### for debug XML install:
```
"barryvdh/laravel-debugbar": "2.1.1",
```

##### Add this line to config/app.php 'providers' array
```
'Gulios\LaravelXSLT\XSLTServiceProvider',
```

### Example Usage:

##### Create main.xsl in resources/views
```

<?xml version="1.0" encoding="utf-8"?>

<!DOCTYPE stylesheet [
        <!ENTITY nbsp  "&#160;" ><!-- space -->
        <!ENTITY copy  "&#169;" ><!-- copyright -->
        ]>
        
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:exslt="http://exslt.org/common" xmlns:str="http://exslt.org/strings" xmlns:php="http://php.net/xsl" exclude-result-prefixes="exslt str php">
                
    <xsl:output encoding="UTF-8" method="html" omit-xml-declaration="yes" indent="yes"
                doctype-system="about:legacy-compat" cdata-section-elements="script"/>

<xsl:variable name="curretLanguage"><xsl:value-of select="/App/Preferences/language/@current"/></xsl:variable>
   
    <xsl:template match="/">
        
        <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{$curretLanguage}" lang="{$curretLanguage}">
            
            <head>
            
            </head>
            
            <body>
            
                <xsl:apply-templates select="/App/Controller"/>
                
            </body>
        </html>
    </xsl:template>
    
</xsl:stylesheet>

```

##### Create index.xsl in resources/views
```

<?xml version="1.0" encoding="utf-8"?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:exslt="http://exslt.org/common" xmlns:str="http://exslt.org/strings" xmlns:php="http://php.net/xsl" exclude-result-prefixes="exslt str php">

    <xsl:import href="main.xsl" />

    <xsl:template match="*">

        <h2><xsl:value-of select="{$curretLanguage}"/></h2>

    </xsl:template>

</xsl:stylesheet>

```

##### Add to app/Http/routes.php
```
Route::get('/', ['as' => 'index', 'uses' => 'SomeController@index']);
```



##### Write some test controller AlwaysProcessController.php in app/Http/Controllers
```

class AlwaysProcessController extends Controller
{
    protected $xml;

    public function __construct()
    {
        $this->xml = \View::addChild('Controller');
        $this->xml->addAttribute('class', Route::currentRouteAction());
        $this->xml->addAttribute('function', Route::currentRouteName());
    }
}

```

after that create SomeController in app/Http/Controllers:

```
namespace App\Http\Controllers;
use App\Http\Requests;

class SomeController extends AlwaysProcessController
{
    public function index()
    {
        //$this->xml->addChild('SomeTagName', 'SomeValue');
        
        //$this->xml->addData($ibanezImages, $someArrayData);
        
        return view('index');
    }
}
```


##### Optional
You can add to config/app.php
<code>
'available_languages' => array('en','pl'),
'default_language' => 'en',
</code>
then you will get these data to XML by default.


#### Example XML
```
<?xml version="1.0"?>
<App>
  <Controller class="App\Http\Controllers\SomeController@index" function="index">
    <SomeTagName>SomeValue</SomeTagName>
  </Controller>
  <Preferences>
    <url isHttps="" currentUrl="http://www.domain/" baseUrl="http://www.domain" previousUrl="http://www.domain/"/>
    <server curretnYear="2016" curretnMonth="02" curretnDay="04" currentDateTime="2016-02-04 14:21:56"/>
    <language current="en" default="en">
      <item>en</item>
      <item>pl</item>
    </language>
  </Preferences>
</App>

```