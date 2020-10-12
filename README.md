# phpinfo
Extract `phpinfo()` into a variable and move CSS to external file.

This might be handy when you want to show the output from `phpinfo()` to authenticated users only in your site's layout for example.

```php
$phpInfo = new PhpInfo();
$html = $phpInfo->getHtml();
```

`$html` will contain `phpinfo()` output, wrapped in `<div id="phpinfo">` & `</div>`.

All inline CSS will be "externalized" to CSS classes, you can load `assets/info.css` to get the colors back.

An example usage with Nette Framework (can be used with other frameworks or standalone, too):
```php
$this->template->phpinfo = Html::el()->setHtml($this->phpInfo->getHtml());
```
