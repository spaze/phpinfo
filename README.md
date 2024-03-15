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

## Sanitization
By default, session id (as returned by `session_id()`) will be sanitized and replaced by `[***]` in the output.
This is to prevent some session hijacking attacks that would read the session id from the cookie value reflected in the `phpinfo()` output.
You can disable that by calling `doNotSanitizeSessionId()` but it's totally not recommended. Do not disable that. Please.

You can add own strings to be sanitized in the output with
```php
addSanitization(string $sanitize, ?string $with = null): self
```
If found, the string in `$sanitize` will be replaced with the string `$with`, if `$with` is null then the default `[***]` will be used instead.

Some of the values in `phpinfo()` output are printed URL-encoded, so the `$sanitize` value will also be searched URL-encoded automatically.
This means that both `foo,bar` and `foo%2Cbar` would be replaced.
