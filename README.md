# phpinfo
Extract phpinfo() into a variable and move CSS to external file

```php
$phpInfo = new PhpInfo();
$html = $phpInfo->getHtml();
```

`$html` will contain `phpinfo()` output, wrapped in `<div id="phpinfo">` & `</div>`.

All inline CSS will be "externalized" to CSS classes, you can load `assets/info.css` to get the colors back.

An example usage with Nette Framework:
```php
$this->template->phpinfo = Html::el()->setHtml($this->phpInfo->getHtml());
```
